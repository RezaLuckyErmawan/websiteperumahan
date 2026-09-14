<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerIdToUserTable extends Migration
{
    public function up()
    {
        try {
            $this->forge->addColumn('user', [
                'customer_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'password',
                ],
            ]);
        } catch (\Throwable $e) {
            if (stripos($e->getMessage(), 'Duplicate column') === false) {
                throw $e;
            }
        }
    }

    public function down()
    {
        try {
            $this->forge->dropColumn('user', 'customer_id');
        } catch (\Throwable $e) {
            if (stripos($e->getMessage(), 'check that') === false) {
                throw $e;
            }
        }
    }
}
