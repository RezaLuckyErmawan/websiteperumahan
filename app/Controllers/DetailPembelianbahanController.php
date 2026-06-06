<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BahanBangunanModel;
use App\Models\DetailPembelianModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PembelianBahanModel;


class DetailPembelianbahanController extends BaseController
{
    public function detailpembelian()
{
    $model = new DetailPembelianModel();
    $bahanModel = new BahanBangunanModel();
    $pembelianModel = new PembelianBahanModel();

    $data['detailpembelian'] = $model
        ->select('detail_pembelian_bahan.*, bahan_bangunan.nama_bahan, pembelian_bahan.nomor_nota')
        ->join('bahan_bangunan', 'bahan_bangunan.id = detail_pembelian_bahan.bahan_bangunan_id')
        ->join('pembelian_bahan', 'pembelian_bahan.id = detail_pembelian_bahan.pembelian_id')
        ->findAll();

    // Kirimkan data pembelian dan bahan ke view
    $data['pembelian'] = $pembelianModel->findAll();
    $data['bahan'] = $bahanModel->findAll();

    return view('page/detailpembelianbahan/detail_pembelian_bahan', $data);
}

    public function json() {
    $request = service('request');
    $model = new DetailPembelianModel();

    $searchValue = $request->getGet('search')['value'] ?? '';
    $start       = $request->getGet('start') ?? 0;
    $length      = $request->getGet('length') ?? 10;

    // Builder pakai join
    $builder = $model
        ->select('detail_pembelian_bahan.*, bahan_bangunan.nama_bahan, pembelian_bahan.nomor_nota')
        ->join('bahan_bangunan', 'bahan_bangunan.id = detail_pembelian_bahan.bahan_bangunan_id', 'left')
        ->join('pembelian_bahan', 'pembelian_bahan.id = detail_pembelian_bahan.pembelian_id', 'left');

    // Hitung total data SEBELUM filter pencarian
    $totalRecords = $builder->countAllResults(false);  // false agar tidak reset query builder

    // Filter pencarian
    if (!empty($searchValue)) {
        $builder->groupStart()
            ->like('nomor_nota', $searchValue)
            ->orLike('nama_bahan', $searchValue)
            ->orLike('jumlah', $searchValue)
            ->groupEnd();
    }

    $filteredRecords = $builder->countAllResults(false);  // false biar gak reset lagi

    // Ambil data
    $data = $builder->orderBy('detail_pembelian_bahan.created_at', 'DESC')
                    ->findAll($length, $start);

    return $this->response->setJSON([
        'draw' => (int) $request->getGet('draw'),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data
    ]);
}



    public function create()
    {
        $pembelianModel = new PembelianBahanModel();
        $bahanModel     = new BahanBangunanModel();

        $data = [
            'pembelian' => $pembelianModel->findAll(),
            'bahan'     => $bahanModel->findAll()
        ];
        return view('page/detailpembelianbahan/create', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $detailModel = new DetailPembelianModel();
        $bahanModel  = new BahanBangunanModel();

        $pembelian_id      = $this->request->getPost('pembelian_id');
        $bahan_bangunan_id = $this->request->getPost('bahan_bangunan_id');
        $jumlah            = (int) $this->request->getPost('jumlah');
        $harga_satuan      = (float) $this->request->getPost('harga_satuan');
        $subtotal          = $jumlah * $harga_satuan;

        if (!$pembelian_id || !$bahan_bangunan_id || $jumlah <= 0 || $harga_satuan < 0) {
            return redirect()->to('/detail-pembelian-bahan')->with('error', 'Data detail pembelian belum lengkap atau tidak valid.');
        }

        $bahan = $bahanModel->find($bahan_bangunan_id);
        if (!$bahan) {
            return redirect()->to('/detail-pembelian-bahan')->with('error', 'Bahan bangunan tidak ditemukan.');
        }

        $db->transStart();
        $detailModel->insert([
            'pembelian_id'      => $pembelian_id,
            'bahan_bangunan_id' => $bahan_bangunan_id,
            'jumlah'            => $jumlah,
            'harga_satuan'      => $harga_satuan,
            'subtotal'          => $subtotal,
        ]);

        $stok_baru = $bahan['stok'] + $jumlah;

        $bahanModel->update($bahan_bangunan_id, [
            'stok' => $stok_baru
        ]);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/detail-pembelian-bahan')->with('error', 'Gagal menyimpan detail pembelian.');
        }

        return redirect()->to('/detail-pembelian-bahan')->with('success', 'Detail pembelian berhasil disimpan dan stok diperbarui.');
    }

