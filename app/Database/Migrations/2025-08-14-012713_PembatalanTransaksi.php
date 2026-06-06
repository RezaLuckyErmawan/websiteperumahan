<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PembatalanTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'perumahan_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'keterangan_pembatalan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'pembelian_id' => [
                'type' => 'INT',
                'unsigned'=> true,
            ] ,
              'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pembatalan_transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('pembatalan_transaksi');
    }
}
