<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DetailPembelianSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'pembelian_id'      => 1,
                'bahan_bangunan_id' => 1,
                'jumlah'            => 40,
                'harga_satuan'      => 75000,
                'subtotal'          => 3000000,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'pembelian_id'      => 1,
                'bahan_bangunan_id' => 2,
                'jumlah'            => 20,
                'harga_satuan'      => 65000,
                'subtotal'          => 1300000,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'pembelian_id'      => 2,
                'bahan_bangunan_id' => 3,
                'jumlah'            => 500,
                'harga_satuan'      => 2500,
                'subtotal'          => 1250000,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'pembelian_id'      => 3,
                'bahan_bangunan_id' => 4,
                'jumlah'            => 12,
                'harga_satuan'      => 85000,
                'subtotal'          => 1020000,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        $this->db->table('detail_pembelian_bahan')->insertBatch($data);
    }
}
