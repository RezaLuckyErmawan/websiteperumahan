<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PembelianBahanModel;
use CodeIgniter\HTTP\ResponseInterface;

class PembelianbahanController extends BaseController
{
    public function datapembelian()
    {
        $model = new PembelianBahanModel();
        $data['pembelianbahan'] = $model->findAll();
        $data['pageTitle'] = 'Pembelian Bahan';
        $data['useDataTables'] = true;
        return view ('page/pembelianbahan/data_pembelian_bahan', $data);
    }

    public function json() {
        $request = service('request');
        $model = new PembelianBahanModel();

        $searchValue = $request->getGet('search')['value'] ?? '';
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;

        $totalRecords = $model->countAll();

        if ($searchValue) {
            $model->groupStart()
            ->like('nomor_nota', $searchValue)
            ->orLike('tanggal', $searchValue)
            ->orLike('tanggal', $searchValue)
            ->orLike('supplier', $searchValue)
            ->orlike('total_harga', $searchValue)
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
        return view('page/pembelianbahan/create');
    }

   public function update($id) {
    $model = new PembelianBahanModel();

    $data = [
        'nomor_nota'   => $this->request->getPost('nomor_nota'),
        'tanggal'      => $this->request->getPost('tanggal'),
        'supplier'     => $this->request->getPost('supplier'),
        'total_harga'  => $this->request->getPost('total_harga'),
    ];

    $model->update($id, $data);
    return $this->response->setJSON(['status' => 'updated']);
}

public function store() {
    $model = new PembelianBahanModel();

    $data = [
        'nomor_nota'   => $this->request->getPost('nomor_nota'),
        'tanggal'      => $this->request->getPost('tanggal'),
        'supplier'     => $this->request->getPost('supplier'),
        'total_harga'  => $this->request->getPost('total_harga'),
    ];

    $model->insert($data);
    return $this->response->setJSON(['status' => 'created']);
}


   public function edit($id) {
    $model = new PembelianBahanModel();
    $data = $model->find($id);
    return $this->response->setJSON($data);
}


   public function delete($id) {
    $model = new PembelianBahanModel();
    $model->delete($id);
    return $this->response->setJSON(['status' => 'deleted']);
}

}
