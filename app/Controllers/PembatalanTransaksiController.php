<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PembatalanTransaksiController extends BaseController
{
    public function pembatalan()
    {
        return view('page/pembatalan/pembatalan_transaksi', [
            'pageTitle' => 'Pembatalan Transaksi',
            'useDataTables' => true,
        ]);
    }

    public function json()
    {
        $request = service('request');
        $db = db_connect();

        $search = $request->getGet('search');
        $searchValue = is_array($search) ? trim((string) ($search['value'] ?? '')) : '';
        $start = max(0, (int) ($request->getGet('start') ?? 0));
        $length = (int) ($request->getGet('length') ?? 10);
        $length = $length > 0 ? $length : 10;

        $baseBuilder = $db->table('pembatalan_transaksi');
        $totalRecords = (clone $baseBuilder)->countAllResults();

        $builder = $db->table('pembatalan_transaksi pt')
            ->select('
                pt.*,
                perumahan.kode_rumah,
                customer.nama,
                pembelian_rumah.harga_beli,
                pembelian_rumah.tanggal_pembelian
            ')
            ->join('perumahan', 'perumahan.id = pt.perumahan_id')
            ->join('customer', 'customer.id = pt.customer_id')
            ->join('pembelian_rumah', 'pembelian_rumah.id = pt.pembelian_id');

        if ($searchValue !== '') {
            $builder->groupStart()
                ->like('perumahan.kode_rumah', $searchValue)
                ->orLike('customer.nama', $searchValue)
                ->orLike('pembelian_rumah.harga_beli', $searchValue)
                ->orLike('pt.keterangan_pembatalan', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = (clone $builder)->countAllResults();
        $data = $builder
            ->orderBy('pt.created_at', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'draw' => (int) $request->getGet('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

}
