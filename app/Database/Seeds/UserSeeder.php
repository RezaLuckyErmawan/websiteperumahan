<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama'         => 'Rika',
                'username'     => 'Rika',
                'password'     => password_hash('password123', PASSWORD_DEFAULT),
                'role'         => 'karyawan',
                'customer_id'  => null,
                'status'       => 'aktif'
            ],
            [
                'nama'         => 'El Gato',
                'username'     => 'ElGato',
                'password'     => password_hash('password123', PASSWORD_DEFAULT),
                'role'         => 'karyawan',
                'customer_id'  => null,
                'status'       => 'aktif'
            ],
            [
                'nama'         => 'UjangRambo',
                'username'     => 'UjangRambo',
                'password'     => password_hash('password123', PASSWORD_DEFAULT),
                'role'         => 'mandor',
                'customer_id'  => null,
                'status'       => 'aktif'
            ],
            [
                'nama'         => 'Admin Sistem',
                'username'     => 'admin1',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'role'         => 'admin',
                'customer_id'  => null,
                'status'       => 'aktif'
            ],
            // User Customer
            [
                'nama'         => 'Ahmad Subagyo',
                'username'     => 'customer_ahmad',
                'password'     => password_hash('customer123', PASSWORD_DEFAULT),
                'role'         => 'customer',
                'customer_id'  => 1,
                'status'       => 'aktif'
            ],
            [
                'nama'         => 'Siti Mariam',
                'username'     => 'customer_siti',
                'password'     => password_hash('customer123', PASSWORD_DEFAULT),
                'role'         => 'customer',
                'customer_id'  => 2,
                'status'       => 'aktif'
            ],
            [
                'nama'         => 'Budi Santoso',
                'username'     => 'customer_budi',
                'password'     => password_hash('customer123', PASSWORD_DEFAULT),
                'role'         => 'customer',
                'customer_id'  => 3,
                'status'       => 'aktif'
            ],
            [
                'nama'         => 'Dewi Lestari',
                'username'     => 'customer_dewi',
                'password'     => password_hash('customer123', PASSWORD_DEFAULT),
                'role'         => 'customer',
                'customer_id'  => 4,
                'status'       => 'aktif'
            ],
            [
                'nama'         => 'Rudi Hermawan',
                'username'     => 'customer_rudi',
                'password'     => password_hash('customer123', PASSWORD_DEFAULT),
                'role'         => 'customer',
                'customer_id'  => 5,
                'status'       => 'aktif'
            ],
            [
                'nama'         => 'Sari Wulandari',
                'username'     => 'customer_sari',
                'password'     => password_hash('customer123', PASSWORD_DEFAULT),
                'role'         => 'customer',
                'customer_id'  => 6,
                'status'       => 'aktif'
            ],
        ];

        // Insert ke tabel user
        $this->db->table('user')->insertBatch($data);
    
    }
}
