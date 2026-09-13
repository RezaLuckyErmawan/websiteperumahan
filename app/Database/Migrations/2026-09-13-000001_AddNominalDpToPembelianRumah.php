<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNominalDpToPembelianRumah extends Migration
{
    public function up()
    {
        try {
            $fields = $this->db->getFieldNames('pembelian_rumah');
        } catch (\Throwable $e) {
            $fields = [];
        }

        if (in_array('nominal_dp', $fields, true)) {
            return;
        }

        try {
            $this->forge->addColumn('pembelian_rumah', [
                'nominal_dp' => [
                    'type'     => 'BIGINT',
                    'constraint' => 20,
                    'null'     => true,
                    'after'    => 'harga_beli',
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
        if (!$this->db->fieldExists('nominal_dp', 'pembelian_rumah')) {
            return;
        }

        $this->forge->dropColumn('pembelian_rumah', 'nominal_dp');
    }
}
