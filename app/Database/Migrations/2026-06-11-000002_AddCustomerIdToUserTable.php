<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerIdToUserTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('customer_id', 'user')) {
            return;
        }

        $fields = [
            'customer_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'password',
            ],
        ];

        $this->forge->addColumn('user', $fields);
    }

    public function down()
    {
        if (!$this->db->fieldExists('customer_id', 'user')) {
            return;
        }

        $this->forge->dropColumn('user', 'customer_id');
    }
}
