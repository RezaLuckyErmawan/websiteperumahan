<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PerumahanSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'kode_rumah'    => 'RUM001',
                'lokasi'        => 'Jl. Melati No. 5, Banyuwangi',
                'tipe'          => '36',
                'luas_tanah'    => 72,
                'luas_bangunan' => 36,
                'harga'         => 350000000,
                'status'        => 'Dijual',
                'deskripsi'     => 'Rumah tipe 36 siap huni, dekat pasar dan sekolah.',
            ],
            [
                'kode_rumah'    => 'RUM002',
                'lokasi'        => 'Jl. Kenanga No. 10, Banyuwangi',
                'tipe'          => '45',
                'luas_tanah'    => 90,
                'luas_bangunan' => 45,
                'harga'         => 480000000,
                'status'        => 'Dijual',
                'deskripsi'     => 'Rumah tipe 45 dengan carport dan taman kecil.',
            ],
            [
                'kode_rumah'    => 'RUM003',
                'lokasi'        => 'Jl. Cempaka No. 3, Banyuwangi',
                'tipe'          => '60',
                'luas_tanah'    => 120,
                'luas_bangunan' => 60,
                'harga'         => 650000000,
                'status'        => 'Terjual',
                'deskripsi'     => 'Rumah tipe 60, sudah lunas oleh customer.',
            ],
            [
                'kode_rumah'    => 'RUM004',
                'lokasi'        => 'Jl. Mawar No. 7, Banyuwangi',
                'tipe'          => '45',
                'luas_tanah'    => 84,
                'luas_bangunan' => 45,
                'harga'         => 420000000,
                'status'        => 'Booked',
                'deskripsi'     => 'Rumah tipe 45 yang sedang dalam proses booking dan pemberkasan.',
            ],
            [
                'kode_rumah'    => 'RUM005',
                'lokasi'        => 'Jl. Anggrek No. 12, Banyuwangi',
                'tipe'          => '36',
                'luas_tanah'    => 72,
                'luas_bangunan' => 36,
                'harga'         => 350000000,
                'status'        => 'Terjual',
                'deskripsi'     => 'Rumah tipe 36 dengan cicilan internal berjalan.',
            ],
        ];

        foreach ($data as $row) {
            $row['gambar'] = $row['gambar'] ?? null;
            $row['dokumen'] = $row['dokumen'] ?? null;
            $row['updated_at'] = $now;

            $existing = $this->db->table('perumahan')->where('kode_rumah', $row['kode_rumah'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('perumahan')->where('id', $existing['id'])->update($row);
                continue;
            }

            $row['created_at'] = $now;
            $this->db->table('perumahan')->insert($row);
        }
    }
}
