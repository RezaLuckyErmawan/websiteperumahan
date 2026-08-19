<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerumahanModel;
use CodeIgniter\HTTP\ResponseInterface;

class DatarumahController extends BaseController
{
    public function datarumah() {
        $data = [
            'pageTitle' => 'Data Perumahan',
            'useDataTables' => true
        ];
        return view('page/datarumah/datarumah', $data);
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

        $baseBuilder = $db->table('perumahan');
        $totalRecords = (clone $baseBuilder)->countAllResults();

        $filteredBuilder = $db->table('perumahan');
        if ($searchValue !== '') {
            $filteredBuilder->groupStart()
                ->like('kode_rumah', $searchValue)
                ->orLike('lokasi', $searchValue)
                ->orLike('tipe', $searchValue)
                ->orLike('status', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = (clone $filteredBuilder)->countAllResults();

        $data = $filteredBuilder
            ->orderBy('created_at', 'DESC')
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


    public function create() {
        return view('page/datarumah/create');
    } 

    public function store() {
        $model = new PerumahanModel();

        $uploadedGambar = $this->uploadGambar();
        if (is_array($uploadedGambar) && ($uploadedGambar['status'] ?? '') === 'error') {
            return $this->response->setStatusCode(400)->setJSON($uploadedGambar);
        }

        $data  = [
            'kode_rumah'     => $this->request->getPost('kode_rumah'),
            'lokasi'         => $this->request->getPost('lokasi'),
            'tipe'           => $this->request->getPost('tipe'),
            'luas_tanah'     => $this->request->getPost('luas_tanah'),
            'luas_bangunan'  => $this->request->getPost('luas_bangunan'),
            'harga'          => $this->request->getPost('harga'),
            'status'         => $this->request->getPost('status'),
            'deskripsi'      => $this->request->getPost('deskripsi')
        ];

        if ($uploadedGambar) {
            $data['gambar'] = $uploadedGambar;
        }

        $uploadedDokumen = $this->uploadDokumen();
        if (is_array($uploadedDokumen) && ($uploadedDokumen['status'] ?? '') === 'error') {
            return $this->response->setStatusCode(400)->setJSON($uploadedDokumen);
        }

        if ($uploadedDokumen) {
            $data['dokumen'] = $uploadedDokumen;
        }

        $model->insert($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil ditambahkan']);
    }

    public function edit($id) {
        $model = new PerumahanModel();
        $data['perumahan'] = $model->find($id);
        return $this->response->setJSON($data['perumahan']);
    }

    public function update($id) {
        $model = new PerumahanModel();
        $old = $model->find($id);

        $uploadedGambar = $this->uploadGambar();
        if (is_array($uploadedGambar) && ($uploadedGambar['status'] ?? '') === 'error') {
            return $this->response->setStatusCode(400)->setJSON($uploadedGambar);
        }

        $uploadedDokumen = $this->uploadDokumen();
        if (is_array($uploadedDokumen) && ($uploadedDokumen['status'] ?? '') === 'error') {
            return $this->response->setStatusCode(400)->setJSON($uploadedDokumen);
        }

        $data = [
            'kode_rumah'     => $this->request->getPost('kode_rumah'),
            'lokasi'         => $this->request->getPost('lokasi'),
            'tipe'           => $this->request->getPost('tipe'),
            'luas_tanah'     => $this->request->getPost('luas_tanah'),
            'luas_bangunan'  => $this->request->getPost('luas_bangunan'),
            'harga'          => $this->request->getPost('harga'),
            'status'         => $this->request->getPost('status'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
        ];

        if ($uploadedGambar) {
            $data['gambar'] = $uploadedGambar;
            $this->deleteGambar($old['gambar'] ?? null);
        }

        if ($uploadedDokumen) {
            $data['dokumen'] = $uploadedDokumen;
            $this->deleteDokumen($old['dokumen'] ?? null);
        }

        $model->update($id, $data);
        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id) {
        $model = new PerumahanModel();
        $data = $model->find($id);
        $model->delete($id);
        $this->deleteGambar($data['gambar'] ?? null);
        $this->deleteDokumen($data['dokumen'] ?? null);
        return $this->response->setJSON(['success' => true]);
    }

    private function uploadGambar()
    {
        $file = $this->request->getFile('gambar');

        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (!$file->isValid()) {
            return ['status' => 'error', 'message' => 'Upload gambar gagal'];
        }

        $validated = $this->validate([
            'gambar' => [
                'uploaded[gambar]',
                'mime_in[gambar,image/jpg,image/jpeg,image/png]',
                'max_size[gambar,2048]',
            ]
        ]);

        if (!$validated) {
            return ['status' => 'error', 'message' => implode(', ', $this->validator->getErrors())];
        }

        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'perumahan';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        return 'uploads/perumahan/' . $newName;
    }

    private function deleteGambar(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    private function uploadDokumen()
    {
        $file = $this->request->getFile('dokumen');

        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (!$file->isValid()) {
            return ['status' => 'error', 'message' => 'Upload dokumen gagal'];
        }

        $validated = $this->validate([
            'dokumen' => [
                'uploaded[dokumen]',
                'mime_in[dokumen,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]',
                'max_size[dokumen,5120]',
            ]
        ]);

        if (!$validated) {
            return ['status' => 'error', 'message' => implode(', ', $this->validator->getErrors())];
        }

        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'documents';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        return 'uploads/documents/' . $newName;
    }

    private function deleteDokumen(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
