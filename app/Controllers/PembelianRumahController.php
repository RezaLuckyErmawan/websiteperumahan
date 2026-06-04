<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\PembatalanModel;
use App\Models\PembelianRumahModel;
use App\Models\PerumahanModel;
use CodeIgniter\HTTP\ResponseInterface;

class PembelianRumahController extends BaseController
{
    public function pembelianrumah()
{   
    $modal = new PembelianRumahModel();
    $perumahan = new PerumahanModel();
    $customerModel = new CustomerModel();
    $data['perumahan'] = $perumahan->orderBy('created_at', 'DESC')->findAll();
    $data['customer']  = $customerModel->orderBy('created_at', 'DESC')->findAll();

    // Ambil semua perumahan_id yang sudah terjual
    $data['terjual_ids'] = array_column(
        $modal->select('perumahan_id')->findAll(),
        'perumahan_id'
    );

    return view('page/pembelianrumah/pembelian_rumah', $data);
}


    public function chartPenjualanRumah() {
        $db = \config\Database::connect();
        $query = $db->query("
            SELECT 
            MONTH(tanggal_pembelian) AS bulan,
            COUNT(*) AS total
            FROM pembelian_rumah
            WHERE status_pembelian != 'batal'
            GROUP BY bulan
        ");
        $results = $query->getResultArray();
        $data = array_fill(1, 12, 0);

        foreach ($results as $row) {
            $bulan = (int) $row['bulan'];
            $data[$bulan] = (int) $row['total'];
        }

        return $this->response->setJSON(array_values($data));

    }

    public function json() {

        $request = service('request');
        $db = \Config\Database::connect();
        $builder = $db->table('pembelian_rumah');
        
        $builder->select('
            pembelian_rumah.*, 
            customer.nama AS customer_nama, 
            perumahan.kode_rumah
        ');
        $builder->join('customer', 'customer.id = pembelian_rumah.customer_id');
        $builder->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id');

        $searchValue = $request->getGet('search')['value'] ?? '';
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $draw = (int) $request->getGet('draw');


        $totalRecords = $builder->countAllResults(false);

        if ($searchValue) {
            $builder->groupStart()
                ->like('customer.nama', $searchValue)
                ->orLike('perumahan.kode_rumah', $searchValue)
                ->orLike('pembelian_rumah.status_pembelian', $searchValue)
                ->orLike('pembelian_rumah.metode_pembayaran', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false); 

        
        $builder->limit($length, $start);
        $builder->orderBy('pembelian_rumah.created_at', 'DESC');

        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function store()
{
    $request = service('request');
    $model = new PembelianRumahModel();
    $perumahanModel = new PerumahanModel();

    // Ambil data rumah berdasarkan ID
    $perumahan = $perumahanModel->find($request->getPost('perumahan_id'));

    if (!$perumahan) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data rumah tidak ditemukan'
        ]);
    }

    $data = [
        'customer_id'       => $request->getPost('customer_id'),
        'perumahan_id'      => $request->getPost('perumahan_id'),
        'tanggal_pembelian' => $request->getPost('tanggal_pembelian'),
        'harga_beli'        => $perumahan['harga'], // AMAN
        'status_pembelian'  => $request->getPost('status_pembelian'),
        'metode_pembayaran' => $request->getPost('metode_pembayaran'),
        'status_dokumen'    => $request->getPost('status_dokumen'),
        'request_khusus'    => $request->getPost('request_khusus'),
        'catatan_marketing' => $request->getPost('catatan_marketing'),
    ];

    $model->insert($data);

    // Update status rumah
    $perumahanModel->update($data['perumahan_id'], ['status' => 'terjual']);

    return $this->response->setJSON(['status' => 'success']);
}


    public function edit($id)
{
    $model = new PembelianRumahModel();
    $data = $model->select('pembelian_rumah.*, customer.nama as nama_customer, perumahan.kode_rumah')
        ->join('customer', 'customer.id = pembelian_rumah.customer_id')
        ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
        ->find($id);
     
    if ($data) {
        return $this->response->setJSON([
            'status' => true,
            'data' => $data
        ]);
    } else {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Data tidak ditemukan'
        ]);
    }
}


    public function update($id) {
    $request = service('request');
    $model = new PembelianRumahModel();
    $pembatalanModel = new PembatalanModel();
    $perumahanModel = new PerumahanModel();

    $dataLama = $model->find($id);
    if (!$dataLama) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ]);
    }

    // Ambil input alasan pembatalan
    $alasanPembatalan = trim($request->getPost('alasan_pembatalan'));

    // Data baru default
    $dataBaru = [
        'customer_id'       => $request->getPost('customer_id'),
        'perumahan_id'      => $request->getPost('perumahan_id'),
        'tanggal_pembelian' => $request->getPost('tanggal_pembelian'),
        'harga_beli'        => $request->getPost('harga_beli'),
        'status_pembelian'  => $request->getPost('status_pembelian'),
        'metode_pembayaran' => $request->getPost('metode_pembayaran'),
        'status_dokumen'    => $request->getPost('status_dokumen'),
        'request_khusus'    => $request->getPost('request_khusus'),
        'catatan_marketing' => $request->getPost('catatan_marketing'),
    ];

    if (!empty($alasanPembatalan)) {
        $dataBaru['status_pembelian'] = 'batal';

        // Insert ke pembatalan_transaksi
        $pembatalanModel->insert([
            'pembelian_id'         => $id,
            'perumahan_id'         => $dataBaru['perumahan_id'],
            'customer_id'          => $dataBaru['customer_id'],
            'keterangan_pembatalan'=> $alasanPembatalan
        ]);

        // Update status perumahan jadi tersedia lagi
        $perumahanModel->update($dataBaru['perumahan_id'], ['status' => 'dijual']);
    }

    $model->update($id, $dataBaru);

    return $this->response->setJSON(['status' => 'success']);
}


    public function delete($id)
{
    $model = new PembelianRumahModel();

    try {
        $deleted = $model->delete($id);
        if ($deleted) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data gagal dihapus.'
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

}
