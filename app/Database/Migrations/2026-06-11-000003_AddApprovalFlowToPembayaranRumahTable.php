<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovalFlowToPembayaranRumahTable extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `pembayaran_rumah` MODIFY `tanggal_bayar` DATE NULL");

        if (!$this->hasColumn('status_pengajuan')) {
            $this->forge->addColumn('pembayaran_rumah', [
                'status_pengajuan' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'disetujui', 'ditolak'],
                    'default'    => 'disetujui',
                    'after'      => 'bukti_bayar',
                ],
            ]);
        }

        if (!$this->hasColumn('approved_at')) {
            $this->forge->addColumn('pembayaran_rumah', [
                'approved_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'status_pengajuan',
                ],
            ]);
        }

        if (!$this->hasColumn('approved_by')) {
            $this->forge->addColumn('pembayaran_rumah', [
                'approved_by' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'approved_at',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->hasColumn('approved_by')) {
            $this->forge->dropColumn('pembayaran_rumah', 'approved_by');
        }

        if ($this->hasColumn('approved_at')) {
            $this->forge->dropColumn('pembayaran_rumah', 'approved_at');
        }

        if ($this->hasColumn('status_pengajuan')) {
            $this->forge->dropColumn('pembayaran_rumah', 'status_pengajuan');
        }

        $this->db->query("ALTER TABLE `pembayaran_rumah` MODIFY `tanggal_bayar` DATE NOT NULL");
    }

    private function hasColumn(string $column): bool
    {
        $result = $this->db->query(
            'SHOW COLUMNS FROM `pembayaran_rumah` LIKE ' . $this->db->escape($column)
        );

        return is_object($result) && $result->getNumRows() > 0;
    }
}
