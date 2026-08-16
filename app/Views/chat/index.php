<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<!-- Chat Custom Styles -->
<link rel="stylesheet" href="<?= base_url('assets/css/chat.css') ?>">
<style>
    .content1 {
        display: flex;
        flex-direction: column;
        padding: 0;
        min-height: 0;
        background: #fff;
    }

    .chat-container {
        width: 100%;
        flex: 1;
        height: 100%;
        max-height: none;
        margin: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .chat-sidebar,
    .chat-main {
        border-radius: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Chat Interface -->
<div class="chat-container">
        <!-- Chat Sidebar -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="sidebar-header">
                <h5>Conversations</h5>
                <button class="btn btn-sm btn-primary new-chat-btn">
                    <i class="fas fa-plus"></i> New Chat
                </button>
            </div>
            <div class="sidebar-search">
                <input type="text" class="form-control" placeholder="Search conversations..." id="searchConversations">
            </div>
            <div class="conversations-list" id="conversationsList">
                <!-- Conversations will be loaded here -->
            </div>
        </div>

        <!-- Chat Main Area -->
        <div class="chat-main">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-room-info">
                    <button class="btn btn-sm btn-light toggle-sidebar d-md-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h6 id="chatRoomTitle">Select a conversation</h6>
                    <small id="chatRoomParticipants" class="text-muted"></small>
                </div>
                <div class="chat-header-actions">
                    <button class="btn btn-sm btn-light" id="searchMessagesBtn" title="Search messages">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-sm btn-light" id="chatInfoBtn" title="Chat info">
                        <i class="fas fa-info-circle"></i>
                    </button>
                    <button class="btn btn-sm btn-light" id="leaveChatBtn" title="Leave chat" style="display: none;">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Search Messages Panel -->
            <div class="search-panel" id="searchPanel" style="display: none;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search messages..." id="searchMessagesInput">
                    <button class="btn btn-outline-secondary" type="button" id="closeSearchPanel">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="search-results" id="searchResults"></div>
            </div>

            <!-- Chat Messages Area -->
            <div class="chat-messages" id="chatMessages">
                <div class="empty-state">
                    <i class="fas fa-comments fa-3x text-muted"></i>
                    <p class="text-muted">Select a conversation to start chatting</p>
                </div>
            </div>

            <!-- Typing Indicator -->
            <div class="typing-indicator" id="typingIndicator" style="display: none;">
                <div class="typing-content">
                    <span class="typing-text"></span>
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>

            <!-- Chat Input Area -->
            <div class="chat-input-area" id="chatInputArea">
                <div class="input-group">
                    <button class="btn btn-outline-secondary" id="attachFileBtn" title="Attach file">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <input type="text" class="form-control" placeholder="Type a message..." id="messageInput" disabled>
                    <button class="btn btn-outline-secondary" id="emojiBtn" title="Add emoji">
                        <i class="fas fa-smile"></i>
                    </button>
                    <button class="btn btn-primary" id="sendMessageBtn" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="chat-input-footer">
                    <small class="text-muted">Press Enter to send, Shift+Enter for new line</small>
                    <input type="file" id="fileInput" style="display: none;" accept="image/*,.pdf,.doc,.docx">
                </div>
            </div>

            <!-- Unread Badge -->
            <div class="unread-badge" id="unreadBadge" style="display: none;">
                <span class="badge bg-danger" id="unreadCount">0</span>
            </div>
        </div>

        <!-- Chat Info Panel -->
        <div class="chat-info-panel" id="chatInfoPanel" style="display: none;">
            <div class="panel-header">
                <h6>Chat Information</h6>
                <button class="btn btn-sm btn-light" id="closeInfoPanel">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="panel-content">
                <div class="info-section">
                    <h6>Participants</h6>
                    <div id="participantsList" class="participants-list"></div>
                </div>
                <div class="info-section">
                    <h6>Statistics</h6>
                    <div id="chatStats" class="chat-stats"></div>
                </div>
            </div>
        </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Pusher JS Library -->
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<!-- Chat Application -->
<script src="<?= base_url('assets/js/chat.js') ?>"></script>

<script>
    // Initialize chat when document is ready
    $(document).ready(function() {
        ChatApp.init({
            pusherKey: <?= json_encode(config('Pusher')->config['key'] ?? '') ?>,
            pusherCluster: <?= json_encode(config('Pusher')->config['cluster'] ?? 'mt1') ?>,
            currentUserId: <?= json_encode(session()->get('user_id')) ?>,
            currentUserName: <?= json_encode(session()->get('nama') ?: session()->get('username')) ?>,
            currentUserRole: <?= json_encode(session()->get('role')) ?>,
            customerId: <?= json_encode(session()->get('customer_id')) ?>,
        });
    });
</script>
<?= $this->endSection() ?>
