<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PembayaranRumahSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $adminId = $this->userIdByUsername('admin1') ?: 1;

        $budiPembelian = $this->pembelianByCustomer('Budi Santoso');
        $ahmadPembelian = $this->pembelianByCustomer('Ahmad Wijaya');
        $dewiPembelian = $this->pembelianByCustomer('Dewi Lestari');

        if ($budiPembelian) {
            $this->syncPembayaran((int) $budiPembelian['id'], [
                [
                    'tanggal_bayar'    => '2025-02-15',
                    'jumlah_bayar'     => 30000000,
                    'jenis_pembayaran' => 'dp',
                    'metode_bayar'     => 'Transfer Bank',
                    'keterangan'       => 'DP rumah RUM005',
                    'status_pengajuan' => 'disetujui',
                    'approved_at'      => '2025-02-16 09:00:00',
                    'approved_by'      => $adminId,
                    'bukti_bayar'      => 'uploads/bukti_pembayaran/budi-dp.pdf',
                ],
                [
                    'tanggal_bayar'    => '2025-03-15',
                    'jumlah_bayar'     => 5333334,
                    'jenis_pembayaran' => 'cicilan',
                    'metode_bayar'     => 'Cicilan Internal',
                    'keterangan'       => 'Cicilan ke-1',
                    'status_pengajuan' => 'disetujui',
                    'approved_at'      => '2025-03-16 10:00:00',
                    'approved_by'      => $adminId,
                    'bukti_bayar'      => 'uploads/bukti_pembayaran/budi-cicilan-1.pdf',
                ],
                [
                    'tanggal_bayar'    => '2025-04-15',
                    'jumlah_bayar'     => 5333334,
                    'jenis_pembayaran' => 'cicilan',
                    'metode_bayar'     => 'Cicilan Internal',
                    'keterangan'       => 'Cicilan ke-2 menunggu verifikasi',
                    'status_pengajuan' => 'pending',
                    'approved_at'      => null,
                    'approved_by'      => null,
                    'bukti_bayar'      => 'uploads/bukti_pembayaran/budi-cicilan-2.pdf',
                ],
            ], $now);
        }

        if ($dewiPembelian) {
            $this->syncPembayaran((int) $dewiPembelian['id'], [
                [
                    'tanggal_bayar'    => null,
                    'jumlah_bayar'     => 5000000,
                    'jenis_pembayaran' => 'booking_fee',
                    'metode_bayar'     => 'Transfer Bank',
                    'keterangan'       => 'Booking fee, menunggu verifikasi',
                    'status_pengajuan' => 'pending',
                    'approved_at'      => null,
                    'approved_by'      => null,
                    'bukti_bayar'      => 'uploads/bukti_pembayaran/dewi-booking.pdf',
                ],
            ], $now);
        }

        if ($ahmadPembelian) {
            $this->syncPembayaran((int) $ahmadPembelian['id'], [
                [
                    'tanggal_bayar'    => '2024-06-10',
                    'jumlah_bayar'     => 100000000,
                    'jenis_pembayaran' => 'dp',
                    'metode_bayar'     => 'Transfer Bank',
                    'keterangan'       => 'DP awal',
                    'status_pengajuan' => 'disetujui',
                    'approved_at'      => '2024-06-11 09:00:00',
                    'approved_by'      => $adminId,
                    'bukti_bayar'      => 'uploads/bukti_pembayaran/ahmad-dp.pdf',
                ],
                [
                    'tanggal_bayar'    => '2024-07-20',
                    'jumlah_bayar'     => 550000000,
                    'jenis_pembayaran' => 'pelunasan',
                    'metode_bayar'     => 'Transfer Bank',
                    'keterangan'       => 'Pelunasan sisa tagihan',
                    'status_pengajuan' => 'disetujui',
                    'approved_at'      => '2024-07-21 11:00:00',
                    'approved_by'      => $adminId,
                    'bukti_bayar'      => 'uploads/bukti_pembayaran/ahmad-lunas.pdf',
                ],
            ], $now);
        }
    }

    private function syncPembayaran(int $pembelianId, array $rows, string $now): void
    {
        $existingIds = array_column(
            $this->db->table('pembayaran_rumah')->select('id')->where('pembelian_rumah_id', $pembelianId)->get()->getResultArray(),
            'id'
        );
        if ($existingIds) {
            $this->db->table('pembayaran_rumah')->where('pembelian_rumah_id', $pembelianId)->delete();
        }

        $fields = $this->db->getFieldNames('pembayaran_rumah');

        foreach ($rows as $row) {
            $row['pembelian_rumah_id'] = $pembelianId;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $row = array_filter(
                $row,
                static fn($key) => in_array($key, $fields, true),
                ARRAY_FILTER_USE_KEY
            );
            $this->db->table('pembayaran_rumah')->insert($row);
        }
    }

    private function pembelianByCustomer(string $nama): ?array
    {
        $customer = $this->db->table('customer')->select('id')->where('nama', $nama)->get()->getRowArray();
        if (!$customer) {
            return null;
        }

        return $this->db->table('pembelian_rumah')->where('customer_id', $customer['id'])->get()->getRowArray() ?: null;
    }

    private function userIdByUsername(string $username): ?int
    {
        $row = $this->db->table('user')->select('id')->where('username', $username)->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }
}
