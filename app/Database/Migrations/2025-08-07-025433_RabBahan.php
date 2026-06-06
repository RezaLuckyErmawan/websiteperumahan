<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RabBahan extends Migration
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
            'bahanbangunan_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'jumlah_rencana' => [
                'type' => 'INT',
                'null' =>true,
            ],
            'harga_satuan' => [
                'type' => 'INT',
                'null' => true,
            ],
            'sub_total' => [
                'type' => 'INT',
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
        $this->forge->addkey('id', true);
        $this->forge->createTable('rab_bahan');
    }

    public function down()
    {
        $this->forge->dropTable('rab_bahan');
    }
}
