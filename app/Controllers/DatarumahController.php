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

        $gambar = $this->resolveGambar();
        if (is_array($gambar) && ($gambar['status'] ?? '') === 'error') {
            return $this->response->setStatusCode(400)->setJSON($gambar);
        }

        $data  = [
            'kode_rumah'     => $this->request->getPost('kode_rumah'),
            'lokasi'         => $this->request->getPost('lokasi'),
            'tipe'           => $this->request->getPost('tipe'),
            'luas_tanah'     => $this->request->getPost('luas_tanah'),
            'luas_bangunan'  => $this->request->getPost('luas_bangunan'),
            'harga'          => $this->request->getPost('harga'),
            'status'         => $this->request->getPost('status'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'gambar'         => is_string($gambar) || $gambar === null ? $gambar : null,
        ];

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

        $gambar = $this->resolveGambar($old);
        if (is_array($gambar) && ($gambar['status'] ?? '') === 'error') {
            return $this->response->setStatusCode(400)->setJSON($gambar);
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
            'gambar'         => is_string($gambar) || $gambar === null ? $gambar : null,
        ];

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
        if (is_array($data)) {
            foreach (PerumahanModel::parseGambar($data['gambar'] ?? null) as $path) {
                $this->deleteGambar($path);
            }
            $this->deleteDokumen($data['dokumen'] ?? null);
        }
        return $this->response->setJSON(['success' => true]);
    }

    private function resolveGambar($old = null)
    {
        $old = is_array($old) ? $old : [];
        $existing = PerumahanModel::parseGambar($this->request->getPost('existing_gambar'));
        $uploaded = $this->uploadGambarList();
        if (is_array($uploaded) && ($uploaded['status'] ?? '') === 'error') {
            return $uploaded;
        }

        $merged = array_values(array_unique(array_merge($existing, is_array($uploaded) ? $uploaded : [])));
        if (count($merged) > 8) {
            return ['status' => 'error', 'message' => 'Maksimal 8 gambar per rumah.'];
        }

        $oldList = PerumahanModel::parseGambar($old['gambar'] ?? null);
        foreach (array_diff($oldList, $merged) as $removed) {
            $this->deleteGambar($removed);
        }

        return PerumahanModel::encodeGambar($merged);
    }

    private function uploadGambarList()
    {
        $files = $this->request->getFileMultiple('gambar') ?: [];
        if ($files === []) {
            $single = $this->request->getFile('gambar');
            $files = $single ? [$single] : [];
        }

        $paths = [];
        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'perumahan';
        $allowed = ['image/jpg', 'image/jpeg', 'image/png'];

        foreach ($files as $file) {
            if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (!$file->isValid()) {
                return ['status' => 'error', 'message' => 'Upload gambar gagal'];
            }

            $mime = strtolower((string) $file->getMimeType());
            if (!in_array($mime, $allowed, true)) {
                return ['status' => 'error', 'message' => 'Format gambar harus JPG atau PNG'];
            }

            if ($file->getSize() > 2 * 1024 * 1024) {
                return ['status' => 'error', 'message' => 'Ukuran gambar maksimal 2MB'];
            }

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $paths[] = 'uploads/perumahan/' . $newName;
        }

        return $paths;
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
