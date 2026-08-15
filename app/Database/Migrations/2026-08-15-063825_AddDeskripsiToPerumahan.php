<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeskripsiToPerumahan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('perumahan', [
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'dokumen',
                'comment' => 'Deskripsi detail perumahan'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('perumahan', 'deskripsi');
    }
}
