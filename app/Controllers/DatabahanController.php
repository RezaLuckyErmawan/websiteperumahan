<?php

namespace App\Controllers;
use App\Models\BahanBangunanModel;
use App\Controllers\BaseController;
use App\Models\PerumahanModel;
use CodeIgniter\HTTP\ResponseInterface;

class DatabahanController extends BaseController
{
    public function databahan() {
        $model = new BahanBangunanModel();
        $data = [
            'pageTitle' => 'Data Bahan Bangunan',
            'useDataTables' => true,
            'bahan' => $model->findAll()
        ];
        return view('/page/databahan/databahan', $data);
    }

   public function json() {
    $request = service('request');
    $model = new BahanBangunanModel();

    $searchValue = $request->getGet('search')['value'] ?? '';
    $start = $request->getGet('start') ?? 0;
    $length = $request->getGet('length') ?? 10;

    $totalRecords = $model->countAll();

    if ($searchValue) {
        $model->groupStart()
              ->like('nama_bahan', $searchValue)
              ->orLike('satuan', $searchValue)
              ->orLike('stok', $searchValue)
              ->groupEnd();
    }

    $filteredRecords = $model->countAllResults(false);

    $data = $model->orderBy('created_at', 'DESC') 
                  ->findAll($length, $start);

    return $this->response->setJSON([
        'draw' => (int) $request->getGet('draw'),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data,
    ]);
}



    public function create() {
        return view('/page/databahan/create');
    }
   // Untuk create via AJAX
    public function store() {
        $model = new BahanBangunanModel();
        $data = $this->request->getPost();
        $model->insert($data);
        return $this->response->setJSON(['status' => 'success']);
    }

    // // Untuk edit (ambil data berdasarkan ID)
    // public function edit($id) {
    //     $model = new BahanBangunanModel();
    //     $data = $model->find($id);
    //     return $this->response->setJSON($data);
    // }

    public function getBahan($id) {
        $model = new BahanBangunanModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    // Untuk update via AJAX
    public function update($id) {
        $model = new BahanBangunanModel();
        $data = $this->request->getPost();
        $model->update($id, $data);
        return $this->response->setJSON(['status' => 'updated']);
    }

    // Untuk delete via AJAX
    public function delete($id) {
        $model = new BahanBangunanModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'deleted']);
    }

}
