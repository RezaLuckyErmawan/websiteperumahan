<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    public function user()
    {
        $userModel = new UserModel();
        $customerModel = new CustomerModel();
        $data['user'] = $userModel->findAll();
        $data['customers'] = $customerModel->orderBy('nama', 'ASC')->findAll();
        return view('page/user/user', $data);
    }

    public function json() {
        $request = service('request');
        $model = new UserModel();

        $searchValue = $request->getGet('search')['value'] ?? '';
        $start = (int) $request->getGet('start');
        $length = (int) $request->getGet('length');
        $draw = (int) $request->getGet('draw');

        $query = $model;

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
        $total = $model->countAll();
        $filtered = $searchValue ? $filteredQuery->countAllResults(false) : $total;
        
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function create() {
        return view('page/user/create');
    }

    public function store() {
        $userModel = new UserModel();
        $allowedRoles = ['admin', 'mandor', 'karyawan', 'spv', 'customer'];
        $role = $this->request->getPost('role');

        if (!in_array($role, $allowedRoles, true)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Role tidak valid']);
        }

        if ($role === 'customer' && !$this->request->getPost('customer_id')) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Customer terkait wajib dipilih']);
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'customer_id' => $role === 'customer' ? $this->request->getPost('customer_id') : null,
            'role' => $role,
            'status' => $this->request->getPost('status')
        ];
        $userModel->insert($data);

        return $this->response->setJSON(['success' => true]);
    }
    
    public function edit($id) {
        $model = new UserModel();
        $user = $model->find($id);
        return $this->response->setJSON($user);
    }

    public function update($id) {
        $userModel = new UserModel();
        $allowedRoles = ['admin', 'mandor', 'karyawan', 'spv', 'customer'];
        $role = $this->request->getPost('role');

        if (!in_array($role, $allowedRoles, true)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Role tidak valid']);
        }

        if ($role === 'customer' && !$this->request->getPost('customer_id')) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Customer terkait wajib dipilih']);
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'customer_id' => $role === 'customer' ? $this->request->getPost('customer_id') : null,
            'role' => $role,
            'status' => $this->request->getPost('status')
        ];

        $password = $this->request->getPost('password');

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $userModel->update($id, $data);

        return $this->response->setJSON(['success' => true]);
    }

    public function delete($id) {
        $userModel = new UserModel();
        $userModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}
