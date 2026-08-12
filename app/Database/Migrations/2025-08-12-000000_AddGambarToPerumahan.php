<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGambarToPerumahan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('perumahan', [
            'gambar' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('perumahan', 'gambar');
    }
}
