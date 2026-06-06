<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BahanBangunanModel;
use App\Models\BahanPembangunanRumahModel;
use App\Models\PerumahanModel;
use CodeIgniter\HTTP\ResponseInterface;

class BahanPembangunanRumahController extends BaseController
{
   public function bahanpembangunanrumah()
    {
    $model = new BahanPembangunanRumahModel();
    $bahanModel = new BahanBangunanModel();
    $perumahanModel = new PerumahanModel();

    $data['bahan_pembangunan'] = $model
        ->select('bahan_pembangunan_rumah.*, bahan_bangunan.nama_bahan, perumahan.kode_rumah, user.nama as nama_mandor')
        ->join('bahan_bangunan', 'bahan_bangunan.id = bahan_pembangunan_rumah.bahan_bangunan_id')
        ->join('perumahan', 'perumahan.id = bahan_pembangunan_rumah.perumahan_id')
        ->join('user', 'user.id = bahan_pembangunan_rumah.mandor_id')
        ->findAll();
    
    $data['perumahan'] = $perumahanModel->findAll(); // ✅ tambahkan ini
    $data['bahan'] = $bahanModel->findAll(); 
    return view('page/bahanpembangunanrumah/data_bahan_pembangunan', $data);
}

    public function json()
{
    $request = service('request');
    $model = new \App\Models\BahanPembangunanRumahModel();

    $searchValue = $request->getGet('search')['value'] ?? '';
    $start  = $request->getGet('start') ?? 0;
    $length = $request->getGet('length') ?? 10;

    $builder = $model
        ->select('bahan_pembangunan_rumah.*, bahan_bangunan.nama_bahan, perumahan.kode_rumah, user.nama as nama_mandor')
        ->join('bahan_bangunan', 'bahan_bangunan.id = bahan_pembangunan_rumah.bahan_bangunan_id')
        ->join('perumahan', 'perumahan.id = bahan_pembangunan_rumah.perumahan_id')
        ->join('user', 'user.id = bahan_pembangunan_rumah.mandor_id');

    $totalRecords = $builder->countAllResults(false); // false agar builder tidak di-reset

    if (!empty($searchValue)) {
        $builder->groupStart()
            ->like('nama_bahan', $searchValue)
            ->orLike('kode_rumah', $searchValue)
            ->orLike('jumlah_pemakaian', $searchValue)
            ->groupEnd();
    }

    $filteredRecords = $builder->countAllResults(false);

    $data = $builder->orderBy('bahan_pembangunan_rumah.created_at', 'DESC')
                    ->findAll($length, $start);

    return $this->response->setJSON([
        'draw' => (int) $request->getGet('draw'),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data,
    ]);
}


     public function create()
    {
        $perumahanModel = new PerumahanModel();
        $bahanModel     = new BahanBangunanModel();

        $data = [
            'perumahan' => $perumahanModel->findAll(),
            'bahan'     => $bahanModel->findAll(),
        ];

        return view('page/bahanpembangunanrumah/create', $data);
    }

public function store()
{
    $db = \Config\Database::connect();
    $model         = new BahanPembangunanRumahModel();
    $bahanModel    = new BahanBangunanModel();

    $perumahan_id       = $this->request->getPost('perumahan_id');
    $bahan_bangunan_id  = $this->request->getPost('bahan_bangunan_id');
    $jumlah_pemakaian   = (int) $this->request->getPost('jumlah_pemakaian');
    $tanggal_penggunaan = $this->request->getPost('tanggal_penggunaan');
    $keterangan         = $this->request->getPost('keterangan');

    // Ambil mandor_id dari session
    $mandor_id = session()->get('user_id');

    // Cek user role jika ingin memastikan hanya mandor
    if (session()->get('role') !== 'mandor') {
        return redirect()->to('/data-bahan-pembangunan')->with('error', 'Hanya mandor yang bisa menambahkan data ini.');
    }

    if (!$perumahan_id || !$bahan_bangunan_id || $jumlah_pemakaian <= 0 || empty($tanggal_penggunaan)) {
        return redirect()->to('/data-bahan-pembangunan')->with('error', 'Data bahan pembangunan belum lengkap atau tidak valid.');
    }

    $bahan = $bahanModel->find($bahan_bangunan_id);
    if (!$bahan) {
        return redirect()->to('/data-bahan-pembangunan')->with('error', 'Data bahan bangunan tidak ditemukan.');
    }

    if ($bahan['stok'] < $jumlah_pemakaian) {
        return redirect()->to('/data-bahan-pembangunan')->with('error', 'Stok bahan bangunan tidak mencukupi.');
    }

    $db->transStart();
    $model->insert([
        'perumahan_id'       => $perumahan_id,
        'bahan_bangunan_id'  => $bahan_bangunan_id,
        'jumlah_pemakaian'   => $jumlah_pemakaian,
        'tanggal_penggunaan' => $tanggal_penggunaan,
        'keterangan'         => $keterangan,
        'mandor_id'          => $mandor_id, // ← ini yang penting
    ]);

    $bahanModel->update($bahan_bangunan_id, [
        'stok' => $bahan['stok'] - $jumlah_pemakaian
    ]);
    $db->transComplete();

    if ($db->transStatus() === false) {
        return redirect()->to('/data-bahan-pembangunan')->with('error', 'Gagal menyimpan data bahan pembangunan.');
    }

    return redirect()->to('/data-bahan-pembangunan')->with('success', 'Data berhasil disimpan dan stok bahan dikurangi.');
}

