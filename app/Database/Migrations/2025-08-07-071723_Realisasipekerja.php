<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use PHPUnit\Framework\Constraint\Constraint;

class Realisasipekerja extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'realisasi_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'nama_kontraktor' => [
                'type' => 'VARCHAR',
                'null' => true,
                'constraint' => 30,
            ],
            'biaya_kontraktor' => [
                'type' => 'BIGINT',
                'null' => true,
                'constraint' => 10,
            ],
            'jumlah_hari' => [
                'type' => 'INT',
                'null' => true,
                'constraint' => 20,
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
        $this->forge->createTable('realisasi_pekerja');
    }

    public function down()
    {
        $this->forge->dropTable('realisasi_pekerja');
    }
}
