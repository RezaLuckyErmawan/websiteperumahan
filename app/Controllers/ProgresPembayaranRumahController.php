<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ProgresPembayaranRumahController extends BaseController
{
    public function index()
    {
        return view('page/pembayaranrumah/progres_pembayaran_rumah', [
            'userRole' => session()->get('role'),
        ]);
    }

    public function json()
    {
        $request = service('request');
        $db = \Config\Database::connect();

        $builder = $db->table('pembelian_rumah')
            ->select('
                pembelian_rumah.*,
                customer.nama AS nama_customer,
                perumahan.kode_rumah,
                COALESCE(total_bayar.total_bayar, 0) AS total_bayar,
                (pembelian_rumah.harga_beli - COALESCE(total_bayar.total_bayar, 0)) AS sisa_bayar
            ')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->join(
                '(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah GROUP BY pembelian_rumah_id) total_bayar',
                'total_bayar.pembelian_rumah_id = pembelian_rumah.id',
                'left'
            );

        if ($this->isCustomer()) {
            $this->applyCustomerScope($builder, 'customer');
        }

        $searchValue = $request->getGet('search')['value'] ?? '';
        if ($searchValue) {
            $builder->groupStart()
                ->like('customer.nama', $searchValue)
                ->orLike('perumahan.kode_rumah', $searchValue)
                ->orLike('pembelian_rumah.status_pembelian', $searchValue)
                ->orLike('pembelian_rumah.metode_pembayaran', $searchValue)
                ->groupEnd();
        }

        $totalBuilder = $db->table('pembelian_rumah')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id');
        if ($this->isCustomer()) {
            $this->applyCustomerScope($totalBuilder, 'customer');
        }

        $total = $totalBuilder->countAllResults();
        $filtered = $builder->countAllResults(false);
        $start = (int) ($request->getGet('start') ?? 0);
        $length = (int) ($request->getGet('length') ?? 10);

        $data = $builder
            ->orderBy('pembelian_rumah.created_at', 'DESC')
            ->get($length, $start)
            ->getResultArray();

        return $this->response->setJSON([
            'draw' => (int) $request->getGet('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    private function isCustomer(): bool
    {
        return session()->get('role') === 'customer';
    }

    private function applyCustomerScope($builder, string $customerAlias)
    {
        $customerId = session()->get('customer_id');
        if ($customerId) {
            return $builder->where($customerAlias . '.id', $customerId);
        }

        $name = (string) session()->get('nama');
        $username = (string) session()->get('username');

        return $builder->groupStart()
            ->where($customerAlias . '.nama', $name)
            ->orWhere($customerAlias . '.nama', $username)
            ->groupEnd();
    }
}
