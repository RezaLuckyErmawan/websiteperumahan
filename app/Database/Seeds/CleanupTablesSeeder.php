<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CleanupTablesSeeder extends Seeder
{
    public function run()
    {
        // Drop the problematic tables if they exist
        $this->db->query("DROP TABLE IF EXISTS messages");
        $this->db->query("DROP TABLE IF EXISTS chat_participants");

        echo "Cleanup completed: messages and chat_participants tables dropped.\n";
    }
}
