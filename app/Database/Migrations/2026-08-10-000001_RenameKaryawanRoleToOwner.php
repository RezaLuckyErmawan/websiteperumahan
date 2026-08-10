<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameKaryawanRoleToOwner extends Migration
{
    public function up()
    {
        // Tambah nilai enum baru dulu agar data lama bisa diganti
        $this->db->query("ALTER TABLE `user` MODIFY `role` ENUM('admin', 'mandor', 'karyawan', 'owner', 'spv', 'customer') NOT NULL");
        $this->db->query("UPDATE `user` SET `role` = 'owner' WHERE `role` = 'karyawan'");
        $this->db->query("ALTER TABLE `user` MODIFY `role` ENUM('admin', 'mandor', 'owner', 'spv', 'customer') NOT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `user` MODIFY `role` ENUM('admin', 'mandor', 'owner', 'karyawan', 'spv', 'customer') NOT NULL");
        $this->db->query("UPDATE `user` SET `role` = 'karyawan' WHERE `role` = 'owner'");
        $this->db->query("ALTER TABLE `user` MODIFY `role` ENUM('admin', 'mandor', 'karyawan', 'spv', 'customer') NOT NULL");
    }
}
