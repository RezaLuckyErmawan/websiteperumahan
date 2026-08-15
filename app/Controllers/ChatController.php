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

        // Get current user info
        $userId = session()->get('user_id');
        $customerId = session()->get('customer_id');
        $username = session()->get('username') ?? 'Customer';
        $userRole = session()->get('role') ?? 'customer';

        // Prepare message data
        $messageData = [
            'chat_room' => $chatRoom,
            'user_id' => $userId,
            'customer_id' => $customerId,
            'message' => $message,
            'message_type' => $messageType,
            'attachment_url' => $attachmentUrl,
            'sender_name' => $username,
            'sender_role' => $userRole,
            'reply_to_id' => $replyToId,
        ];

        // Save to database
        $messageId = $this->messageModel->insert($messageData);

        if ($messageId) {
            // Add/update participant if not exists
            $participantData = [
                'chat_room' => $chatRoom,
                'user_id' => $userId,
                'customer_id' => $customerId,
                'participant_name' => $username,
                'participant_type' => $customerId ? 'customer' : 'user',
                'role' => $userRole,
                'is_online' => true,
                'last_seen' => date('Y-m-d H:i:s'),
                'last_message_id' => $messageId,
            ];

            $this->participantModel->addParticipant($participantData);

            // Prepare data for Pusher
            $pusherData = [
                'id' => $messageId,
                'chat_room' => $chatRoom,
                'message' => $message,
                'message_type' => $messageType,
                'attachment_url' => $attachmentUrl,
                'user_id' => $userId,
                'sender_name' => $username,
                'sender_role' => $userRole,
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
        $unreadCount = $this->messageModel->getUnreadCount($room, session()->get('user_id'));

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
        $lastMessageId = $this->request->getPost('last_message_id');
        $userId = session()->get('user_id');

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $updated = $this->messageModel->markAsRead($chatRoom, $userId, $lastMessageId);

        // Update participant's last message
        if ($lastMessageId) {
            $this->participantModel->updateLastMessage($chatRoom, $userId, null, $lastMessageId);
        }

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

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $count = $this->messageModel->getUnreadCount($chatRoom, $userId);

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
        $userId = session()->get('user_id');
        $customerId = session()->get('customer_id');
        $username = session()->get('username') ?? 'Customer';
        $userRole = session()->get('role') ?? 'customer';

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $participantData = [
            'chat_room' => $chatRoom,
            'user_id' => $userId,
            'customer_id' => $customerId,
            'participant_name' => $username,
            'participant_type' => $customerId ? 'customer' : 'user',
            'role' => $userRole,
            'is_online' => true,
            'last_seen' => date('Y-m-d H:i:s'),
        ];

        $participantId = $this->participantModel->addParticipant($participantData);

        // Notify others in room
        $pusherData = [
            'chat_room' => $chatRoom,
            'participant_name' => $username,
            'participant_type' => $customerId ? 'customer' : 'user',
            'role' => $userRole,
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
        $isTyping = $this->request->getPost('is_typing') == 'true';
        $userId = session()->get('user_id');
        $customerId = session()->get('customer_id');

        if (!$chatRoom) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Chat room is required',
            ], 400);
        }

        $updated = $this->participantModel->updateTypingStatus($chatRoom, $userId, $customerId, $isTyping);

        if ($updated && $isTyping) {
            // Notify others about typing status
            $username = session()->get('username') ?? 'Customer';
            $pusherData = [
                'chat_room' => $chatRoom,
                'user_name' => $username,
                'timestamp' => time(),
            ];

            $this->sendToPusher($chatRoom, 'typing-indicator', $pusherData);
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
        $userId = session()->get('user_id');
        $limit = $this->request->getVar('limit') ?? 10;

        $conversations = $this->messageModel->getRecentConversations($userId, $limit);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $conversations,
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
                    'cluster' => $pusherConfig['cluster'],
                    'useTLS' => $pusherConfig['use_tls'],
                    'timeout' => $pusherConfig['timeout'] ?? 30,
                ]
            );

            // Trigger event
            $pusher->trigger($channel, $event, $data);

            log_message('info', "Pusher event sent: {$event} to {$channel}");
            return true;

        } catch (\Exception $e) {
            log_message('error', 'Pusher error: ' . $e->getMessage());
            return false;
        }
    }
}
