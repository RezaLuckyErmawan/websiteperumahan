<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $rum005 = $this->rumahId('RUM005');
        $rum004 = $this->rumahId('RUM004');
        $rum003 = $this->rumahId('RUM003');

        $data = [
            [
                'nama'              => 'Sari Wulandari',
                'email'             => 'sari@example.com',
                'telepon'           => '08123450001',
                'alamat'            => 'Jl. Diponegoro No. 8, Banyuwangi',
                'perumahan_id'      => null,
                'tanggal_pembelian' => null,
            ],
            [
                'nama'              => 'Budi Santoso',
                'email'             => 'budi@example.com',
                'telepon'           => '083345678912',
                'alamat'            => 'Jl. Mawar No. 78, Banyuwangi',
                'perumahan_id'      => $rum005,
                'tanggal_pembelian' => '2025-02-15',
            ],
            [
                'nama'              => 'Dewi Lestari',
                'email'             => 'dewi@example.com',
                'telepon'           => '08456789023',
                'alamat'            => 'Jl. Anggrek No. 23, Banyuwangi',
                'perumahan_id'      => $rum004,
                'tanggal_pembelian' => '2026-08-13',
            ],
            [
                'nama'              => 'Ahmad Wijaya',
                'email'             => 'ahmad@example.com',
                'telepon'           => '08234567890',
                'alamat'            => 'Jl. Sudirman No. 100, Banyuwangi',
                'perumahan_id'      => $rum003,
                'tanggal_pembelian' => '2024-06-10',
            ],
        ];

        foreach ($data as $customer) {
            $customer['updated_at'] = $now;
            $existing = $this->db->table('customer')->where('email', $customer['email'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('customer')->where('id', $existing['id'])->update($customer);
                continue;
            }

            $customer['created_at'] = $now;
            $this->db->table('customer')->insert($customer);
        }
    }

    private function rumahId(string $kode): ?int
    {
        $row = $this->db->table('perumahan')->select('id')->where('kode_rumah', $kode)->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }
}
