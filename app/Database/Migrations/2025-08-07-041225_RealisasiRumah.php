<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RealisasiRumah extends Migration
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
            'sub_total_asli' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => true,
            ],
            'tanggal_mulai' => [
                'type' => 'date',
            ],
            'tanggal_selesai' =>[
                'type' => 'date',
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
        $this->forge->createTable('realisasi_rumah');
    }

    public function down()
    {
        $this->forge->dropTable('realisasi_rumah');
    }
}
