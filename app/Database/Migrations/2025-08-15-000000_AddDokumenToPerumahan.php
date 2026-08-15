<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDokumenToPerumahan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('perumahan', [
            'dokumen' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'gambar',
                'comment' => 'Path to uploaded document file (PDF, DOC, etc.)'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('perumahan', 'dokumen');
    }
}
