<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PembelianRumahSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'customer_id'         => 3,
                'perumahan_id'        => 1,
                'tanggal_pembelian'   => '2025-07-21',
                'harga_beli'          => 350000000,
                'status_pembelian'    => 'Lunas',
                'metode_pembayaran'   => 'KPR',
                'catatan_marketing'   => 'Dari pameran Banyuwangi Expo',
                'status_dokumen'      => 'Verifikasi',
                'request_khusus'      => 'Tambah kanopi belakang',
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'customer_id'         => 1,
                'perumahan_id'        => 2,
                'tanggal_pembelian'   => '2025-07-20',
                'harga_beli'          => 280000000,
                'status_pembelian'    => 'Cicil',
                'metode_pembayaran'   => 'Cicilan Internal',
                'catatan_marketing'   => 'Customer tetap',
                'status_dokumen'      => 'Lengkap',
                'request_khusus'      => 'Cat tembok custom warna biru',
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ],
            [
                'customer_id'         => 4,
                'perumahan_id'        => 3,
                'tanggal_pembelian'   => '2025-07-18',
                'harga_beli'          => 300000000,
                'status_pembelian'    => 'DP',
                'metode_pembayaran'   => 'Cash',
                'catatan_marketing'   => 'Follow-up dari Instagram Ads',
                'status_dokumen'      => 'Pending',
                'request_khusus'      => 'Tambah pagar depan',
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]
        ];

        // Masukkan data ke tabel
        $this->db->table('pembelian_rumah')->insertBatch($data);
    }
}