  public function edit($id)
{
    $detailModel = new DetailPembelianModel();
    $detail = $detailModel->find($id);

    if ($this->request->isAJAX()) {
        return $this->response->setJSON(['detail' => $detail]);
    }

    // fallback untuk non-AJAX
    $bahanModel = new BahanBangunanModel();
    $pembelianModel = new PembelianBahanModel();
    return view('page/detailpembelianbahan/edit', [
        'detail' => $detail,
        'bahan' => $bahanModel->findAll(),
        'pembelian' => $pembelianModel->findAll()
    ]);
}


    public function update($id)
    {
        $db = \Config\Database::connect();
        $detailModel = new DetailPembelianModel();
        $bahanModel  = new BahanBangunanModel();

        // Ambil data lama detail pembelian
        $detailLama = $detailModel->find($id);
 
        if (!$detailLama) {
        return redirect()->to('/detail-pembelian-bahan')->with('error', 'Data tidak ditemukan.');
        }
    // Ambil input baru dari form
        $pembelian_id      = $this->request->getPost('pembelian_id');
        $bahan_bangunan_id = $this->request->getPost('bahan_bangunan_id');
        $jumlah_baru       = (int) $this->request->getPost('jumlah');
        $harga_satuan      = (float) $this->request->getPost('harga_satuan');
        $subtotal          = $jumlah_baru * $harga_satuan;

        if (!$pembelian_id || !$bahan_bangunan_id || $jumlah_baru <= 0 || $harga_satuan < 0) {
            return redirect()->to('/detail-pembelian-bahan')->with('error', 'Data detail pembelian belum lengkap atau tidak valid.');
        }

        // Cek bahan bangunan terkait
        $bahan = $bahanModel->find($bahan_bangunan_id);
        if (!$bahan) {
            return redirect()->to('/detail-pembelian-bahan')->with('error', 'Bahan bangunan tidak ditemukan.');
        }

        $db->transStart();

        // Hitung perubahan stok hanya jika bahan bangunan tidak diubah
        if ($bahan_bangunan_id == $detailLama['bahan_bangunan_id']) {
            $selisih = $jumlah_baru - $detailLama['jumlah'];
            $stok_baru = $bahan['stok'] + $selisih;
            $bahanModel->update($bahan_bangunan_id, ['stok' => $stok_baru]);
        } else {
            // Jika bahan bangunan diganti, rollback stok lama dan tambahkan ke yang baru
            $bahanLama = $bahanModel->find($detailLama['bahan_bangunan_id']);
            if ($bahanLama) {
                $bahanModel->update($bahanLama['id'], ['stok' => $bahanLama['stok'] - $detailLama['jumlah']]);
            }
            $bahanModel->update($bahan_bangunan_id, ['stok' => $bahan['stok'] + $jumlah_baru]);
        }
      
        $detailModel->update($id, [
            'pembelian_id'      => $pembelian_id,
            'bahan_bangunan_id' => $bahan_bangunan_id,
            'jumlah'            => $jumlah_baru,
            'harga_satuan'      => $harga_satuan,
            'subtotal'          => $subtotal,
        ]);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/detail-pembelian-bahan')->with('error', 'Gagal memperbarui detail pembelian.');
        }

    return redirect()->to('/detail-pembelian-bahan')->with('success', 'Data berhasil diperbarui dan stok disesuaikan.');
    }

    public function delete($id)
{
    $db = \Config\Database::connect();
    $detailModel = new \App\Models\DetailPembelianModel();
    $bahanModel  = new \App\Models\BahanBangunanModel();

    // Cari record detail pembelian yang akan dihapus
    $detail = $detailModel->find($id);
    if (!$detail) {
        return redirect()->to('/detail-pembelian-bahan')->with('error', 'Detail pembelian tidak ditemukan.');
    }

    // Ambil stok sekarang dari bahan bangunan terkait
    $bahan = $bahanModel->find($detail['bahan_bangunan_id']);

    $db->transStart();

    if ($bahan) {
        // Kurangi stok sesuai jumlah yang pernah ditambahkan
        $stokBaru = (int)$bahan['stok'] - (int)$detail['jumlah'];

        // Pastikan stok tidak negatif
        if ($stokBaru < 0) {
            $stokBaru = 0;
        }

        $bahanModel->update($bahan['id'], [
            'stok'       => $stokBaru,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // Hapus record detail pembelian
    $detailModel->delete($id);
    $db->transComplete();

    if ($db->transStatus() === false) {
        return redirect()->to('/detail-pembelian-bahan')
                         ->with('error', 'Gagal menghapus detail pembelian.');
    }

    return redirect()->to('/detail-pembelian-bahan')
                     ->with('success', 'Detail pembelian dihapus dan stok bahan disesuaikan.');
}


}
