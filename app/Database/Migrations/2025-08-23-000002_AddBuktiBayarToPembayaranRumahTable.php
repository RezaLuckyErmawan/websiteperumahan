<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuktiBayarToPembayaranRumahTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pembayaran_rumah', [
            'bukti_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'keterangan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pembayaran_rumah', 'bukti_bayar');
    }
}
