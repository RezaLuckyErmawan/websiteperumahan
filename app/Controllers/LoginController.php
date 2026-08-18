<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class LoginController extends BaseController
{
    public function loginform()
    {
        return view('page/login');
    }

    public function registerForm()
    {
        return view('page/register', [
            'tanggalDaftar' => date('Y-m-d'),
        ]);
    }

    public function register()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[user.username]',
            'nama' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[customer.email]',
            'telepon' => 'required|min_length[8]|max_length[20]',
            'alamat' => 'required|min_length[5]',
            'password' => 'required|min_length[6]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $tanggalDaftar = date('Y-m-d');
        $customerModel = new CustomerModel();
        $userModel = new UserModel();
        $db = db_connect();

        $db->transStart();

        try {
            $customerModel->insert([
                'nama' => trim((string) $this->request->getPost('nama')),
                'email' => trim((string) $this->request->getPost('email')),
                'telepon' => trim((string) $this->request->getPost('telepon')),
                'alamat' => trim((string) $this->request->getPost('alamat')) ?: '-',
                'tanggal_pembelian' => $tanggalDaftar,
            ]);

            $customerId = (int) $customerModel->getInsertID();

            $userModel->insert([
                'nama' => trim((string) $this->request->getPost('nama')),
                'username' => trim((string) $this->request->getPost('username')),
                'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
                'customer_id' => $customerId,
                'role' => 'customer',
                'status' => 'aktif',
            ]);

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Pendaftaran gagal. Silakan coba lagi.');
        }

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Pendaftaran gagal. Silakan coba lagi.');
        }

        return redirect()->to('/login')->with('success', 'Pendaftaran berhasil. Silakan login.');
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
            return redirect()->back()->with('error', 'Password Anda Salah!');
        }
        if ($user['role'] !== 'admin' && strtolower($user['status']) !== 'aktif') {
            return redirect()->back()->with('error', 'Akun belum disetujui atau ditolak.');
        }

        $customerId = null;
        if ($user['role'] === 'customer') {
            $customerModel = new \App\Models\CustomerModel();
            $customer = null;

            if (!empty($user['customer_id'])) {
                $customer = $customerModel->find($user['customer_id']);
            }

            if (!$customer && !empty($user['nama'])) {
                $customer = $customerModel->where('nama', $user['nama'])->first();
            }

            if ($customer) {
                $customerId = (int) $customer['id'];
            }
        }

        session()->set([
            'user_id' => $user['id'],
            'nama' => $user['nama'],
            'username' => $user['username'],
            'customer_id' => $customerId,  // Focus on customer_id
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
