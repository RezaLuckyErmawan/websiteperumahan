<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PembelianRumahSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('pembelian_rumah')->countAllResults() > 0) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $dewiId = $this->customerIdByNama('Dewi Lestari');
        $budiId = $this->customerIdByNama('Budi Santoso');

        if ($dewiId === null || $budiId === null) {
            return;
        }

        $data = [
            [
                'customer_id'        => $dewiId,
                'perumahan_id'       => 3,
                'tanggal_pembelian'  => '2025-03-20',
                'harga_beli'         => 650000000,
                'status_pembelian'   => 'DP',
                'metode_pembayaran'  => 'Cash',
                'lama_cicilan_tahun' => null,
                'catatan_marketing'  => 'Follow-up dari Instagram Ads',
                'status_dokumen'     => 'Pending',
                'request_khusus'     => 'Tambah pagar depan',
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
            [
                'customer_id'        => $budiId,
                'perumahan_id'       => 1,
                'tanggal_pembelian'  => '2025-02-15',
                'harga_beli'         => 350000000,
                'status_pembelian'   => 'Cicil',
                'metode_pembayaran'  => 'Cicilan Internal',
                'lama_cicilan_tahun' => 5,
                'catatan_marketing'  => 'Customer tetap',
                'status_dokumen'     => 'Lengkap',
                'request_khusus'     => 'Cat tembok custom warna biru',
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
        ];

        $this->db->table('pembelian_rumah')->insertBatch($data);
    }

    private function customerIdByNama(string $nama): ?int
    {
        $row = $this->db->table('customer')->select('id')->where('nama', $nama)->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }
}
