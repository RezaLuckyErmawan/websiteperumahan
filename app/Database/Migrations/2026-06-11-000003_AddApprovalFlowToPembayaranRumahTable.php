<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovalFlowToPembayaranRumahTable extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `pembayaran_rumah` MODIFY `tanggal_bayar` DATE NULL");

        if (!$this->db->fieldExists('status_pengajuan', 'pembayaran_rumah')) {
            $this->forge->addColumn('pembayaran_rumah', [
                'status_pengajuan' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'disetujui', 'ditolak'],
                    'default'    => 'disetujui',
                    'after'      => 'bukti_bayar',
                ],
                'approved_at' => [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'status_pengajuan',
                ],
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
        if ($this->db->fieldExists('approved_by', 'pembayaran_rumah')) {
            $this->forge->dropColumn('pembayaran_rumah', 'approved_by');
        }

        if ($this->db->fieldExists('approved_at', 'pembayaran_rumah')) {
            $this->forge->dropColumn('pembayaran_rumah', 'approved_at');
        }

        if ($this->db->fieldExists('status_pengajuan', 'pembayaran_rumah')) {
            $this->forge->dropColumn('pembayaran_rumah', 'status_pengajuan');
        }

        $this->db->query("ALTER TABLE `pembayaran_rumah` MODIFY `tanggal_bayar` DATE NOT NULL");
    }
}
