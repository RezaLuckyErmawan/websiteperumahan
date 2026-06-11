<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerRoleToUserTable extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `user` MODIFY `role` ENUM('admin', 'mandor', 'karyawan', 'spv', 'customer') NOT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `user` MODIFY `role` ENUM('admin', 'mandor', 'karyawan', 'spv') NOT NULL");
    }
}