    public function edit($id) {
        $model = new BahanPembangunanRumahModel();
        $perumahanmodel = new PerumahanModel();
        $bahanmodel  = new BahanBangunanModel();
        $bahan = $model->find($id);

        if($this->request->isAJAX()) {
            return $this->response->setJson(['bahan' => $bahan]);
        }
        $data = [
            'bahan'      => $model->find($id),
            'perumahan'  => $perumahanmodel->findAll(),
            'bahanList'  => $bahanmodel->findAll(),
    ];

    return view('page/bahanpembangunanrumah/edit', $data);
    }

    public function show($id)
{
    $model = new BahanPembangunanRumahModel();
    $data = $model->find($id);

    if (!$data) {
        return $this->response->setJSON(['error' => 'Data tidak ditemukan'])->setStatusCode(404);
    }

    return $this->response->setJSON($data);
}


    public function update($id)
{
        $db = \Config\Database::connect();
        $model        = new BahanPembangunanRumahModel();
        $bahanModel   = new BahanBangunanModel();

        $dataLama = $model->find($id);
        if (!$dataLama) {
            return redirect()->to('/data-bahan-pembangunan')->with('error', 'Data tidak ditemukan.');
        }

        $perumahan_id       = $this->request->getPost('perumahan_id');
        $bahan_bangunan_id  = $this->request->getPost('bahan_bangunan_id');
        $jumlah_baru        = (int) $this->request->getPost('jumlah_pemakaian');
        $tanggal_penggunaan = $this->request->getPost('tanggal_penggunaan');
        $keterangan         = $this->request->getPost('keterangan');

        if (!$perumahan_id || !$bahan_bangunan_id || $jumlah_baru <= 0 || empty($tanggal_penggunaan)) {
            return redirect()->back()->with('error', 'Data bahan pembangunan belum lengkap atau tidak valid.');
        }

        // Data bahan lama dan baru
        $bahanBaru = $bahanModel->find($bahan_bangunan_id);
        if (!$bahanBaru) {
            return redirect()->back()->with('error', 'Bahan bangunan tidak ditemukan.');
        }

        $db->transStart();

        // Perhitungan stok
        if ($bahan_bangunan_id == $dataLama['bahan_bangunan_id']) {
            // Jika ID bahan sama, hanya sesuaikan jumlah
            $selisih = $jumlah_baru - $dataLama['jumlah_pemakaian'];
            $stok_baru = $bahanBaru['stok'] - $selisih;

            if ($stok_baru < 0) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Stok bahan tidak mencukupi.');
            }

            $bahanModel->update($bahan_bangunan_id, ['stok' => $stok_baru]);
        } else {
            // Jika ID bahan diganti, rollback stok lama dan kurangi dari stok baru
            $bahanLama = $bahanModel->find($dataLama['bahan_bangunan_id']);

            if ($bahanLama) {
                $bahanModel->update($bahanLama['id'], [
                    'stok' => $bahanLama['stok'] + $dataLama['jumlah_pemakaian']
                ]);
            }

            if ($bahanBaru['stok'] < $jumlah_baru) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Stok bahan baru tidak mencukupi.');
            }

            $bahanModel->update($bahan_bangunan_id, [
                'stok' => $bahanBaru['stok'] - $jumlah_baru
            ]);
        }

        // Simpan data update
        $model->update($id, [
            'perumahan_id'       => $perumahan_id,
            'bahan_bangunan_id'  => $bahan_bangunan_id,
            'jumlah_pemakaian'   => $jumlah_baru,
            'tanggal_penggunaan' => $tanggal_penggunaan,
            'keterangan'         => $keterangan,
        ]);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/data-bahan-pembangunan')->with('error', 'Gagal memperbarui data bahan pembangunan.');
        }

    return redirect()->to('/data-bahan-pembangunan')->with('success', 'Data berhasil diperbarui dan stok disesuaikan.');
}


public function delete($id)
{
    $db = \Config\Database::connect();
    $model       = new BahanPembangunanRumahModel();
    $bahanModel  = new BahanBangunanModel();

    $data = $model->find($id);
    if (!$data) {
        return redirect()->to('/data-bahan-pembangunan')->with('error', 'Data tidak ditemukan.');
    }

    $bahan = $bahanModel->find($data['bahan_bangunan_id']);
    $db->transStart();
    if ($bahan) {
        $stokBaru = (int)$bahan['stok'] + (int)$data['jumlah_pemakaian'];

        $bahanModel->update($bahan['id'], [
            'stok' => $stokBaru,
            'updated_at' => date('Y-m-d H:i:s') 
        ]);
    }

    $model->delete($id);
    $db->transComplete();

    if ($db->transStatus() === false) {
        return redirect()->to('/data-bahan-pembangunan')->with('error', 'Gagal menghapus data bahan pembangunan.');
    }

    return redirect()->to('/data-bahan-pembangunan')->with('success', 'Data berhasil dihapus dan stok dikembalikan.');
}

    public function lihatbahan($id) {
        $model = new BahanPembangunanRumahModel();
        $data['rumah'] =(new PerumahanModel())->find($id);
        $data['bahan_pembangunan'] = $model
        ->select('bahan_pembangunan_rumah.*, bahan_bangunan.nama_bahan , user.nama as nama_mandor')
        ->join('bahan_bangunan', 'bahan_bangunan.id = bahan_pembangunan_rumah.bahan_bangunan_id')
        ->join('user', 'user.id = bahan_pembangunan_rumah.mandor_id')
        
        ->where('bahan_pembangunan_rumah.perumahan_id', $id)
        ->findAll();

        return view('page/bahanpembangunanrumah/lihat_bahan', $data);
     }


}
