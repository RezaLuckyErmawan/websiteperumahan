<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RAB extends Migration
{
    public function up()
    {
        $this->forge->addField([
        'id' => [
            'type' => 'INT',
            'unsigned' => true,
            'auto_increment' => true,
        ],
        'kode_rab' => [
            'type' => 'VARCHAR',
            'constraint' => 50,
        ],
        'perumahan_id' => [
            'type' => 'INT',
            'unsigned' => true,
        ],
        'total_anggaran' => [
            'type' => 'BIGINT',
            'null' => true
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

        $this->forge->addForeignKey('perumahan_id', 'perumahan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rab_rumah');
    }

    public function down()
    {
        $this->forge->dropTable('rab_rumah');
    }
}
