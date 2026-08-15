<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTanggalCicilanToPembelianRumah extends Migration
{
    public function up()
    {
        try {
            $fields = $this->db->getFieldNames('pembelian_rumah');
        } catch (\Throwable $e) {
            $fields = [];
        }

        if (in_array('tanggal_cicilan', $fields, true)) {
            return;
        }

        try {
            $this->forge->addColumn('pembelian_rumah', [
                'tanggal_cicilan' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'lama_cicilan_tahun',
                ],
            ]);
        } catch (\Throwable $e) {
            if (stripos($e->getMessage(), 'Duplicate column') === false) {
                throw $e;
            }
        }
    }

    public function down()
    {
        if (!$this->db->fieldExists('tanggal_cicilan', 'pembelian_rumah')) {
            return;
        }

        $this->forge->dropColumn('pembelian_rumah', 'tanggal_cicilan');
    }
}
