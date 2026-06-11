<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class LoginController extends BaseController
{
    public function loginform()
    {
        return view('page/login');
    }

    public function login() {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $userModel = new UserModel();
        $user =$userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'username Tidak ditemukan');
        };   
        if (!password_verify($password, $user['password'])){
            return redirect()->back()->with('error', 'Password Salah coy');
        }
        if ($user['role'] !== 'admin' && strtolower($user['status']) !== 'aktif') {
            return redirect()->back()->with('error', 'Akun belum disetujui atau ditolak.');
        }

        
        session()->set([
            'user_id' => $user['id'],
            'nama' => $user['nama'],
            'username' => $user['username'],
            'customer_id' => $user['customer_id'] ?? null,
            'role' => $user['role'],
            'isLoggedIn' => true,
        ]);

        if ($user['role'] === 'customer') {
            return redirect()->to('/pembayaran-rumah');
        }

        return redirect()->to('/dashboard');
     }

     public function logout() {
        session()->destroy();
        return redirect()->to('/login');
     }
}
