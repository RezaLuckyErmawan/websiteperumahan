<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\PembatalanModel;
use App\Models\PembayaranRumahModel;
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
            WHERE LOWER(status_pembelian) != 'batal'
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
            perumahan.kode_rumah,
            COALESCE(total_bayar.total_bayar, 0) AS total_bayar,
            (pembelian_rumah.harga_beli - COALESCE(total_bayar.total_bayar, 0)) AS sisa_bayar
        ');
        $builder->join('customer', 'customer.id = pembelian_rumah.customer_id');
        $builder->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id');
        $builder->join(
            '(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah GROUP BY pembelian_rumah_id) total_bayar',
            'total_bayar.pembelian_rumah_id = pembelian_rumah.id',
            'left'
        );

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
    $db = \Config\Database::connect();
    $model = new PembelianRumahModel();
    $perumahanModel = new PerumahanModel();
    $allowedStatus = ['Lunas', 'Cicil', 'DP', 'Batal'];
    $allowedMetode = ['Cash', 'KPR', 'Cicilan Internal'];
    $allowedDokumen = ['Lengkap', 'Pending', 'Verifikasi'];

    $statusPembelian = $request->getPost('status_pembelian');
    $metodePembayaran = $request->getPost('metode_pembayaran');
    $statusDokumen = $request->getPost('status_dokumen');

    if (
        !$request->getPost('customer_id') ||
        !$request->getPost('perumahan_id') ||
        !$request->getPost('tanggal_pembelian') ||
        !in_array($statusPembelian, $allowedStatus, true) ||
        !in_array($metodePembayaran, $allowedMetode, true) ||
        !in_array($statusDokumen, $allowedDokumen, true)
    ) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Data pembelian rumah belum lengkap atau tidak valid']);
    }

    // Ambil data rumah berdasarkan ID
    $perumahan = $perumahanModel->find($request->getPost('perumahan_id'));

    if (!$perumahan) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data rumah tidak ditemukan'
        ]);
    }

    if (strtolower((string) $perumahan['status']) === 'terjual') {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Rumah ini sudah terjual']);
    }

    $data = [
        'customer_id'       => $request->getPost('customer_id'),
        'perumahan_id'      => $request->getPost('perumahan_id'),
        'tanggal_pembelian' => $request->getPost('tanggal_pembelian'),
        'harga_beli'        => $perumahan['harga'], // AMAN
        'status_pembelian'  => $statusPembelian,
        'metode_pembayaran' => $metodePembayaran,
        'status_dokumen'    => $statusDokumen,
        'request_khusus'    => $request->getPost('request_khusus'),
        'catatan_marketing' => $request->getPost('catatan_marketing'),
    ];

    $db->transStart();
    $model->insert($data);

    // Update status rumah
    if (strtolower($statusPembelian) === 'batal') {
        $perumahanModel->update($data['perumahan_id'], ['status' => 'Dijual']);
    } else {
        $perumahanModel->update($data['perumahan_id'], ['status' => 'Terjual']);
    }
    $db->transComplete();

    if ($db->transStatus() === false) {
        return $this->response->setStatusCode(500)
            ->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan pembelian rumah']);
    }

    return $this->response->setJSON(['status' => 'success']);
}


    public function edit($id)
{
    $model = new PembelianRumahModel();
    $data = $model->select('
            pembelian_rumah.*,
            customer.nama as nama_customer,
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
        )
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
    $db = \Config\Database::connect();
    $model = new PembelianRumahModel();
    $pembatalanModel = new PembatalanModel();
    $perumahanModel = new PerumahanModel();
    $allowedStatus = ['Lunas', 'Cicil', 'DP', 'Batal'];
    $allowedMetode = ['Cash', 'KPR', 'Cicilan Internal'];
    $allowedDokumen = ['Lengkap', 'Pending', 'Verifikasi'];

    $dataLama = $model->find($id);
    if (!$dataLama) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ]);
    }

    // Ambil input alasan pembatalan
    $alasanPembatalan = trim($request->getPost('alasan_pembatalan'));
    $statusPembelian = $alasanPembatalan ? 'Batal' : $request->getPost('status_pembelian');
    $metodePembayaran = $request->getPost('metode_pembayaran');
    $statusDokumen = $request->getPost('status_dokumen');

    if (
        !$request->getPost('customer_id') ||
        !$request->getPost('perumahan_id') ||
        !$request->getPost('tanggal_pembelian') ||
        !in_array($statusPembelian, $allowedStatus, true) ||
        !in_array($metodePembayaran, $allowedMetode, true) ||
        !in_array($statusDokumen, $allowedDokumen, true)
    ) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Data pembelian rumah belum lengkap atau tidak valid']);
    }

    // Data baru default
    $dataBaru = [
        'customer_id'       => $request->getPost('customer_id'),
        'perumahan_id'      => $request->getPost('perumahan_id'),
        'tanggal_pembelian' => $request->getPost('tanggal_pembelian'),
        'harga_beli'        => $request->getPost('harga_beli'),
        'status_pembelian'  => $statusPembelian,
        'metode_pembayaran' => $metodePembayaran,
        'status_dokumen'    => $statusDokumen,
        'request_khusus'    => $request->getPost('request_khusus'),
        'catatan_marketing' => $request->getPost('catatan_marketing'),
    ];

    $rumahBaru = $perumahanModel->find($dataBaru['perumahan_id']);
    if (!$rumahBaru) {
        return $this->response->setStatusCode(404)
            ->setJSON(['status' => 'error', 'message' => 'Data rumah tidak ditemukan']);
    }

    if (
        (int) $dataBaru['perumahan_id'] !== (int) $dataLama['perumahan_id'] &&
        strtolower((string) $rumahBaru['status']) === 'terjual'
    ) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Rumah pengganti sudah terjual']);
    }

    $db->transStart();

    if (!empty($alasanPembatalan)) {
        // Insert ke pembatalan_transaksi
        $pembatalanModel->insert([
            'pembelian_id'         => $id,
            'perumahan_id'         => $dataBaru['perumahan_id'],
            'customer_id'          => $dataBaru['customer_id'],
            'keterangan_pembatalan'=> $alasanPembatalan
        ]);

    }

    $model->update($id, $dataBaru);

    if ((int) $dataBaru['perumahan_id'] !== (int) $dataLama['perumahan_id']) {
        $perumahanModel->update($dataLama['perumahan_id'], ['status' => 'Dijual']);
    }

    if (strtolower($dataBaru['status_pembelian']) === 'batal') {
        $perumahanModel->update($dataBaru['perumahan_id'], ['status' => 'Dijual']);
    } else {
        $perumahanModel->update($dataBaru['perumahan_id'], ['status' => 'Terjual']);
    }

    $db->transComplete();

    if ($db->transStatus() === false) {
        return $this->response->setStatusCode(500)
            ->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui pembelian rumah']);
    }

    return $this->response->setJSON(['status' => 'success']);
}


    public function delete($id)
{
    $model = new PembelianRumahModel();
    $perumahanModel = new PerumahanModel();
    $pembayaranModel = new PembayaranRumahModel();
    $db = \Config\Database::connect();

    try {
        $data = $model->find($id);
        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        $db->transStart();
        $buktiPembayaran = $pembayaranModel
            ->where('pembelian_rumah_id', $id)
            ->where('bukti_bayar IS NOT NULL', null, false)
            ->findAll();

        $deleted = $model->delete($id);
        if ($deleted) {
            $perumahanModel->update($data['perumahan_id'], ['status' => 'Dijual']);
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Data gagal dihapus.']);
        }

        if ($deleted) {
            foreach ($buktiPembayaran as $bukti) {
                $path = $bukti['bukti_bayar'] ?? null;
                if ($path) {
                    $fullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
                    if (is_file($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

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
