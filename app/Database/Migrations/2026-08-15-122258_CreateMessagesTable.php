<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessagesTable extends Migration
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
                'comment' => 'Room identifier: customer-{id}, booking-{id}, internal-{team}',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Sender user ID (null for anonymous customers)',
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Customer ID for customer-specific chats',
            ],
            'message' => [
                'type' => 'TEXT',
                'comment' => 'Message content',
            ],
            'message_type' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'image', 'file'],
                'default' => 'text',
                'comment' => 'Type of message content',
            ],
            'attachment_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'URL for file/image attachments',
            ],
            'is_read' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'comment' => 'Whether message has been read',
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when message was read',
            ],
            'sender_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Cached sender name for display',
            ],
            'sender_role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Cached sender role: admin, customer, sales, etc.',
            ],
            'reply_to_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID of message being replied to',
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Additional message metadata',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Soft delete timestamp',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('chat_room');
        $this->forge->addKey('user_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('is_read');
        $this->forge->addKey('created_at');
        $this->forge->addKey('reply_to_id');

        // Add foreign key constraints if tables exist
        // Note: These should be added after users and customers tables are created
        // $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        // $this->forge->addForeignKey('customer_id', 'customer', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('messages');

        // Create index for efficient queries
        $this->db->query('CREATE INDEX idx_chat_room_created ON messages(chat_room, created_at DESC)');
        // Note: Partial indexes not supported in MySQL, using regular index instead
        $this->db->query('CREATE INDEX idx_unread_messages ON messages(chat_room, is_read)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX IF EXISTS idx_chat_room_created ON messages');
        $this->db->query('DROP INDEX IF EXISTS idx_unread_messages ON messages');
        $this->forge->dropTable('messages', true);
    }
}
