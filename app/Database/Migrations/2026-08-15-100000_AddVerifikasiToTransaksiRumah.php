<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVerifikasiToTransaksiRumah extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('status_verifikasi', 'transaksi_rumah')) {
            $this->forge->addColumn('transaksi_rumah', [
                'status_verifikasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'pending',
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
                'pembelian_rumah_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'verified_by',
                ],
            ]);
        }

        $this->ubahUniquePerumahanMenjadiIndexBiasa();
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi_rumah', [
            'status_verifikasi',
            'catatan_verifikasi',
            'verified_at',
            'verified_by',
            'pembelian_rumah_id',
        ]);
    }

    private function ubahUniquePerumahanMenjadiIndexBiasa(): void
    {
        $rows = $this->db->query("SHOW INDEX FROM `transaksi_rumah` WHERE Key_name = 'perumahan_id'")->getResultArray();
        if (!$rows) {
            $this->db->query('ALTER TABLE `transaksi_rumah` ADD INDEX `perumahan_id` (`perumahan_id`)');
            return;
        }

        $isUnique = (int) ($rows[0]['Non_unique'] ?? 1) === 0;
        if (!$isUnique) {
            return;
        }

        $fkName = $this->namaForeignKeyPerumahan();
        if ($fkName) {
            $this->db->query('ALTER TABLE `transaksi_rumah` DROP FOREIGN KEY `' . $fkName . '`');
        }

        $this->db->query('ALTER TABLE `transaksi_rumah` DROP INDEX `perumahan_id`');
        $this->db->query('ALTER TABLE `transaksi_rumah` ADD INDEX `perumahan_id` (`perumahan_id`)');
        $this->db->query(
            'ALTER TABLE `transaksi_rumah`
             ADD CONSTRAINT `transaksi_rumah_perumahan_id_foreign`
             FOREIGN KEY (`perumahan_id`) REFERENCES `perumahan`(`id`)
             ON DELETE CASCADE ON UPDATE CASCADE'
        );
    }

    private function namaForeignKeyPerumahan(): ?string
    {
        $row = $this->db->query("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'transaksi_rumah'
              AND COLUMN_NAME = 'perumahan_id'
              AND REFERENCED_TABLE_NAME = 'perumahan'
            LIMIT 1
        ")->getRowArray();

        return $row['CONSTRAINT_NAME'] ?? null;
    }
}
