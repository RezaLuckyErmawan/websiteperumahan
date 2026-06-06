<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InsidentilModel;
use App\Models\PerumahanModel;
use App\Models\UserModel;
use App\Models\RealisasiRumahModel;
use App\Models\RealisasiBahanModel;
use App\Models\RealisasiPekerjaModel;
use CodeIgniter\HTTP\ResponseInterface;

class PekerjaanInsidentilController extends BaseController
{
    public function pekerjaaninsidentil()
    {
        $perumahan = (new PerumahanModel())->findAll();
        $mandor    = (new UserModel())->where('role', 'mandor')->findAll();
        $role      = session()->get('role');

        return view('page/pekerjaan_insidentil/pekerjaan_insidentil', [
            'perumahan' => $perumahan,
            'mandor'    => $mandor,
            'role'      => $role,
        ]);
    }

    public function json()
    {
        $request = service('request');
        $session = session();

        $userId = $session->get('user_id');
        $role   = $session->get('role');

        $db = \Config\Database::connect();
        $builder = $db->table('pekerjaan_insidentil pi')
            ->select('
                pi.*,
                perumahan.kode_rumah,
                mandor.nama as mandor_nama,
                approver.nama as approver_nama
            ')
            ->join('perumahan', 'perumahan.id = pi.perumahan_id')
            ->join('user as mandor', 'mandor.id = pi.mandor_id')
            ->join('user as approver', 'approver.id = pi.approved_by', 'left');

        if ($role === 'mandor') {
            $builder->where('pi.mandor_id', $userId);
        }

        $searchValue = $request->getGet('search')['value'] ?? null;
        if ($searchValue) {
            $builder->groupStart()
                ->like('perumahan.kode_rumah', $searchValue)
                ->orLike('mandor.nama', $searchValue)
                ->orLike('approver.nama', $searchValue)
                ->orLike('pi.tanggal', $searchValue)
                ->orLike('pi.nama_pekerjaan', $searchValue)
                ->orLike('pi.keterangan', $searchValue)
                ->orLike('pi.total_biaya', $searchValue)
                ->orLike('pi.status', $searchValue)
            ->groupEnd();
        }

        $total    = $db->table('pekerjaan_insidentil')->countAll();
        $filtered = $builder->countAllResults(false);
        $start  = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $builder->orderBy('pi.created_at', 'DESC');
        $data = $builder->get($length, $start)->getResultArray();

        return $this->response->setJSON([
            'draw'            => (int) $request->getGet('draw'),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    public function store()
    {
        $model = new InsidentilModel();
        $session = session();
        $userId = $session->get('user_id');
        $role   = $session->get('role');

        if (!in_array($role, ['mandor', 'admin'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya mandor atau admin yang bisa menambahkan data'
            ]);
        }

        $mandorId = ($role === 'mandor') ? $userId : $this->request->getPost('mandor_id');

        $data = [
            'perumahan_id'   => $this->request->getPost('perumahan_id'),
            'mandor_id'      => $mandorId,
            'tanggal'        => $this->request->getPost('tanggal'),
            'nama_pekerjaan' => $this->request->getPost('nama_pekerjaan'),
            'keterangan'     => $this->request->getPost('keterangan'),
            'total_biaya'    => $this->request->getPost('total_biaya'),
            'status'         => 'pending',
        ];

        if ($model->insert($data)) {
            $this->updateSubTotalAsliByPerumahan($data['perumahan_id']);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil ditambahkan']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Data gagal ditambahkan']);
    }

    public function edit($id)
    {
        $model = new InsidentilModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    public function update($id)
    {
        $model = new InsidentilModel();
        $session = session();
        $userId = $session->get('user_id');
        $role   = $session->get('role');

        if (!in_array($role, ['mandor', 'admin'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Hanya mandor atau admin yang bisa melakukan update data',
            ]);
        }

        $dataLama = $model->find($id);

        $mandorId = ($role === 'mandor') ? $userId : $this->request->getPost('mandor_id');

        $data = [
            'perumahan_id'   => $this->request->getPost('perumahan_id'),
            'mandor_id'      => $mandorId,
            'tanggal'        => $this->request->getPost('tanggal'),
            'nama_pekerjaan' => $this->request->getPost('nama_pekerjaan'),
            'keterangan'     => $this->request->getPost('keterangan'),
            'total_biaya'    => $this->request->getPost('total_biaya'),
            'status'         => ($role === 'admin') ? $this->request->getPost('status') : 'pending',
        ];

        if ($model->update($id, $data)) {
            $this->updateSubTotalAsliByPerumahan($data['perumahan_id']);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil diperbarui']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Data gagal diperbarui']);
    }

    public function delete($id)
    {
        $model = new InsidentilModel();
        $data = $model->find($id);

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }

        if ($model->delete($id)) {
            $this->updateSubTotalAsliByPerumahan($data['perumahan_id']);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Data gagal dihapus']);
    }

    private function updateSubTotalAsliByPerumahan($perumahan_id)
    {
        $realisasiRumahModel   = new RealisasiRumahModel();
        $realisasiBahanModel   = new RealisasiBahanModel();
        $realisasiPekerjaModel = new RealisasiPekerjaModel();
        $insidentilModel       = new InsidentilModel();

        $realisasiRumah = $realisasiRumahModel->where('perumahan_id', $perumahan_id)->first();
        if (!$realisasiRumah) {
            return;
        }

        $realisasi_id = $realisasiRumah['id'];

        $totalBahan = $realisasiBahanModel
            ->where('realisasi_id', $realisasi_id)
            ->selectSum('sub_total')
            ->first()['sub_total'] ?? 0;

        $totalPekerja = $realisasiPekerjaModel
            ->where('realisasi_id', $realisasi_id)
            ->selectSum('biaya_kontraktor')
            ->first()['biaya_kontraktor'] ?? 0;

        $totalInsidentil = $insidentilModel
            ->where('perumahan_id', $perumahan_id)
            ->where('status' ,'approved')
            ->selectSum('total_biaya')
            ->first()['total_biaya'] ?? 0;

        $grandTotal = $totalBahan + $totalPekerja + $totalInsidentil;

        $realisasiRumahModel->update($realisasi_id, ['sub_total_asli' => $grandTotal]);
    }
}
