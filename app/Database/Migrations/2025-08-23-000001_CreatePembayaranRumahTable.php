<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembayaranRumahTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pembelian_rumah_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'tanggal_bayar' => [
                'type' => 'DATE',
            ],
            'jumlah_bayar' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
            ],
            'jenis_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['booking_fee', 'dp', 'cicilan', 'pelunasan'],
                'default'    => 'cicilan',
            ],
            'metode_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
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
        $this->forge->addForeignKey('pembelian_rumah_id', 'pembelian_rumah', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pembayaran_rumah');
    }

    public function down()
    {
        $this->forge->dropTable('pembayaran_rumah');
    }
}
