<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembelianRumahTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'customer_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'perumahan_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'tanggal_pembelian' => [
                'type' => 'DATE',
            ],
            'harga_beli' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
            ],
            'status_pembelian' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Proses',
            ],
            'metode_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'lama_cicilan_tahun' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'tanggal_cicilan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status_dokumen' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Pending',
            ],
            'request_khusus' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan_marketing' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        // Foreign key ke tabel customer dan perumahan
        $this->forge->addForeignKey('customer_id', 'customer', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('perumahan_id', 'perumahan', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('pembelian_rumah');
    }

    public function down()
    {
        $this->forge->dropTable('pembelian_rumah');
    }
}
