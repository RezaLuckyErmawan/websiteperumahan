<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'nama'        => 'Admin Sistem',
                'username'    => 'admin1',
                'password'    => password_hash('admin123', PASSWORD_DEFAULT),
                'role'        => 'admin',
                'customer_id' => null,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Admin Kedua',
                'username'    => 'admin2',
                'password'    => password_hash('admin123', PASSWORD_DEFAULT),
                'role'        => 'admin',
                'customer_id' => null,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Owner Utama',
                'username'    => 'owner1',
                'password'    => password_hash('password123', PASSWORD_DEFAULT),
                'role'        => 'owner',
                'customer_id' => null,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Owner Kedua',
                'username'    => 'owner2',
                'password'    => password_hash('password123', PASSWORD_DEFAULT),
                'role'        => 'owner',
                'customer_id' => null,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Mandor Utama',
                'username'    => 'mandor1',
                'password'    => password_hash('password123', PASSWORD_DEFAULT),
                'role'        => 'mandor',
                'customer_id' => null,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Mandor Kedua',
                'username'    => 'mandor2',
                'password'    => password_hash('password123', PASSWORD_DEFAULT),
                'role'        => 'mandor',
                'customer_id' => null,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Supervisor Utama',
                'username'    => 'spv1',
                'password'    => password_hash('password123', PASSWORD_DEFAULT),
                'role'        => 'spv',
                'customer_id' => null,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Supervisor Kedua',
                'username'    => 'spv2',
                'password'    => password_hash('password123', PASSWORD_DEFAULT),
                'role'        => 'spv',
                'customer_id' => null,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Dewi Lestari',
                'username'    => 'customer_dewi',
                'password'    => password_hash('customer123', PASSWORD_DEFAULT),
                'role'        => 'customer',
                'customer_id' => 1,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
            [
                'nama'        => 'Budi Santoso',
                'username'    => 'customer_budi',
                'password'    => password_hash('customer123', PASSWORD_DEFAULT),
                'role'        => 'customer',
                'customer_id' => 2,
                'status'      => 'aktif',
                'created_at'  => $now,
            ],
        ];

        foreach ($data as $user) {
            $exists = $this->db->table('user')
                ->where('username', $user['username'])
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('user')->insert($user);
            }
        }
    }
}
