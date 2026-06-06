<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\PerumahanModel;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerController extends BaseController
{
    public function customer()
    {
        $model = new CustomerModel();
        $perumahan = new PerumahanModel();
       $data['customers'] = $model->orderBy('created_at', 'DESC')->findAll();
        $data['perumahan'] = $perumahan->where('status !=', 'Terjual')->findAll();
        return view ('page/datacustomer/data-customer', $data);
    }
    
    public function json()
{
    $request = service('request');
        $model = new CustomerModel();

        $searchValue = $request->getGet('search')['value'] ?? '';
        $start       = $request->getGet('start') ?? 0;
        $length      = $request->getGet('length') ?? 10;

        $builder = $model;

        $totalRecords = $builder->countAllResults(false);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('nama', $searchValue)
                ->orLike('email', $searchValue)
                ->orLike('alamat', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        $data = $builder->orderBy('created_at', 'DESC')
                        ->findAll($length, $start);

        return $this->response->setJSON([
            'draw' => (int) $request->getGet('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
}

    public function create() {
        $perumahan = new PerumahanModel();
        $data['perumahan'] = $perumahan->findAll();
        return view ('page/datacustomer/create', $data);
    }

    public function store() {
        $model = new CustomerModel();
        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'tanggal_pembelian' => $this->request->getPost('tanggal_pembelian'),
        ];

        $model->insert($data);
        return redirect()->to('/data-customer');
    }

    public function edit($id) {
         $model = new CustomerModel();
        $perumahanModel = new PerumahanModel();
        $customer = $model->find($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['customer' => $customer]);
        }

        return view('page/datacustomer/edit', [
            'customer'  => $customer,
            'perumahan' => $perumahanModel->findAll()
        ]);
    }
    public function update($id) {
        $model = new CustomerModel();

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'tanggal_pembelian' => $this->request->getPost('tanggal_pembelian'),
        ];

        $model->update($id, $data);
        return redirect()->to('/data-customer')->with('success', 'Data customer berhasil diperbarui');
    }

    public function delete($id) {
        $model = new CustomerModel();

        $customer = $model->find($id);
        if (!$customer) {
            return redirect()->to('/data-customer')->with('error', 'Data customer tidak ditemukan.');
        }
        
        $model->delete($id);
    return redirect()->to('/data-customer')->with('success', 'Data customer berhasil dihapus.');
    }

}
