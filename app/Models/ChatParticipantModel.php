<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatParticipantModel extends Model
{
    protected $table = 'chat_participants';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'chat_room',
        'user_id',
        'customer_id',
        'participant_name',
        'participant_type',
        'role',
        'is_online',
        'last_seen',
        'last_message_id',
        'typing_status',
        'typing_at',
        'joined_at',
        'left_at',
        'metadata',
    ];

    protected $validationRules = [
        'chat_room' => 'required|max_length[100]',
        'participant_name' => 'required|max_length[100]',
        'participant_type' => 'required|in_list[user,customer]',
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
            'participant_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'participant_type' => [
                'type' => 'ENUM',
                'constraint' => ['user', 'customer'],
                'default' => 'user',
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'is_online' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'last_seen' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_message_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'typing_status' => [
                'type' => 'ENUM',
                'constraint' => ['idle', 'typing'],
                'default' => 'idle',
            ],
            'typing_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'joined_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'left_at' => [
                'type' => 'DATETIME',
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
        ]);
        $forge->addKey('id', true);
        $forge->addKey('chat_room');
        $forge->createTable($this->table, true);
    }

    /**
     * Add participant to chat room
     *
     * @param array $data Participant data
     * @return int|false Insert ID or false on failure
     */
    public function addParticipant(array $data)
    {
        $data['joined_at'] = date('Y-m-d H:i:s');
        $data['left_at'] = null;

        // Check if already exists
        $existing = $this->getParticipant($data['chat_room'], $data['user_id'] ?? null, $data['customer_id'] ?? null);

        if ($existing) {
            // Update existing participant
            $this->update($existing['id'], [
                'is_online' => true,
                'left_at' => null,
                'last_seen' => date('Y-m-d H:i:s'),
            ]);

            return $existing['id'];
        }

        return $this->insert($data);
    }

    /**
     * Remove participant from chat room
     *
     * @param string $chatRoom Chat room identifier
     * @param int|null $userId User ID
     * @param int|null $customerId Customer ID
     * @return bool Success status
     */
    public function removeParticipant(string $chatRoom, ?int $userId = null, ?int $customerId = null): bool
    {
        if ($userId === null && $customerId === null) {
            return false;
        }

        $db = $this->db;
        $query = $db->table($this->table)
            ->where('chat_room', $chatRoom);

        if ($userId !== null && $customerId !== null) {
            $query->groupStart()
                ->where('user_id', $userId)
                ->orWhere('customer_id', $customerId)
                ->groupEnd();
        } elseif ($userId !== null) {
            $query->where('user_id', $userId);
        } elseif ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        if ($query->get()->getRowArray() === null) {
            return false;
        }

        return $query->set([
            'is_online' => false,
            'left_at' => date('Y-m-d H:i:s'),
        ])->update() !== false;
    }

    /**
     * Get participant info
     *
     * @param string $chatRoom Chat room identifier
     * @param int|null $userId User ID
     * @param int|null $customerId Customer ID
     * @return array|null Participant data
     */
    public function getParticipant(string $chatRoom, ?int $userId = null, ?int $customerId = null): ?array
    {
        if ($userId === null && $customerId === null) {
            return null;
        }

        $builder = $this->where('chat_room', $chatRoom);

        if ($userId !== null && $customerId !== null) {
            $builder->groupStart()
                ->where('user_id', $userId)
                ->orWhere('customer_id', $customerId)
                ->groupEnd();
        } elseif ($userId !== null) {
            $builder->where('user_id', $userId);
        } elseif ($customerId !== null) {
            $builder->where('customer_id', $customerId);
        }

        return $builder->first();
    }

    /**
     * Get all participants in a room
     *
     * @param string $chatRoom Chat room identifier
     * @param bool $onlineOnly Only get online participants
     * @return array Participants
     */
    public function getRoomParticipants(string $chatRoom, bool $onlineOnly = false): array
    {
        $builder = $this->where('chat_room', $chatRoom)
            ->where('left_at', null);

        if ($onlineOnly) {
            $builder->where('is_online', true);
        }

        return $builder->orderBy('joined_at', 'ASC')
            ->find();
    }

    /**
     * Update online status
     *
     * @param string $chatRoom Chat room identifier
     * @param int|null $userId User ID
     * @param int|null $customerId Customer ID
     * @param bool $isOnline Online status
     * @return bool Success status
     */
    public function updateOnlineStatus(string $chatRoom, ?int $userId, ?int $customerId, bool $isOnline): bool
    {
        if ($userId === null && $customerId === null) {
            return false;
        }

        $db = $this->db;
        $query = $db->table($this->table)
            ->where('chat_room', $chatRoom);

        if ($userId !== null && $customerId !== null) {
            $query->groupStart()
                ->where('user_id', $userId)
                ->orWhere('customer_id', $customerId)
                ->groupEnd();
        } elseif ($userId !== null) {
            $query->where('user_id', $userId);
        } elseif ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        if ($query->get()->getRowArray() === null) {
            return false;
        }

        return $query->set([
            'is_online' => $isOnline,
            'last_seen' => date('Y-m-d H:i:s'),
        ])->update();
    }

    /**
     * Update typing status
     *
     * @param string $chatRoom Chat room identifier
     * @param int|null $userId User ID
     * @param int|null $customerId Customer ID
     * @param bool $isTyping Typing status
     * @return bool Success status
     */
    public function updateTypingStatus(string $chatRoom, ?int $userId, ?int $customerId, bool $isTyping): bool
    {
        if ($userId === null && $customerId === null) {
            return false;
        }

        $db = $this->db;
        $query = $db->table($this->table)
            ->where('chat_room', $chatRoom);

        if ($userId !== null && $customerId !== null) {
            $query->groupStart()
                ->where('user_id', $userId)
                ->orWhere('customer_id', $customerId)
                ->groupEnd();
        } elseif ($userId !== null) {
            $query->where('user_id', $userId);
        } elseif ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        $row = $query->get()->getRowArray();
        if ($row === null) {
            return false;
        }

        return $db->table($this->table)
            ->where('id', $row['id'])
            ->update([
                'typing_status' => $isTyping ? 'typing' : 'idle',
                'typing_at' => date('Y-m-d H:i:s'),
            ]) !== false;
    }

    /**
     * Update last message read
     *
     * @param string $chatRoom Chat room identifier
     * @param int|null $userId User ID
     * @param int|null $customerId Customer ID
     * @param int $messageId Last message ID
     * @return bool Success status
     */
    public function updateLastMessage(string $chatRoom, ?int $userId, ?int $customerId, int $messageId): bool
    {
        if ($userId === null && $customerId === null) {
            return false;
        }

        $db = $this->db;
        $query = $db->table($this->table)
            ->where('chat_room', $chatRoom);

        if ($userId !== null && $customerId !== null) {
            $query->groupStart()
                ->where('user_id', $userId)
                ->orWhere('customer_id', $customerId)
                ->groupEnd();
        } elseif ($userId !== null) {
            $query->where('user_id', $userId);
        } elseif ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        $row = $query->get()->getRowArray();
        if ($row === null) {
            return false;
        }

        return $db->table($this->table)
            ->where('id', $row['id'])
            ->update(['last_message_id' => $messageId]) !== false;
    }

    /**
     * Clean up inactive participants
     *
     * @param int $minutesInactive Minutes inactive to remove
     * @return int Number of participants removed
     */
    public function cleanupInactiveParticipants(int $minutesInactive = 30): int
    {
        $timeThreshold = date('Y-m-d H:i:s', time() - ($minutesInactive * 60));

        return $this->where('last_seen <', $timeThreshold)
            ->where('is_online', true)
            ->update([
                'is_online' => false,
                'left_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Get participant count per room
     *
     * @param string $chatRoom Chat room identifier
     * @return array Participant counts
     */
    public function getParticipantCounts(string $chatRoom): array
    {
        $db = \Config\Database::connect();

        $query = $db->query("
            SELECT
                COUNT(*) as total_participants,
                COUNT(CASE WHEN is_online = 1 THEN 1 END) as online_participants,
                COUNT(CASE WHEN participant_type = 'user' THEN 1 END) as user_count,
                COUNT(CASE WHEN participant_type = 'customer' THEN 1 END) as customer_count
            FROM {$this->table}
            WHERE chat_room = ?
            AND left_at IS NULL
        ", [$chatRoom]);

        return $query->getRowArray();
    }

    /**
     * Get rooms for a participant
     *
     * @param int|null $userId User ID
     * @param int|null $customerId Customer ID
     * @return array Room identifiers
     */
    public function getParticipantRooms(?int $userId = null, ?int $customerId = null): array
    {
        if ($userId === null && $customerId === null) {
            return [];
        }

        $builder = $this->select('chat_room')
            ->distinct()
            ->where('left_at', null);

        if ($userId !== null && $customerId !== null) {
            $builder->groupStart()
                ->where('user_id', $userId)
                ->orWhere('customer_id', $customerId)
                ->groupEnd();
        } elseif ($userId !== null) {
            $builder->where('user_id', $userId);
        } elseif ($customerId !== null) {
            $builder->where('customer_id', $customerId);
        }

        $results = $builder->find();

        return array_column($results, 'chat_room');
    }
}
