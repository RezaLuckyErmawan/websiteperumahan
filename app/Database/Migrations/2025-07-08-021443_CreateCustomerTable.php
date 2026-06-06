<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerTable extends Migration
{
     public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'perumahan_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'nama'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'telepon'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'alamat'       => ['type' => 'TEXT'],
            'tanggal_pembelian' => ['type' => 'DATE', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('perumahan_id', 'perumahan', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('customer');
    }

    public function down()
    {
        $this->forge->dropTable('customer');
    }
}
