<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'perumahan_id'      => 3,
                'nama'              => 'Dewi Lestari',
                'email'             => 'dewi@example.com',
                'telepon'           => '08456789023',
                'alamat'            => 'Jl. Anggrek No. 23, Banyuwangi',
                'tanggal_pembelian' => '2025-03-20',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'perumahan_id'      => 1,
                'nama'              => 'Budi Santoso',
                'email'             => 'budi@example.com',
                'telepon'           => '083345678912',
                'alamat'            => 'Jl. Mawar No. 78, Banyuwangi',
                'tanggal_pembelian' => '2025-02-15',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        foreach ($data as $customer) {
            $exists = $this->db->table('customer')
                ->where('email', $customer['email'])
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('customer')->insert($customer);
            }
        }
    }
}
