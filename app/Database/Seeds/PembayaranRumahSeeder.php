<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PembayaranRumahSeeder extends Seeder
{
    public function run()
    {
        // Insert Perumahan (Houses)
        $perumahanData = [
            [
                'kode_rumah'     => 'RH001',
                'lokasi'         => 'Jl. Melati No. 10',
                'tipe'           => '36/60',
                'luas_tanah'     => 60,
                'luas_bangunan'  => 36,
                'harga'          => 150000000,
                'status'         => 'Terjual',
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'kode_rumah'     => 'RH002',
                'lokasi'         => 'Jl. Melati No. 12',
                'tipe'           => '45/72',
                'luas_tanah'     => 72,
                'luas_bangunan'  => 45,
                'harga'          => 200000000,
                'status'         => 'Terjual',
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'kode_rumah'     => 'RH003',
                'lokasi'         => 'Jl. Mawar No. 5',
                'tipe'           => '36/60',
                'luas_tanah'     => 60,
                'luas_bangunan'  => 36,
                'harga'          => 155000000,
                'status'         => 'Terjual',
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'kode_rumah'     => 'RH004',
                'lokasi'         => 'Jl. Mawar No. 7',
                'tipe'           => '60/90',
                'luas_tanah'     => 90,
                'luas_bangunan'  => 60,
                'harga'          => 280000000,
                'status'         => 'Terjual',
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'kode_rumah'     => 'RH005',
                'lokasi'         => 'Jl. Anggrek No. 15',
                'tipe'           => '36/60',
                'luas_tanah'     => 60,
                'luas_bangunan'  => 36,
                'harga'          => 160000000,
                'status'         => 'Dijual',
                'created_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('perumahan')->insertBatch($perumahanData);

        // Get inserted perumahan IDs
        $perumahanIds = [1, 2, 3, 4, 5];

        // Insert Customer
        $customerData = [
            [
                'perumahan_id'       => 1,
                'nama'                => 'Budi Santoso',
                'email'               => 'budi@gmail.com',
                'telepon'             => '08123456789',
                'alamat'              => 'Jl. Merdeka No. 50',
                'tanggal_pembelian'   => '2025-01-15',
                'created_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'perumahan_id'       => 2,
                'nama'                => 'Siti Rahayu',
                'email'               => 'siti@yahoo.com',
                'telepon'             => '08198765432',
                'alamat'              => 'Jl. Pahlawan No. 25',
                'tanggal_pembelian'   => '2025-02-10',
                'created_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'perumahan_id'       => 3,
                'nama'                => 'Ahmad Wijaya',
                'email'               => 'ahmad.w@gmail.com',
                'telepon'             => '08234567890',
                'alamat'              => 'Jl. Sudirman No. 100',
                'tanggal_pembelian'   => '2025-03-05',
                'created_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'perumahan_id'       => 4,
                'nama'                => 'Dewi Lestari',
                'email'               => 'dewi.l@hotmail.com',
                'telepon'             => '08345678901',
                'alamat'              => 'Jl. Gatot Subroto No. 75',
                'tanggal_pembelian'   => '2025-04-20',
                'created_at'          => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('customer')->insertBatch($customerData);

        // Insert Pembelian Rumah (links customer to perumahan)
        $pembelianData = [
            [
                'customer_id'         => 1,
                'perumahan_id'        => 1,
                'tanggal_pembelian'   => '2025-01-15',
                'harga_beli'          => 150000000,
                'status_pembelian'    => 'DP',
                'metode_pembayaran'   => 'KPR',
                'status_dokumen'      => 'Lengkap',
                'created_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'customer_id'         => 2,
                'perumahan_id'        => 2,
                'tanggal_pembelian'   => '2025-02-10',
                'harga_beli'          => 200000000,
                'status_pembelian'    => 'Cicil',
                'metode_pembayaran'   => 'KPR',
                'status_dokumen'      => 'Lengkap',
                'created_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'customer_id'         => 3,
                'perumahan_id'        => 3,
                'tanggal_pembelian'   => '2025-03-05',
                'harga_beli'          => 155000000,
                'status_pembelian'    => 'DP',
                'metode_pembayaran'   => 'Tunai',
                'status_dokumen'      => 'Pending',
                'created_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'customer_id'         => 4,
                'perumahan_id'        => 4,
                'tanggal_pembelian'   => '2025-04-20',
                'harga_beli'          => 280000000,
                'status_pembelian'    => 'Proses',
                'metode_pembayaran'   => 'KPR',
                'status_dokumen'      => 'Lengkap',
                'created_at'          => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('pembelian_rumah')->insertBatch($pembelianData);

        // pembayaran_rumah stays EMPTY for testing
    }
}
