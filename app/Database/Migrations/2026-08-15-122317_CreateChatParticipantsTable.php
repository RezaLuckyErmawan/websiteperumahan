<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatParticipantsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'chat_room' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Room identifier',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User participant ID (null for anonymous)',
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Customer participant ID',
            ],
            'participant_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Display name for participant',
            ],
            'participant_type' => [
                'type' => 'ENUM',
                'constraint' => ['user', 'customer'],
                'default' => 'user',
                'comment' => 'Type of participant',
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'User role in system: admin, sales, etc.',
            ],
            'is_online' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'comment' => 'Online status',
            ],
            'last_seen' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Last activity timestamp',
            ],
            'last_message_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Last message read by this participant',
            ],
            'typing_status' => [
                'type' => 'ENUM',
                'constraint' => ['idle', 'typing'],
                'default' => 'idle',
                'comment' => 'Typing indicator status',
            ],
            'typing_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Last typing activity',
            ],
            'joined_at' => [
                'type' => 'DATETIME',
                'comment' => 'When participant joined the room',
            ],
            'left_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When participant left the room',
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Additional participant metadata',
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

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['chat_room', 'user_id'], 'unique_chat_user');
        $this->forge->addUniqueKey(['chat_room', 'customer_id'], 'unique_chat_customer');
        $this->forge->addKey('chat_room');
        $this->forge->addKey('user_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('is_online');

        // Create table
        $this->forge->createTable('chat_participants');

        // Create indexes for efficient queries
        // Note: Partial indexes not supported in MySQL, using regular index instead
        $this->db->query('CREATE INDEX idx_chat_online ON chat_participants(chat_room, is_online)');
        $this->db->query('CREATE INDEX idx_last_seen ON chat_participants(last_seen DESC)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX IF EXISTS idx_chat_online ON chat_participants');
        $this->db->query('DROP INDEX IF EXISTS idx_last_seen ON chat_participants');
        $this->forge->dropTable('chat_participants', true);
    }
}
