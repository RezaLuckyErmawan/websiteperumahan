<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBookingFieldsToPembelianRumah extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('sumber', 'pembelian_rumah')) {
            $this->forge->addColumn('pembelian_rumah', [
                'sumber' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'admin',
                    'after'      => 'request_khusus',
                ],
                'user_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'sumber',
                ],
                'berkas' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'user_id',
                ],
                'status_berkas' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'pending',
                    'after'      => 'berkas',
                ],
                'status_verifikasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'tidak_perlu',
                    'after'      => 'status_berkas',
                ],
                'catatan_verifikasi' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'status_verifikasi',
                ],
                'verified_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'catatan_verifikasi',
                ],
                'verified_by' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'verified_at',
                ],
            ]);
        }

        $this->db->query("UPDATE pembelian_rumah SET sumber = 'admin', status_verifikasi = 'tidak_perlu' WHERE sumber IS NULL OR sumber = ''");
        $this->salinDariTransaksiRumah();
    }

    public function down()
    {
        $this->forge->dropColumn('pembelian_rumah', [
            'sumber',
            'user_id',
            'berkas',
            'status_berkas',
            'status_verifikasi',
            'catatan_verifikasi',
            'verified_at',
            'verified_by',
        ]);
    }

    private function salinDariTransaksiRumah(): void
    {
        if (!$this->db->tableExists('transaksi_rumah')) {
            return;
        }

        $rows = $this->db->table('transaksi_rumah')->get()->getResultArray();
        foreach ($rows as $row) {
            $exists = $this->db->table('pembelian_rumah')
                ->where('sumber', 'customer')
                ->where('perumahan_id', $row['perumahan_id'])
                ->where('user_id', $row['user_id'])
                ->where('created_at', $row['created_at'])
                ->countAllResults();
            if ($exists) {
                continue;
            }

            $customerId = $this->pastikanCustomer($row);
            $rumah = $this->db->table('perumahan')->where('id', $row['perumahan_id'])->get()->getRowArray();
            $verifikasi = $row['status_verifikasi'] ?? 'pending';
            if (($row['pembelian_rumah_id'] ?? null) && $verifikasi === 'disetujui') {
                $this->db->table('pembelian_rumah')->where('id', $row['pembelian_rumah_id'])->update([
                    'sumber' => 'customer',
                    'user_id' => $row['user_id'] ?? null,
                    'berkas' => $row['berkas'] ?? null,
                    'status_berkas' => $row['status_berkas'] ?? 'pending',
                    'status_verifikasi' => 'disetujui',
                    'catatan_verifikasi' => $row['catatan_verifikasi'] ?? null,
                    'verified_at' => $row['verified_at'] ?? null,
                    'verified_by' => $row['verified_by'] ?? null,
                ]);
                continue;
            }

            $statusPembelian = 'Booking';
            if ($verifikasi === 'disetujui') {
                $statusPembelian = 'DP';
            } elseif ($verifikasi === 'ditolak') {
                $statusPembelian = 'Batal';
            }

            $this->db->table('pembelian_rumah')->insert([
                'customer_id' => $customerId,
                'perumahan_id' => $row['perumahan_id'],
                'tanggal_pembelian' => date('Y-m-d', strtotime((string) ($row['created_at'] ?? 'now'))),
                'harga_beli' => $rumah['harga'] ?? 0,
                'status_pembelian' => $statusPembelian,
                'metode_pembayaran' => null,
                'status_dokumen' => 'Pending',
                'sumber' => 'customer',
                'user_id' => $row['user_id'] ?? null,
                'berkas' => $row['berkas'] ?? null,
                'status_berkas' => $row['status_berkas'] ?? 'pending',
                'status_verifikasi' => $verifikasi ?: 'pending',
                'catatan_verifikasi' => $row['catatan_verifikasi'] ?? null,
                'verified_at' => $row['verified_at'] ?? null,
                'verified_by' => $row['verified_by'] ?? null,
                'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $row['updated_at'] ?? date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function pastikanCustomer(array $row): int
    {
        $existing = $this->db->table('customer')->where('email', $row['email'])->get()->getRowArray();
        if ($existing) {
            return (int) $existing['id'];
        }

        $this->db->table('customer')->insert([
            'nama' => $row['nama'],
            'email' => $row['email'],
            'telepon' => $row['telepon'],
            'alamat' => $row['alamat'],
            'perumahan_id' => $row['perumahan_id'],
            'tanggal_pembelian' => date('Y-m-d', strtotime((string) ($row['created_at'] ?? 'now'))),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insertID();
    }
}
