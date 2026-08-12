<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PerumahanSeeder extends Seeder
{
    public function run()
    {
          $data = [
            [
                'kode_rumah'     => 'RUM001',
                'lokasi'         => 'Jl. Melati No. 5, Banyuwangi',
                'tipe'           => '36',
                'luas_tanah'     => 72,
                'luas_bangunan'  => 36,
                'harga'          => 350000000,
                'status'         => 'Tanah',
                'gambar'         => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'kode_rumah'     => 'RUM002',
                'lokasi'         => 'Jl. Kenanga No. 10, Banyuwangi',
                'tipe'           => '45',
                'luas_tanah'     => 90,
                'luas_bangunan'  => 45,
                'harga'          => 480000000,
                'status'         => 'Dijual',
                'gambar'         => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'kode_rumah'     => 'RUM003',
                'lokasi'         => 'Jl. Cempaka No. 3, Banyuwangi',
                'tipe'           => '60',
                'luas_tanah'     => 120,
                'luas_bangunan'  => 60,
                'harga'          => 650000000,
                'status'         => 'Terjual',
                'gambar'         => null,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];

        // Insert data ke tabel 'perumahan'
        $this->db->table('perumahan')->insertBatch($data);
    }
}
