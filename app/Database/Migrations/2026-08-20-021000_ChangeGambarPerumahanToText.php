<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeGambarPerumahanToText extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('perumahan', [
            'gambar' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('perumahan', [
            'gambar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }
}
