<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiRumahTable extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `perumahan` MODIFY `status` ENUM('Tanah','Proses Pembangunan','Dijual','Terjual','Booked') NOT NULL");

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'perumahan_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'telepon' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'booked',
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
        $this->forge->addUniqueKey('perumahan_id');
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('perumahan_id', 'perumahan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('transaksi_rumah');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi_rumah', true);
        $this->db->query("ALTER TABLE `perumahan` MODIFY `status` ENUM('Tanah','Proses Pembangunan','Dijual','Terjual') NOT NULL");
    }
}
