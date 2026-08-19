<?php

namespace App\Database\Seeds;

use App\Models\TransaksiRumahModel;
use CodeIgniter\Database\Seeder;

class PembelianRumahSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $sariId = $this->customerIdByNama('Sari Wulandari');
        $budiId = $this->customerIdByNama('Budi Santoso');
        $dewiId = $this->customerIdByNama('Dewi Lestari');
        $ahmadId = $this->customerIdByNama('Ahmad Wijaya');
        $budiUserId = $this->userIdByUsername('customer_budi');
        $dewiUserId = $this->userIdByUsername('customer_dewi');
        $ahmadUserId = $this->userIdByUsername('customer_ahmad');

        if ($sariId) {
            $sariPembelian = $this->db->table('pembelian_rumah')->where('customer_id', $sariId)->get()->getResultArray();
            foreach ($sariPembelian as $row) {
                $this->db->table('pembayaran_rumah')->where('pembelian_rumah_id', $row['id'])->delete();
            }
            $this->db->table('pembelian_rumah')->where('customer_id', $sariId)->delete();
        }

        if ($budiId && $this->rumahId('RUM005')) {
            $this->upsertPembelian($budiId, [
                'perumahan_id'       => $this->rumahId('RUM005'),
                'tanggal_pembelian'  => '2025-02-15',
                'harga_beli'         => 350000000,
                'status_pembelian'   => 'Cicil',
                'metode_pembayaran'  => 'Cicilan Internal',
                'lama_cicilan_tahun' => 5,
                'tanggal_cicilan'    => '2025-03-15',
                'catatan_marketing'  => 'Customer cicilan internal 5 tahun.',
                'status_dokumen'     => 'Lengkap',
                'request_khusus'     => 'Cat tembok custom warna biru',
                'sumber'             => 'admin',
                'user_id'            => $budiUserId,
                'berkas'             => $this->berkasLengkap(true),
                'status_berkas'      => 'lengkap',
                'status_verifikasi'  => 'tidak_perlu',
                'updated_at'         => $now,
            ]);
        }

        if ($dewiId && $this->rumahId('RUM004')) {
            $this->upsertPembelian($dewiId, [
                'perumahan_id'       => $this->rumahId('RUM004'),
                'tanggal_pembelian'  => '2026-08-13',
                'harga_beli'         => 420000000,
                'status_pembelian'   => 'Booking',
                'metode_pembayaran'  => 'Cicilan Internal',
                'lama_cicilan_tahun' => 10,
                'tanggal_cicilan'    => '2026-09-13',
                'catatan_marketing'  => 'Booking dari landing page. Berkas masih sebagian.',
                'status_dokumen'     => 'Pending',
                'request_khusus'     => null,
                'sumber'             => 'customer',
                'user_id'            => $dewiUserId,
                'berkas'             => $this->berkasSebagian(),
                'status_berkas'      => 'pending',
                'status_verifikasi'  => 'pending',
                'updated_at'         => $now,
            ]);
        }

        if ($ahmadId && $this->rumahId('RUM003')) {
            $this->upsertPembelian($ahmadId, [
                'perumahan_id'       => $this->rumahId('RUM003'),
                'tanggal_pembelian'  => '2024-06-10',
                'harga_beli'         => 650000000,
                'status_pembelian'   => 'Lunas',
                'metode_pembayaran'  => 'Cash',
                'lama_cicilan_tahun' => null,
                'tanggal_cicilan'    => null,
                'catatan_marketing'  => 'Pelunasan cash, dokumen lengkap.',
                'status_dokumen'     => 'Lengkap',
                'request_khusus'     => null,
                'sumber'             => 'admin',
                'user_id'            => $ahmadUserId,
                'berkas'             => $this->berkasLengkap(true),
                'status_berkas'      => 'lengkap',
                'status_verifikasi'  => 'disetujui',
                'updated_at'         => $now,
            ]);
        }
    }

    private function upsertPembelian(int $customerId, array $data): void
    {
        $existing = $this->db->table('pembelian_rumah')->where('customer_id', $customerId)->get()->getRowArray();
        $fields = $this->db->getFieldNames('pembelian_rumah');
        $data = array_filter(
            $data,
            static fn($key) => in_array($key, $fields, true),
            ARRAY_FILTER_USE_KEY
        );

        if ($existing) {
            $this->db->table('pembelian_rumah')->where('id', $existing['id'])->update($data);
            return;
        }

        $data['customer_id'] = $customerId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('pembelian_rumah')->insert($data);
    }

    private function berkasSebagian(): string
    {
        return TransaksiRumahModel::encodeBerkas(
            [
                'ktp' => 'uploads/berkas_booking/dewi-ktp.pdf',
                'kk'  => 'uploads/berkas_booking/dewi-kk.pdf',
            ],
            [
                'ktp' => 'pending',
                'kk'  => 'disetujui',
            ]
        );
    }

    private function berkasLengkap(bool $disetujui): string
    {
        $files = [];
        $verifikasi = [];
        foreach (array_keys(TransaksiRumahModel::JENIS_BERKAS) as $key) {
            $files[$key] = 'uploads/berkas_booking/sample-' . $key . '.pdf';
            $verifikasi[$key] = $disetujui ? 'disetujui' : 'pending';
        }

        return TransaksiRumahModel::encodeBerkas($files, $verifikasi);
    }

    private function customerIdByNama(string $nama): ?int
    {
        $row = $this->db->table('customer')->select('id')->where('nama', $nama)->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }

    private function userIdByUsername(string $username): ?int
    {
        $row = $this->db->table('user')->select('id')->where('username', $username)->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }

    private function rumahId(string $kode): ?int
    {
        $row = $this->db->table('perumahan')->select('id')->where('kode_rumah', $kode)->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }
}
