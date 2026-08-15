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
    public function getUnreadCount(string $chatRoom, ?int $userId = null): int
    {
        $builder = $this->where('chat_room', $chatRoom)
            ->where('is_read', false);

        if ($userId !== null) {
            $builder->where('user_id !=', $userId);
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
    public function markAsRead(string $chatRoom, int $userId, ?int $lastMessageId = null): bool
    {
        $data = [
            'is_read' => true,
            'read_at' => date('Y-m-d H:i:s'),
        ];

        $builder = $this->where('chat_room', $chatRoom)
            ->where('user_id !=', $userId)
            ->where('is_read', false);

        if ($lastMessageId !== null) {
            $builder->where('id <=', $lastMessageId);
        }

        return $builder->update($data);
    }

    /**
     * Get recent conversations for a user
     *
     * @param int $userId User ID
     * @param int $limit Number of conversations
     * @return array Recent conversations
     */
    public function getRecentConversations(int $userId, int $limit = 10): array
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT
                m.chat_room,
                MAX(m.created_at) as last_message_at,
                COUNT(CASE WHEN m.is_read = 0 AND m.user_id != ? THEN 1 END) as unread_count,
                (SELECT message FROM {$this->table} m2
                 WHERE m2.chat_room = m.chat_room
                 AND m2.deleted_at IS NULL
                 ORDER BY m2.created_at DESC
                 LIMIT 1) as last_message
            FROM {$this->table} m
            INNER JOIN (
                SELECT DISTINCT chat_room
                FROM {$this->table}
                WHERE user_id = ? OR customer_id IN (
                    SELECT id FROM customer WHERE user_id = ?
                )
            ) distinct_rooms ON m.chat_room = distinct_rooms.chat_room
            WHERE m.deleted_at IS NULL
            GROUP BY m.chat_room
            ORDER BY last_message_at DESC
            LIMIT ?
        ", [$userId, $userId, $userId, $limit]);

        return $query->getResultArray();
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
                cp.user_id
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
