<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RabPekerja extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'rab_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'nama_kontraktor' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'biaya_kontraktor' => [
                'type' => 'BIGINT',
                'constraint' => 30,
                'null' => true,
            ],
            'estimasi_hari' => [
                'type' => 'INT',
                'constraint' => 20,
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
        $this->forge->createTable('rab_pekerja');
    }

    public function down()
    {
        $this->forge->dropTable('rab_pekerja');
    }
}
 
