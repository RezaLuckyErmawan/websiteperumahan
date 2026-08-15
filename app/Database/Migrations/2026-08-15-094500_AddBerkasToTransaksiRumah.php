<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBerkasToTransaksiRumah extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaksi_rumah', [
            'berkas' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'alamat',
            ],
            'status_berkas' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
                'after'      => 'berkas',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi_rumah', ['berkas', 'status_berkas']);
    }
}
