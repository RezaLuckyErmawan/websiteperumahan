<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerumahanModel;
use App\Models\RabRumahModel;
use App\Models\RealisasiRumahModel;
use App\Models\RealisasiPekerjaModel;

class RealisasiPekerjaController extends BaseController
{
    public function realisasipekerja() {
    $realisasiPekerja   = new RealisasiPekerjaModel();
    $realisasiRumahModel = new RealisasiRumahModel();

    $realisasiRumah = $realisasiRumahModel
        ->select('realisasi_rumah.id as realisasi_id, perumahan.kode_rumah, rab_rumah.kode_rab')
        ->join('perumahan', 'perumahan.id = realisasi_rumah.perumahan_id')
        ->join('rab_rumah', 'rab_rumah.perumahan_id = perumahan.id', 'left')
        ->findAll();

    $usedIds = $realisasiPekerja->select('realisasi_id')->findColumn('realisasi_id') ?? [];

    return view('page/realisasi/realisasi_pekerja', [
        'realisasiRumah' => $realisasiRumah,
        'usedIds'        => $usedIds,
    ]);
}


    public function json() {
    $request = service('request');
    $model   = new RealisasiPekerjaModel();

    $searchValue = $request->getGet('search')['value'] ?? null;
    $start  = (int) ($request->getGet('start') ?? 0);
    $length = (int) ($request->getGet('length') ?? 10);

    $builder = $model->db->table('realisasi_pekerja')
        ->select('
            realisasi_pekerja.*,
            realisasi_rumah.tanggal_mulai,
            realisasi_rumah.tanggal_selesai,
            perumahan.kode_rumah
        ')
        ->join('realisasi_rumah', 'realisasi_rumah.id = realisasi_pekerja.realisasi_id')
        ->join('perumahan', 'perumahan.id = realisasi_rumah.perumahan_id');

    if ($searchValue) {
        $builder->groupStart()
            ->like('perumahan.kode_rumah', $searchValue)
            ->orLike('realisasi_pekerja.nama_kontraktor', $searchValue)
            ->orLike('realisasi_pekerja.biaya_kontraktor', $searchValue)
            ->orLike('realisasi_pekerja.jumlah_hari', $searchValue)
            ->groupEnd();
    }

    $total    = $model->countAll();
    $filtered = $builder->countAllResults(false); 

    $data = $builder->orderBy('realisasi_pekerja.created_at', 'DESC')
                    ->limit($length, $start)
                    ->get()
                    ->getResultArray();

    return $this->response->setJSON([
        'draw'            => (int) $request->getGet('draw'),
        'recordsTotal'    => $total,
        'recordsFiltered' => $filtered,
        'data'            => $data,
    ]);
    }

    private function updateSubTotalAsli($realisasi_id) {
        $realisasiPekerjaModel = new \App\Models\RealisasiPekerjaModel();
        $realisasiBahanModel = new \App\Models\RealisasiBahanModel();
        $realisasiRumahModel = new \App\Models\RealisasiRumahModel();

        $totalPekerja =  $realisasiPekerjaModel
            ->where('realisasi_id', $realisasi_id)
            ->selectSum('biaya_kontraktor')
            ->first();

        $totalBahan = $realisasiBahanModel
            ->where('realisasi_id', $realisasi_id)
            ->selectSum('sub_total')
            ->first();
        
        $grandTotal = ($totalPekerja['biaya_kontraktor'] ?? 0) + ($totalBahan['sub_total'] ?? 0);

        $realisasiRumahModel->update($realisasi_id, ['sub_total_asli' => $grandTotal]);
    }
    public function store() {
        $request = service('request');
        $model = new RealisasiPekerjaModel();
        $realisasiRumahModel = new RealisasiRumahModel();

        $realisasi_id = $request->getPost('realisasi_id');
        $realisasiRumah = $realisasiRumahModel->find($realisasi_id);

        $jumlah_hari = 0;
        if ($realisasiRumah && $realisasiRumah['tanggal_mulai'] && $realisasiRumah['tanggal_selesai']) {
            $mulai = new \DateTime($realisasiRumah['tanggal_mulai']);
            $selesai = new \DateTime($realisasiRumah['tanggal_selesai']);
            $jumlah_hari = $mulai->diff($selesai)->days + 1;
        }

        $data = [
            'realisasi_id'    => $realisasi_id,
            'nama_kontraktor' => $request->getPost('nama_kontraktor'),
            'biaya_kontraktor'=> $request->getPost('biaya_kontraktor'),
            'jumlah_hari'     => $jumlah_hari,
        ];

        $model->insert($data);
        $this->updateSubTotalAsli($realisasi_id);

        return $this->response->setJSON(['status' => 'success']);
    }
    public function edit($id) {
        $model = new RealisasiPekerjaModel();
        $data = $model->find($id);
        if($data) {
            return $this->response->setJSON($data);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data Tidak ditemukan'], 400);
        }
    }  
    public function update($id) {
        $model = new RealisasiPekerjaModel();
        $realisasiRumahModel = new RealisasiRumahModel();

        $realisasi_id = $this->request->getPost('realisasi_id');
        $realisasiRumah = $realisasiRumahModel->find($realisasi_id);

        $jumlah_hari = 0;
        if ($realisasiRumah && $realisasiRumah['tanggal_mulai'] && $realisasiRumah['tanggal_selesai']) {
            $mulai = new \DateTime($realisasiRumah['tanggal_mulai']);
            $selesai = new \DateTime($realisasiRumah['tanggal_selesai']);
            $jumlah_hari = $mulai->diff($selesai)->days + 1;
        }

        $data = [
            'realisasi_id'    => $realisasi_id,
            'nama_kontraktor' => $this->request->getPost('nama_kontraktor'),
            'biaya_kontraktor'=> $this->request->getPost('biaya_kontraktor'),
            'jumlah_hari'     => $jumlah_hari,
        ];

        if ($model->update($id, $data)) {
            $this->updateSubTotalAsli($realisasi_id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data Berhasil diperbarui']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data Gagal diperbarui'], 400);
        }
    }
    public function delete($id) {
    $model = new RealisasiPekerjaModel();

    $pekerja = $model->find($id);
    if (!$pekerja) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ], 400);
    } 

    $realisasi_id = $pekerja['realisasi_id'];

    if ($model->delete($id)) {
        $this->updateSubTotalAsli($realisasi_id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    } else {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ], 400);
    }
}


}
