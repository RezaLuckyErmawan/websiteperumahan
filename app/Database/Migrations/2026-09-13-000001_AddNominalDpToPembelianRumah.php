<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNominalDpToPembelianRumah extends Migration
{
    public function up()
    {
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
        try {
            $this->forge->dropColumn('pembelian_rumah', 'nominal_dp');
        } catch (\Throwable $e) {
            if (stripos($e->getMessage(), 'check that') === false) {
                throw $e;
            }
        }
    }
}
