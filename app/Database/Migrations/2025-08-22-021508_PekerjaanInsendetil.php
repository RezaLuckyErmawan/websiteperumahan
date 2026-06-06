<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PekerjaanInsendetil extends Migration
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
            'mandor_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'tanggal' => [
                'type' => 'DATETIME',
            ],
            'nama_pekerjaan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'total_biaya' => [
                'type' => 'BIGINT',
                'null' => true,
                'constraint' => 20,
            ],
           'status' => [
                'type' => "ENUM('pending','approved','rejected')",
                'default' => 'pending',
            ],
            'approved_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]

        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pekerjaan_insidentil');
    }

    public function down()
    {
        $this->forge->dropTable('pekerjaan_insidentil');
    }
}
