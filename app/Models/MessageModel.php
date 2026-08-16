<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'chat_room',
        'user_id',
        'customer_id',
        'message',
        'message_type',
        'attachment_url',
        'is_read',
        'read_at',
        'sender_name',
        'sender_role',
        'reply_to_id',
        'metadata',
    ];

    protected $validationRules = [
        'chat_room' => 'required|max_length[100]',
        'message' => 'required|max_length[5000]',
        'message_type' => 'permit_empty|in_list[text,image,file]',
    ];

    protected function initialize()
    {
        if ($this->db->tableExists($this->table)) {
            return;
        }

        $forge = \Config\Database::forge();
        $forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'chat_room' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'message_type' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'image', 'file'],
                'default' => 'text',
            ],
            'attachment_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_read' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'sender_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'sender_role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'reply_to_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'metadata' => [
                'type' => 'JSON',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $forge->addKey('id', true);
        $forge->addKey('chat_room');
        $forge->createTable($this->table, true);
    }

    protected $validationMessages = [
        'chat_room' => [
            'required' => 'Chat room identifier is required',
            'max_length' => 'Chat room identifier cannot exceed 100 characters',
        ],
        'message' => [
            'required' => 'Message content is required',
            'max_length' => 'Message cannot exceed 5000 characters',
        ],
    ];

    /**
     * Get messages for a specific chat room
     *
     * @param string $chatRoom Chat room identifier
     * @param int $limit Number of messages to retrieve
     * @param int $offset Offset for pagination
     * @return array Messages
     */
    public function getMessagesByRoom(string $chatRoom, int $limit = 50, int $offset = 0): array
    {
        return $this->where('chat_room', $chatRoom)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'ASC')
            ->limit($limit, $offset)
            ->find();
    }

    /**
     * Get unread message count for a room
     *
     * @param string $chatRoom Chat room identifier
     * @param int|null $userId User ID (optional)
     * @return int Unread count
     */
    public function getUnreadCount(string $chatRoom, ?int $userId = null, ?int $customerId = null): int
    {
        if ($userId === null && $customerId === null) {
            return 0;
        }

        $participant = db_connect()->table('chat_participants')
            ->where('chat_room', $chatRoom)
            ->where('left_at', null);

        if ($userId !== null && $customerId !== null) {
            $participant->groupStart()
                ->where('user_id', $userId)
                ->orWhere('customer_id', $customerId)
                ->groupEnd();
        } elseif ($userId !== null) {
            $participant->where('user_id', $userId);
        } else {
            $participant->where('customer_id', $customerId);
        }

        $row = $participant->get()->getRowArray();
        $lastReadId = (int) ($row['last_message_id'] ?? 0);

        $builder = $this->where('chat_room', $chatRoom)
            ->where('deleted_at', null)
            ->where('id >', $lastReadId);

        if ($userId !== null) {
            $builder->groupStart()
                ->where('user_id !=', $userId)
                ->orWhere('user_id', null)
                ->groupEnd();
        }

        if ($customerId !== null) {
            $builder->groupStart()
                ->where('customer_id !=', $customerId)
                ->orWhere('customer_id', null)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Mark messages as read
     *
     * @param string $chatRoom Chat room identifier
     * @param int $userId User ID marking as read
     * @param int|null $lastMessageId Last message ID to mark up to
     * @return bool Success status
     */
    public function markAsRead(string $chatRoom, ?int $userId = null, ?int $customerId = null, ?int $lastMessageId = null): bool
    {
        if ($userId === null && $customerId === null) {
            return false;
        }

        $db = $this->db;
        $query = $db->table($this->table)
            ->where('chat_room', $chatRoom)
            ->where('is_read', false);

        if ($userId !== null && $customerId !== null) {
            $query->groupStart()
                ->where('user_id !=', $userId)
                ->orWhere('customer_id !=', $userId)
                ->orWhere('user_id !=', $customerId)
                ->orWhere('customer_id !=', $customerId)
                ->groupEnd();
        } elseif ($userId !== null) {
            $query->groupStart()
                ->where('user_id !=', $userId)
                ->orWhere('customer_id !=', $userId)
                ->groupEnd();
        } elseif ($customerId !== null) {
            $query->groupStart()
                ->where('user_id !=', $customerId)
                ->orWhere('customer_id !=', $customerId)
                ->groupEnd();
        }

        if ($lastMessageId !== null) {
            $query->where('id <=', $lastMessageId);
        }

        if ($query->get()->getRowArray() === null) {
            return false;
        }

        return $query->set([
            'is_read' => true,
            'read_at' => date('Y-m-d H:i:s'),
        ])->update();
    }

    /**
     * Get recent conversations for a user
     *
     * @param int $userId User ID
     * @param int $limit Number of conversations
     * @return array Recent conversations
     */
    public function getRecentConversations(?int $userId = null, ?int $customerId = null, int $limit = 10, string $role = ''): array
    {
        if ($userId === null && $customerId === null && !in_array($role, ['admin', 'owner', 'mandor', 'spv'], true)) {
            return [];
        }

        $db = \Config\Database::connect();

        $conditions = [];
        $params = [];

        if ($userId !== null) {
            $conditions[] = 'cp.user_id = ?';
            $params[] = $userId;
        }

        if ($customerId !== null) {
            $conditions[] = 'cp.customer_id = ?';
            $params[] = $customerId;
            $conditions[] = 'cp.chat_room = ?';
            $params[] = 'customer-' . $customerId;
        }

        if (in_array($role, ['admin', 'owner', 'mandor', 'spv'], true)) {
            $conditions[] = 'cp.role = ?';
            $params[] = $role;
        }

        if ($role === 'admin') {
            $conditions[] = "cp.chat_room LIKE 'customer-%'";
        }

        if (empty($conditions)) {
            return [];
        }

        $whereSql = '(' . implode(' OR ', $conditions) . ')';

        $query = $db->query(
            "
            SELECT
                cp.chat_room,
                MAX(m.created_at) as last_message_at,
                (
                    SELECT m2.message
                    FROM {$this->table} m2
                    WHERE m2.chat_room = cp.chat_room
                    AND m2.deleted_at IS NULL
                    ORDER BY m2.created_at DESC, m2.id DESC
                    LIMIT 1
                ) as last_message,
                (
                    SELECT m2.user_id
                    FROM {$this->table} m2
                    WHERE m2.chat_room = cp.chat_room
                    AND m2.deleted_at IS NULL
                    ORDER BY m2.created_at DESC, m2.id DESC
                    LIMIT 1
                ) as last_message_user_id
            FROM chat_participants cp
            LEFT JOIN {$this->table} m ON m.chat_room = cp.chat_room AND m.deleted_at IS NULL
            WHERE cp.left_at IS NULL
            AND {$whereSql}
            GROUP BY cp.chat_room
            ORDER BY last_message_at DESC
            LIMIT ?
            ",
            array_merge($params, [$limit])
        );

        $rows = $query->getResultArray();

        foreach ($rows as &$row) {
            $row['unread_count'] = $this->getUnreadCount($row['chat_room'], $userId, $customerId);
        }

        return $rows;
    }

    /**
     * Search messages in a room
     *
     * @param string $chatRoom Chat room identifier
     * @param string $searchQuery Search query
     * @param int $limit Result limit
     * @return array Matching messages
     */
    public function searchMessages(string $chatRoom, string $searchQuery, int $limit = 20): array
    {
        return $this->like('message', $searchQuery)
            ->where('chat_room', $chatRoom)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get message with replies
     *
     * @param int $messageId Message ID
     * @return array|null Message with replies
     */
    public function getMessageWithReplies(int $messageId): ?array
    {
        $message = $this->find($messageId);

        if ($message) {
            $replies = $this->where('reply_to_id', $messageId)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'ASC')
                ->find();

            $message['replies'] = $replies;
        }

        return $message;
    }

    /**
     * Get typing users in a room
     *
     * @param string $chatRoom Chat room identifier
     * @param int $secondsAgo Seconds ago to consider typing
     * @return array Typing users
     */
    public function getTypingUsers(string $chatRoom, int $secondsAgo = 5): array
    {
        $db = \Config\Database::connect();
        $timeThreshold = date('Y-m-d H:i:s', time() - $secondsAgo);

        $query = $db->query("
            SELECT DISTINCT
                cp.participant_name,
                cp.user_id,
                cp.customer_id
            FROM chat_participants cp
            WHERE cp.chat_room = ?
            AND cp.typing_status = 'typing'
            AND cp.typing_at > ?
            AND cp.left_at IS NULL
        ", [$chatRoom, $timeThreshold]);

        return $query->getResultArray();
    }

    /**
     * Clean up old messages
     *
     * @param int $daysOld Days old to delete
     * @param int|null $limit Max messages to delete
     * @return int Number of messages deleted
     */
    public function cleanupOldMessages(int $daysOld = 90, ?int $limit = null): int
    {
        $builder = $this->where('created_at <', date('Y-m-d H:i:s', time() - ($daysOld * 86400)));

        if ($limit !== null) {
            $builder->limit($limit);
        }

        $count = $builder->countAllResults(false);

        if ($count > 0) {
            $this->delete(null, $limit);
        }

        return $count;
    }

    /**
     * Get message statistics
     *
     * @param string $chatRoom Chat room identifier
     * @return array Statistics
     */
    public function getMessageStats(string $chatRoom): array
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT
                COUNT(*) as total_messages,
                COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread_messages,
                COUNT(CASE WHEN message_type = 'image' THEN 1 END) as image_count,
                COUNT(CASE WHEN message_type = 'file' THEN 1 END) as file_count,
                COUNT(CASE WHEN attachment_url IS NOT NULL THEN 1 END) as attachment_count
            FROM {$this->table}
            WHERE chat_room = ?
            AND deleted_at IS NULL
        ", [$chatRoom]);

        return $query->getRowArray();
    }
}
