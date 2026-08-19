<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BahanBangunanModel;
use App\Models\DetailPembelianModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PembelianBahanModel;


class DetailPembelianbahanController extends BaseController
{
    private function detailPembelianQuery()
    {
        return (new DetailPembelianModel())
            ->select('
                detail_pembelian_bahan.*,
                bahan_bangunan.nama_bahan,
                pembelian_bahan.nomor_nota
            ')
            ->join('bahan_bangunan', 'bahan_bangunan.id = detail_pembelian_bahan.bahan_bangunan_id', 'left')
            ->join('pembelian_bahan', 'pembelian_bahan.id = detail_pembelian_bahan.pembelian_id', 'left');
    }

    public function detailpembelian()
    {
        $bahanModel = new BahanBangunanModel();
        $pembelianModel = new PembelianBahanModel();

        $data['detailpembelian'] = $this->detailPembelianQuery()
            ->orderBy('detail_pembelian_bahan.created_at', 'DESC')
            ->findAll();

        $data['pembelian'] = $pembelianModel->orderBy('created_at', 'DESC')->findAll();
        $data['bahan'] = $bahanModel->orderBy('created_at', 'DESC')->findAll();

        return view('page/detailpembelianbahan/detail_pembelian_bahan', $data);
    }

    public function json() {
        $request = service('request');
        $builder = $this->detailPembelianQuery();

        $search = $request->getGet('search');
        $searchValue = is_array($search) ? trim((string) ($search['value'] ?? '')) : '';
        $start = max(0, (int) ($request->getGet('start') ?? 0));
        $length = max(1, (int) ($request->getGet('length') ?? 10));

        $totalRecords = (clone $builder)->countAllResults();

        if ($searchValue !== '') {
            $builder->groupStart()
                ->like('pembelian_bahan.nomor_nota', $searchValue)
                ->orLike('bahan_bangunan.nama_bahan', $searchValue)
                ->orLike('detail_pembelian_bahan.jumlah', $searchValue)
                ->orLike('detail_pembelian_bahan.harga_satuan', $searchValue)
                ->orLike('detail_pembelian_bahan.subtotal', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = (clone $builder)->countAllResults();

        $data = $builder
            ->orderBy('detail_pembelian_bahan.created_at', 'DESC')
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
        return redirect()->to('/detail-pembelian-bahan');
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

    return redirect()->to('/detail-pembelian-bahan');
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
