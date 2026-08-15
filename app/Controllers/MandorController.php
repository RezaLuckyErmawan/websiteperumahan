<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class MandorController extends BaseController
{
    public function mandor()
    {
        $userModel = new UserModel();
        $data['user'] = $userModel->where('role', 'mandor')->findAll();
        $data['useDataTables'] = true;
        $data['pageTitle'] = 'Data Mandor';

        return view('page/user/mandor', $data);
    }

    public function json()
    {
        $request = service('request');
        $model = new UserModel();

        $searchValue = $request->getGet('search')['value'] ?? '';
        $start = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');
        $draw = (int) $request->getGet('draw');

        $query = $model->where('role', 'mandor');

        if ($searchValue) {
            $query = $query
                ->groupStart()
                ->like('nama', $searchValue)
                ->orLike('username', $searchValue)
                ->orLike('status', $searchValue)
                ->groupEnd();
        }

        $query = $query->orderBy('created_at', 'DESC');

        $filteredQuery = clone $query;

        $data = $query->findAll($length, $start);
        $total = $model->where('role', 'mandor')->countAllResults();
        $filtered = $searchValue ? $filteredQuery->countAllResults(false) : $total;

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('page/user/create');
    }

    public function store()
    {
        $userModel = new UserModel();
        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'mandor',
            'status' => $this->request->getPost('status')
        ];
        $userModel->insert($data);

        return $this->response->setJSON(['success' => true]);
    }

    public function edit($id)
    {
        $model = new UserModel();
        $user = $model->find($id);
        return $this->response->setJSON($user);
    }

    public function update($id)
    {
        $userModel = new UserModel();

        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'role' => 'mandor',
            'status' => $this->request->getPost('status'),
        ];

        $password = $this->request->getPost('password');

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $userModel->update($id, $data);
        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}
