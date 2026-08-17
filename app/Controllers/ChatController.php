<?php

namespace App\Controllers;

use App\Models\MessageModel;
use App\Models\ChatParticipantModel;
use CodeIgniter\RESTful\ResourceController;

class ChatController extends ResourceController
{
    protected $messageModel;
    protected $participantModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->messageModel = new MessageModel();
        $this->participantModel = new ChatParticipantModel();
    }

    /**
     * Display chat interface
     * GET /chat
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'Chat - Sistem Manajemen Perumahan',
        ];

        return view('chat/index', $data);
    }

    /**
     * Send a message to a chat room
     * POST /chat/send
     */
    public function send()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'chat_room' => 'required|max_length[100]',
            'message' => 'required|max_length[5000]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors(),
            ], 400);
        }

        $chatRoom = $this->request->getPost('chat_room');
        $message = $this->request->getPost('message');
        $messageType = $this->request->getPost('message_type') ?? 'text';
        $replyToId = $this->request->getPost('reply_to_id');
        $attachmentUrl = $this->request->getPost('attachment_url');

        $identity = $this->currentChatIdentity();

        $messageData = [
            'chat_room' => $chatRoom,
            'user_id' => $identity['user_id'],
            'customer_id' => $identity['customer_id'],
            'message' => $message,
            'message_type' => $messageType,
            'attachment_url' => $attachmentUrl,
            'sender_name' => $identity['name'],
            'sender_role' => $identity['role'],
            'reply_to_id' => $replyToId,
        ];

        // Save to database
        $messageId = $this->messageModel->insert($messageData);

        if ($messageId) {
            // Add/update participant if not exists
            $this->participantModel->addParticipant([
                'chat_room' => $chatRoom,
                'user_id' => $identity['user_id'],
                'customer_id' => $identity['customer_id'],
                'participant_name' => $identity['name'],
                'participant_type' => $identity['participant_type'],
                'role' => $identity['role'],
                'is_online' => true,
                'last_seen' => date('Y-m-d H:i:s'),
                'last_message_id' => $messageId,
            ]);
            $this->ensureCustomerRoomParticipant($chatRoom);

            // Prepare data for Pusher
            $pusherData = [
                'id' => $messageId,
                'chat_room' => $chatRoom,
                'message' => $message,
                'message_type' => $messageType,
                'attachment_url' => $attachmentUrl,
                'user_id' => $identity['user_id'],
                'sender_name' => $identity['name'],
                'sender_role' => $identity['role'],
                'reply_to_id' => $replyToId,
                'timestamp' => time(),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Try to send via Pusher (if configured)
            $pusherSent = $this->sendToPusher($chatRoom, 'new-message', $pusherData);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Message sent successfully',
                'data' => $pusherData,
                'pusher_sent' => $pusherSent,
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to send message',
        ], 500);
    }

    /**
     * Get message history for a chat room
     * GET /chat/history/{room}
     */
    /**
     * Upload an attachment for a chat message
     * POST /chat/upload
     */
    public function upload()
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak ada file yang dikirim atau file tidak valid.',
            ], 400);
        }

        $allowedMime = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMime, true)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tipe file tidak diizinkan.',
            ], 400);
        }

        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'chat';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $storedName = $file->getRandomName();
        $file->move($uploadPath, $storedName);

        $attachmentType = str_starts_with($mimeType, 'image/') ? 'image' : 'file';
        $originalName = $file->getName();
        $url = base_url('uploads/chat/' . $storedName);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'filename' => $storedName,
                'original_name' => $originalName,
                'type' => $attachmentType,
                'url' => $url,
                'mime_type' => $mimeType,
            ],
        ]);
    }

    public function history($room = null)
    {
        if (!$room) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $limit = $this->request->getVar('limit') ?? 50;
        $offset = $this->request->getVar('offset') ?? 0;

        $messages = $this->messageModel->getMessagesByRoom($room, $limit, $offset);
        $unreadCount = $this->messageModel->getUnreadCount(
            $room,
            session()->get('user_id'),
            session()->get('customer_id')
        );

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $messages,
            'unread_count' => $unreadCount,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /**
     * Mark messages as read
     * POST /chat/read
     */
    public function markAsRead()
    {
        $chatRoom = $this->request->getPost('chat_room');
        $identity = $this->currentChatIdentity();

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $lastMessage = $this->messageModel
            ->where('chat_room', $chatRoom)
            ->where('deleted_at', null)
            ->orderBy('id', 'DESC')
            ->first();

        $lastMessageId = $lastMessage['id'] ?? $this->request->getPost('last_message_id');
        if (!$lastMessageId) {
            return $this->response->setJSON([
                'status' => 'success',
                'updated' => 0,
            ]);
        }

        $updated = $this->participantModel->updateLastMessage(
            $chatRoom,
            $identity['user_id'],
            $identity['customer_id'],
            (int) $lastMessageId
        );

        return $this->response->setJSON([
            'status' => 'success',
            'updated' => $updated,
        ]);
    }

    /**
     * Get unread message count
     * GET /chat/unread
     */
    public function unread()
    {
        $chatRoom = $this->request->getVar('chat_room');
        $userId = session()->get('user_id');
        $customerId = session()->get('customer_id');

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $count = $this->messageModel->getUnreadCount($chatRoom, $userId, $customerId);

        return $this->response->setJSON([
            'status' => 'success',
            'unread_count' => $count,
        ]);
    }

    /**
     * Join a chat room
     * POST /chat/join
     */
    public function join()
    {
        $chatRoom = $this->request->getPost('chat_room');
        $identity = $this->currentChatIdentity();

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $participantId = $this->participantModel->addParticipant([
            'chat_room' => $chatRoom,
            'user_id' => $identity['user_id'],
            'customer_id' => $identity['customer_id'],
            'participant_name' => $identity['name'],
            'participant_type' => $identity['participant_type'],
            'role' => $identity['role'],
            'is_online' => true,
            'last_seen' => date('Y-m-d H:i:s'),
        ]);
        $this->ensureCustomerRoomParticipant($chatRoom);

        $lastMessage = $this->messageModel
            ->where('chat_room', $chatRoom)
            ->where('deleted_at', null)
            ->orderBy('id', 'DESC')
            ->first();
        if (!empty($lastMessage['id'])) {
            $this->participantModel->updateLastMessage(
                $chatRoom,
                $identity['user_id'],
                $identity['customer_id'],
                (int) $lastMessage['id']
            );
        }

        $pusherData = [
            'chat_room' => $chatRoom,
            'participant_name' => $identity['name'],
            'participant_type' => $identity['participant_type'],
            'role' => $identity['role'],
            'timestamp' => time(),
        ];

        $this->sendToPusher($chatRoom, 'user-joined', $pusherData);

        // Get room info
        $participants = $this->participantModel->getRoomParticipants($chatRoom);
        $messages = $this->messageModel->getMessagesByRoom($chatRoom, 50);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Joined chat room successfully',
            'data' => [
                'participant_id' => $participantId,
                'participants' => $participants,
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * Leave a chat room
     * POST /chat/leave
     */
    public function leave()
    {
        $chatRoom = $this->request->getPost('chat_room');
        $userId = session()->get('user_id');
        $customerId = session()->get('customer_id');

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $removed = $this->participantModel->removeParticipant($chatRoom, $userId, $customerId);

        // Notify others
        $username = session()->get('username') ?? 'Customer';
        $pusherData = [
            'chat_room' => $chatRoom,
            'participant_name' => $username,
            'timestamp' => time(),
        ];

        $this->sendToPusher($chatRoom, 'user-left', $pusherData);

        return $this->response->setJSON([
            'status' => 'success',
            'removed' => $removed,
        ]);
    }

    /**
     * Update typing status
     * POST /chat/typing
     */
    public function typing()
    {
        $chatRoom = $this->request->getPost('chat_room');
        $isTypingRaw = $this->request->getPost('is_typing');
        $isTyping = $isTypingRaw === true || $isTypingRaw === 1 || $isTypingRaw === '1' || $isTypingRaw === 'true';
        $identity = $this->currentChatIdentity();

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $updated = $this->participantModel->updateTypingStatus(
            $chatRoom,
            $identity['user_id'],
            $identity['customer_id'],
            $isTyping
        );

        if ($updated && $isTyping) {
            $this->sendToPusher($chatRoom, 'typing-indicator', [
                'chat_room' => $chatRoom,
                'user_id' => $identity['user_id'],
                'user_name' => $identity['name'],
                'timestamp' => time(),
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'is_typing' => $isTyping,
        ]);
    }

    /**
     * Get typing users in a room
     * GET /chat/typing-users/{room}
     */
    public function typingUsers($room = null)
    {
        if (!$room) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $typingUsers = $this->messageModel->getTypingUsers($room);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $typingUsers,
        ]);
    }

    /**
     * Get recent conversations
     * GET /chat/conversations
     */
    public function conversations()
    {
        $identity = $this->currentChatIdentity();
        $limit = $this->request->getVar('limit') ?? (in_array($identity['role'], ['admin', 'owner', 'mandor', 'spv'], true) ? 50 : 10);

        $conversations = $this->messageModel->getRecentConversations(
            $identity['user_id'],
            $identity['customer_id'],
            (int) $limit,
            $identity['role']
        );

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $conversations,
        ]);
    }

    /**
     * Get navbar notification summary.
     * GET /chat/notifications
     */
    public function notifications()
    {
        $identity = $this->currentChatIdentity();
        $limit = (int) ($this->request->getVar('limit') ?? 15);
        $limit = $limit > 0 ? $limit : 15;

        $conversations = $this->messageModel->getRecentConversations(
            $identity['user_id'],
            $identity['customer_id'],
            $limit,
            $identity['role']
        );

        $items = [];
        $totalUnread = 0;

        foreach ($conversations as $conversation) {
            $unreadCount = (int) ($conversation['unread_count'] ?? 0);
            $totalUnread += $unreadCount;

            if ($unreadCount <= 0) {
                continue;
            }

            $items[] = [
                'chat_room' => (string) ($conversation['chat_room'] ?? ''),
                'room_label' => $this->formatChatRoomLabel((string) ($conversation['chat_room'] ?? '')),
                'last_message' => (string) ($conversation['last_message'] ?? ''),
                'last_message_at' => $conversation['last_message_at'] ?? null,
                'unread_count' => $unreadCount,
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'unread_count' => $totalUnread,
                'items' => $items,
            ],
        ]);
    }

    /**
     * Get participants in a room
     * GET /chat/participants/{room}
     */
    public function participants($room = null)
    {
        if (!$room) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $onlineOnly = $this->request->getVar('online_only') == 'true';
        $participants = $this->participantModel->getRoomParticipants($room, $onlineOnly);
        $counts = $this->participantModel->getParticipantCounts($room);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'participants' => $participants,
                'counts' => $counts,
            ],
        ]);
    }

    /**
     * Update online status
     * POST /chat/online-status
     */
    public function onlineStatus()
    {
        $chatRoom = $this->request->getPost('chat_room');
        $isOnline = $this->request->getPost('is_online') == 'true';
        $userId = session()->get('user_id');
        $customerId = session()->get('customer_id');

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $updated = $this->participantModel->updateOnlineStatus($chatRoom, $userId, $customerId, $isOnline);

        return $this->response->setJSON([
            'status' => 'success',
            'is_online' => $isOnline,
        ]);
    }

    /**
     * Search messages
     * GET /chat/search
     */
    public function search()
    {
        $chatRoom = $this->request->getVar('chat_room');
        $query = $this->request->getVar('q');
        $limit = $this->request->getVar('limit') ?? 20;

        if (!$chatRoom || !$query) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room and search query are required',
            ], 400);
        }

        $results = $this->messageModel->searchMessages($chatRoom, $query, $limit);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $results,
            'query' => $query,
        ]);
    }

    /**
     * Get message statistics
     * GET /chat/stats/{room}
     */
    public function stats($room = null)
    {
        if (!$room) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $stats = $this->messageModel->getMessageStats($room);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    /**
     * @return array{user_id: int|null, customer_id: int|null, name: string, role: string, participant_type: string}
     */
    private function currentChatIdentity(): array
    {
        $role = (string) (session()->get('role') ?? 'user');
        $isCustomer = $role === 'customer';
        $customerId = $isCustomer ? session()->get('customer_id') : null;

        return [
            'user_id' => session()->get('user_id') ? (int) session()->get('user_id') : null,
            'customer_id' => $customerId ? (int) $customerId : null,
            'name' => (string) (session()->get('nama') ?: session()->get('username') ?: 'User'),
            'role' => $role,
            'participant_type' => $isCustomer ? 'customer' : 'user',
        ];
    }

    private function ensureCustomerRoomParticipant(string $chatRoom): void
    {
        if (!preg_match('/^customer-(\d+)$/', $chatRoom, $matches)) {
            return;
        }

        $customerId = (int) $matches[1];
        $customer = (new \App\Models\CustomerModel())->find($customerId);
        if (!$customer) {
            return;
        }

        $linkedUser = (new \App\Models\UserModel())->where('customer_id', $customerId)->first();

        $this->participantModel->addParticipant([
            'chat_room' => $chatRoom,
            'user_id' => $linkedUser['id'] ?? null,
            'customer_id' => $customerId,
            'participant_name' => $customer['nama'],
            'participant_type' => 'customer',
            'role' => 'customer',
            'is_online' => false,
            'last_seen' => date('Y-m-d H:i:s'),
        ]);
    }

    private function formatChatRoomLabel(string $chatRoom): string
    {
        if ($chatRoom === '') {
            return 'Chat';
        }

        if (preg_match('/^customer-(\d+)$/', $chatRoom, $matches)) {
            return 'Customer #' . $matches[1];
        }

        $label = str_replace(['-', '_'], ' ', $chatRoom);
        return ucwords(trim($label));
    }

    /**
     * Send data via Pusher
     *
     * @param string $channel Channel name
     * @param string $event Event name
     * @param array $data Data to send
     * @return bool Success status
     */
    private function sendToPusher(string $channel, string $event, array $data): bool
    {
        try {
            if (!class_exists('\\Pusher\\Pusher')) {
                log_message('warning', 'Pusher PHP SDK is not installed. Skipping real-time notification.');
                return false;
            }

            // Check if Pusher is configured
            $pusherConfig = config('Pusher')->config;

            if (empty($pusherConfig['app_id']) || empty($pusherConfig['key']) || empty($pusherConfig['secret'])) {
                log_message('warning', 'Pusher not configured. Skipping real-time notification.');
                return false;
            }

            // Initialize Pusher
            $pusher = new \Pusher\Pusher(
                $pusherConfig['key'],
                $pusherConfig['secret'],
                $pusherConfig['app_id'],
                [
                    'cluster' => $pusherConfig['cluster'] ?? 'mt1',
                    'useTLS' => (bool) ($pusherConfig['use_tls'] ?? true),
                    'timeout' => (int) ($pusherConfig['timeout'] ?? 30),
                ]
            );

            // Trigger event
            $pusher->trigger(
                str_starts_with($channel, 'chat-') ? $channel : 'chat-' . $channel,
                $event,
                $data
            );

            log_message('info', "Pusher event sent: {$event} to {$channel}");
            return true;

        } catch (\Exception $e) {
            log_message('error', 'Pusher error: ' . $e->getMessage());
            return false;
        }
    }
}
