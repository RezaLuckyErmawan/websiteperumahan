<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatParticipantsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('chat_participants')) {
            return;
        }

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
                'null' => true,
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
        $this->forge->addKey('chat_room');
        $this->forge->addKey('user_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('is_online');
        $this->forge->addKey('last_seen');
        $this->forge->addKey(['chat_room', 'is_online']);

        $this->forge->createTable('chat_participants', true);
    }

    public function down()
    {
        $this->forge->dropTable('chat_participants', true);
    }
}
