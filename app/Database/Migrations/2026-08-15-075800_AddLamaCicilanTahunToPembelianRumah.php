<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLamaCicilanTahunToPembelianRumah extends Migration
{
    public function up()
    {
        try {
            $fields = $this->db->getFieldNames('pembelian_rumah');
        } catch (\Throwable $e) {
            $fields = [];
        }

        if (in_array('lama_cicilan_tahun', $fields, true)) {
            return;
        }

        try {
            $this->forge->addColumn('pembelian_rumah', [
                'lama_cicilan_tahun' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'metode_pembayaran',
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
        if (!$this->db->fieldExists('lama_cicilan_tahun', 'pembelian_rumah')) {
            return;
        }

        $this->forge->dropColumn('pembelian_rumah', 'lama_cicilan_tahun');
    }
}
