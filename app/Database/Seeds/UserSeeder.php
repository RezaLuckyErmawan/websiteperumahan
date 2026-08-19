<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $passwordStaff = password_hash('password123', PASSWORD_DEFAULT);
        $passwordAdmin = password_hash('admin123', PASSWORD_DEFAULT);
        $passwordCustomer = password_hash('customer123', PASSWORD_DEFAULT);

        $data = [
            [
                'nama'     => 'Admin Sistem',
                'username' => 'admin1',
                'password' => $passwordAdmin,
                'role'     => 'admin',
            ],
            [
                'nama'     => 'Admin Kedua',
                'username' => 'admin2',
                'password' => $passwordAdmin,
                'role'     => 'admin',
            ],
            [
                'nama'     => 'Owner Utama',
                'username' => 'owner1',
                'password' => $passwordStaff,
                'role'     => 'owner',
            ],
            [
                'nama'     => 'Owner Kedua',
                'username' => 'owner2',
                'password' => $passwordStaff,
                'role'     => 'owner',
            ],
            [
                'nama'     => 'Mandor Utama',
                'username' => 'mandor1',
                'password' => $passwordStaff,
                'role'     => 'mandor',
            ],
            [
                'nama'     => 'Mandor Kedua',
                'username' => 'mandor2',
                'password' => $passwordStaff,
                'role'     => 'mandor',
            ],
            [
                'nama'     => 'Supervisor Utama',
                'username' => 'spv1',
                'password' => $passwordStaff,
                'role'     => 'spv',
            ],
            [
                'nama'     => 'Supervisor Kedua',
                'username' => 'spv2',
                'password' => $passwordStaff,
                'role'     => 'spv',
            ],
            [
                'nama'     => 'Sari Wulandari',
                'username' => 'customer_sari',
                'password' => $passwordCustomer,
                'role'     => 'customer',
            ],
            [
                'nama'     => 'Budi Santoso',
                'username' => 'customer_budi',
                'password' => $passwordCustomer,
                'role'     => 'customer',
            ],
            [
                'nama'     => 'Dewi Lestari',
                'username' => 'customer_dewi',
                'password' => $passwordCustomer,
                'role'     => 'customer',
            ],
            [
                'nama'     => 'Ahmad Wijaya',
                'username' => 'customer_ahmad',
                'password' => $passwordCustomer,
                'role'     => 'customer',
            ],
        ];

        foreach ($data as $user) {
            $user['customer_id'] = $user['role'] === 'customer' ? $this->customerIdByNama($user['nama']) : null;
            $user['status'] = 'aktif';

            $existing = $this->db->table('user')->where('username', $user['username'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('user')->where('id', $existing['id'])->update([
                    'nama'        => $user['nama'],
                    'role'        => $user['role'],
                    'customer_id' => $user['customer_id'],
                    'status'      => 'aktif',
                ]);
                continue;
            }

            $user['created_at'] = $now;
            $this->db->table('user')->insert($user);
        }
    }

    private function customerIdByNama(string $nama): ?int
    {
        $row = $this->db->table('customer')->select('id')->where('nama', $nama)->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }
}
