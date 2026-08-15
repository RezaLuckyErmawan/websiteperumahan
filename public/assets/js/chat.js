/**
 * Chat Application
 * Handles real-time chat functionality with Pusher integration
 */

const ChatApp = {
    // Configuration
    config: {
        pusherKey: '',
        pusherCluster: 'mt1',
        currentUserId: null,
        currentUserName: '',
        currentUserRole: '',
        apiBaseUrl: '/chat',
    },

    // State
    state: {
        currentRoom: null,
        pusher: null,
        channel: null,
        isConnected: false,
        participants: [],
        unreadCount: 0,
        typingTimer: null,
        reconnectAttempts: 0,
        maxReconnectAttempts: 5,
    },

    // Initialize chat application
    init: function(options) {
        // Merge options with config
        Object.assign(this.config, options);

        // Check if Pusher is available
        if (typeof Pusher === 'undefined') {
            console.error('Pusher library not loaded');
            this.showError('Chat service unavailable. Please refresh the page.');
            return;
        }

        // Initialize Pusher
        this.initializePusher();

        // Setup event listeners
        this.setupEventListeners();

        // Load initial conversations
        this.loadConversations();

        console.log('ChatApp initialized');
    },

    // Initialize Pusher
    initializePusher: function() {
        try {
            this.state.pusher = new Pusher(this.config.pusherKey, {
                cluster: this.config.pusherCluster,
                encrypted: true,
                authEndpoint: '/chat/auth', // Endpoint for private channels
            });

            // Handle connection state
            this.state.pusher.connection.bind('connected', () => {
                console.log('Pusher connected');
                this.state.isConnected = true;
                this.state.reconnectAttempts = 0;
                this.showConnectionStatus();
            });

            this.state.pusher.connection.bind('disconnected', () => {
                console.log('Pusher disconnected');
                this.state.isConnected = false;
                this.showConnectionStatus();
            });

            this.state.pusher.connection.bind('error', (err) => {
                console.error('Pusher error:', err);
                this.handleConnectionError();
            });

        } catch (error) {
            console.error('Failed to initialize Pusher:', error);
            this.showError('Failed to initialize chat service');
        }
    },

    // Setup event listeners
    setupEventListeners: function() {
        const self = this;

        // Message input
        $('#messageInput').on('keypress', function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                self.sendMessage();
            }
        });

        // Send message button
        $('#sendMessageBtn').on('click', function() {
            self.sendMessage();
        });

        // Typing indicator
        $('#messageInput').on('input', function() {
            self.sendTypingIndicator(true);

            // Clear previous timer
            if (self.state.typingTimer) {
                clearTimeout(self.state.typingTimer);
            }

            // Set new timer to stop typing after 2 seconds of inactivity
            self.state.typingTimer = setTimeout(function() {
                self.sendTypingIndicator(false);
            }, 2000);
        });

        // Join chat room
        $(document).on('click', '.conversation-item', function() {
            const room = $(this).data('room');
            self.joinChatRoom(room);
        });

        // Attach file
        $('#attachFileBtn').on('click', function() {
            $('#fileInput').click();
        });

        // File input change
        $('#fileInput').on('change', function() {
            self.handleFileUpload(this.files[0]);
        });

        // Leave chat
        $('#leaveChatBtn').on('click', function() {
            self.leaveChatRoom();
        });

        // Search messages
        $('#searchMessagesBtn').on('click', function() {
            $('#searchPanel').slideToggle();
            $('#searchMessagesInput').focus();
        });

        $('#closeSearchPanel').on('click', function() {
            $('#searchPanel').slideUp();
        });

        $('#searchMessagesInput').on('input', function() {
            const query = $(this).val();
            if (query.length >= 2) {
                self.searchMessages(query);
            }
        });

        // Chat info panel
        $('#chatInfoBtn').on('click', function() {
            self.toggleChatInfo();
        });

        $('#closeInfoPanel').on('click', function() {
            $('#chatInfoPanel').hide();
        });

        // Toggle sidebar on mobile
        $('.toggle-sidebar').on('click', function() {
            $('#chatSidebar').toggleClass('show');
        });

        // Search conversations
        $('#searchConversations').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('.conversation-item').each(function() {
                const name = $(this).find('.conversation-name').text().toLowerCase();
                const match = name.includes(query);
                $(this).toggle(match);
            });
        });

        // New chat button (placeholder)
        $('.new-chat-btn').on('click', function() {
            self.showNewChatDialog();
        });
    },

    // Load conversations
    loadConversations: function() {
        const self = this;

        $.ajax({
            url: this.config.apiBaseUrl + '/conversations',
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    self.renderConversations(response.data);
                }
            },
            error: function() {
                console.error('Failed to load conversations');
            }
        });
    },

    // Render conversations list
    renderConversations: function(conversations) {
        const $list = $('#conversationsList');
        $list.empty();

        if (conversations.length === 0) {
            $list.html(`
                <div class="text-center text-muted p-3">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>No conversations yet</p>
                </div>
            `);
            return;
        }

        conversations.forEach(function(conv) {
            const unreadBadge = conv.unread_count > 0
                ? `<span class="unread-badge-conversation">${conv.unread_count}</span>`
                : '';

            const lastMessage = conv.last_message
                ? `<div class="conversation-last-message">${conv.last_message}</div>`
                : '<div class="conversation-last-message">No messages yet</div>';

            const item = `
                <div class="conversation-item" data-room="${conv.chat_room}">
                    <div class="conversation-avatar">
                        ${conv.chat_room.charAt(0).toUpperCase()}
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-name">
                            ${conv.chat_room}
                            ${unreadBadge}
                        </div>
                        ${lastMessage}
                    </div>
                    <div class="conversation-meta">
                        ${self.formatTimestamp(conv.last_message_at)}
                    </div>
                </div>
            `;

            $list.append(item);
        });
    },

    // Join a chat room
    joinChatRoom: function(room) {
        const self = this;

        // Leave current room if any
        if (this.state.currentRoom && this.state.channel) {
            this.leaveChatRoom();
        }

        // Show loading state
        this.showLoadingState();

        $.ajax({
            url: this.config.apiBaseUrl + '/join',
            method: 'POST',
            data: {
                chat_room: room,
            },
            success: function(response) {
                if (response.status === 'success') {
                    self.state.currentRoom = room;

                    // Subscribe to Pusher channel
                    self.subscribeToChannel(room);

                    // Update UI
                    self.updateChatRoomUI(room, response.data);

                    // Enable input
                    self.enableChatInput();

                    // Mark as read
                    self.markMessagesAsRead();

                    console.log('Joined room:', room);
                }
            },
            error: function() {
                self.showError('Failed to join chat room');
            }
        });
    },

    // Subscribe to Pusher channel
    subscribeToChannel: function(room) {
        const self = this;

        // Unsubscribe from previous channel
        if (this.state.channel) {
            this.state.pusher.unsubscribe(this.state.channel.name);
        }

        // Subscribe to new channel
        this.state.channel = this.state.pusher.subscribe(`chat-${room}`);

        // Bind to events
        this.state.channel.bind('new-message', function(data) {
            self.handleNewMessage(data);
        });

        this.state.channel.bind('typing-indicator', function(data) {
            self.handleTypingIndicator(data);
        });

        this.state.channel.bind('user-joined', function(data) {
            self.handleUserJoined(data);
        });

        this.state.channel.bind('user-left', function(data) {
            self.handleUserLeft(data);
        });

        console.log('Subscribed to channel:', `chat-${room}`);
    },

    // Send a message
    sendMessage: function() {
        const message = $('#messageInput').val().trim();
        const replyToId = $('.message.reply-to').data('message-id');

        if (!message || !this.state.currentRoom) return;

        const self = this;

        $.ajax({
            url: this.config.apiBaseUrl + '/send',
            method: 'POST',
            data: {
                chat_room: this.state.currentRoom,
                message: message,
                reply_to_id: replyToId,
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#messageInput').val('');

                    // Remove reply indicator if present
                    $('.message.reply-to').removeClass('reply-to');

                    console.log('Message sent:', response.data);
                }
            },
            error: function() {
                self.showError('Failed to send message');
            }
        });
    },

    // Handle new message from Pusher
    handleNewMessage: function(data) {
        // Don't add own messages (they're added immediately when sent)
        if (data.user_id === this.config.currentUserId) return;

        // Add message to UI
        this.addMessageToUI(data);

        // Update unread count if not in current room
        if (data.chat_room !== this.state.currentRoom) {
            this.updateUnreadCount(1);
        }
    },

    // Add message to UI
    addMessageToUI: function(messageData) {
        const isSent = messageData.user_id === this.config.currentUserId;
        const messageClass = isSent ? 'message sent' : 'message';

        const avatar = messageData.sender_name.charAt(0).toUpperCase();

        let contentHtml = '';

        if (messageData.message_type === 'text') {
            contentHtml = `<div class="message-text">${this.escapeHtml(messageData.message)}</div>`;
        } else if (messageData.message_type === 'image' && messageData.attachment_url) {
            contentHtml = `
                <img src="${messageData.attachment_url}" class="message-image" alt="Image">
                <div class="message-text">${this.escapeHtml(messageData.message)}</div>
            `;
        } else if (messageData.message_type === 'file' && messageData.attachment_url) {
            contentHtml = `
                <div class="message-file">
                    <i class="fas fa-file"></i>
                    <a href="${messageData.attachment_url}" target="_blank">View File</a>
                </div>
                <div class="message-text">${this.escapeHtml(messageData.message)}</div>
            `;
        }

        const messageHtml = `
            <div class="${messageClass}" data-message-id="${messageData.id}">
                <div class="message-avatar">${avatar}</div>
                <div class="message-content">
                    <div class="message-sender">${messageData.sender_name}</div>
                    ${contentHtml}
                    <div class="message-meta">
                        ${this.formatTimestamp(messageData.created_at)}
                    </div>
                </div>
            </div>
        `;

        $('#chatMessages').append(messageHtml);
        this.scrollToBottom();
    },

    // Send typing indicator
    sendTypingIndicator: function(isTyping) {
        if (!this.state.currentRoom) return;

        $.ajax({
            url: this.config.apiBaseUrl + '/typing',
            method: 'POST',
            data: {
                chat_room: this.state.currentRoom,
                is_typing: isTyping,
            },
            error: function() {
                console.error('Failed to send typing indicator');
            }
        });
    },

    // Handle typing indicator from Pusher
    handleTypingIndicator: function(data) {
        if (data.user_name === this.config.currentUserName) return;

        const $indicator = $('#typingIndicator');
        const $text = $indicator.find('.typing-text');

        $text.text(`${data.user_name} is typing...`);
        $indicator.show();

        // Hide after 3 seconds
        setTimeout(function() {
            $indicator.hide();
        }, 3000);
    },

    // Handle user joined event
    handleUserJoined: function(data) {
        if (data.participant_name === this.config.currentUserName) return;

        this.showSystemMessage(`${data.participant_name} joined the chat`);
        this.loadParticipants();
    },

    // Handle user left event
    handleUserLeft: function(data) {
        if (data.participant_name === this.config.currentUserName) return;

        this.showSystemMessage(`${data.participant_name} left the chat`);
        this.loadParticipants();
    },

    // Leave chat room
    leaveChatRoom: function() {
        const self = this;

        if (!this.state.currentRoom) return;

        $.ajax({
            url: this.config.apiBaseUrl + '/leave',
            method: 'POST',
            data: {
                chat_room: this.state.currentRoom,
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Unsubscribe from channel
                    if (self.state.channel) {
                        self.state.pusher.unsubscribe(`chat-${self.state.currentRoom}`);
                        self.state.channel = null;
                    }

                    self.state.currentRoom = null;

                    // Reset UI
                    self.resetChatUI();

                    console.log('Left chat room');
                }
            },
            error: function() {
                console.error('Failed to leave chat room');
            }
        });
    },

    // Mark messages as read
    markMessagesAsRead: function() {
        if (!this.state.currentRoom) return;

        $.ajax({
            url: this.config.apiBaseUrl + '/read',
            method: 'POST',
            data: {
                chat_room: this.state.currentRoom,
            },
            success: function(response) {
                if (response.status === 'success') {
                    console.log('Messages marked as read');
                }
            }
        });
    },

    // Search messages
    searchMessages: function(query) {
        const self = this;

        $.ajax({
            url: this.config.apiBaseUrl + '/search',
            method: 'GET',
            data: {
                chat_room: this.state.currentRoom,
                q: query,
            },
            success: function(response) {
                if (response.status === 'success') {
                    self.renderSearchResults(response.data);
                }
            }
        });
    },

    // Render search results
    renderSearchResults: function(results) {
        const $results = $('#searchResults');
        $results.empty();

        if (results.length === 0) {
            $results.html('<div class="text-muted">No results found</div>');
            return;
        }

        results.forEach(function(result) {
            const item = `
                <div class="search-result-item" data-message-id="${result.id}">
                    <div class="message-sender">${result.sender_name}</div>
                    <div class="message-text">${result.message}</div>
                    <div class="message-meta">${self.formatTimestamp(result.created_at)}</div>
                </div>
            `;
            $results.append(item);
        });
    },

    // Load participants
    loadParticipants: function() {
        const self = this;

        if (!this.state.currentRoom) return;

        $.ajax({
            url: this.config.apiBaseUrl + '/participants/' + this.state.currentRoom,
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    self.renderParticipants(response.data);
                }
            }
        });
    },

    // Render participants
    renderParticipants: function(data) {
        const $list = $('#participantsList');
        $list.empty();

        data.participants.forEach(function(participant) {
            const onlineDot = participant.is_online
                ? '<span class="online-dot"></span>'
                : '';

            const item = `
                <div class="participant-item">
                    <div class="participant-avatar">
                        ${participant.participant_name.charAt(0).toUpperCase()}
                    </div>
                    <div class="participant-info">
                        <div class="participant-name">
                            ${participant.participant_name}
                            ${onlineDot}
                        </div>
                        <div class="participant-status">
                            ${participant.is_online ? 'Online' : 'Offline'}
                        </div>
                    </div>
                </div>
            `;

            $list.append(item);
        });

        // Update counts
        $('#chatRoomParticipants').text(`${data.counts.online_participants} online`);
    },

    // Toggle chat info panel
    toggleChatInfo: function() {
        const $panel = $('#chatInfoPanel');

        if ($panel.is(':visible')) {
            $panel.hide();
        } else {
            this.loadParticipants();
            this.loadChatStats();
            $panel.show();
        }
    },

    // Load chat statistics
    loadChatStats: function() {
        const self = this;

        if (!this.state.currentRoom) return;

        $.ajax({
            url: this.config.apiBaseUrl + '/stats/' + this.state.currentRoom,
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    self.renderStats(response.data);
                }
            }
        });
    },

    // Render statistics
    renderStats: function(stats) {
        const $stats = $('#chatStats');
        $stats.html(`
            <div class="stat-item">
                <span>Total Messages:</span>
                <span>${stats.total_messages}</span>
            </div>
            <div class="stat-item">
                <span>Unread:</span>
                <span>${stats.unread_messages}</span>
            </div>
            <div class="stat-item">
                <span>Images:</span>
                <span>${stats.image_count}</span>
            </div>
            <div class="stat-item">
                <span>Files:</span>
                <span>${stats.file_count}</span>
            </div>
        `);
    },

    // Update chat room UI
    updateChatRoomUI: function(room, data) {
        $('#chatRoomTitle').text(room);
        $('#leaveChatBtn').show();

        // Clear empty state
        $('#chatMessages').find('.empty-state').remove();

        // Load existing messages
        this.loadMessageHistory(room);

        // Update conversations list to show active
        $('.conversation-item').removeClass('active');
        $(`.conversation-item[data-room="${room}"]`).addClass('active');

        // Load participants
        this.renderParticipants(data);
    },

    // Load message history
    loadMessageHistory: function(room) {
        const self = this;

        $.ajax({
            url: this.config.apiBaseUrl + '/history/' + room,
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    $('#chatMessages').empty();

                    response.data.forEach(function(message) {
                        self.addMessageToUI(message);
                    });

                    self.scrollToBottom();
                }
            }
        });
    },

    // Reset chat UI
    resetChatUI: function() {
        $('#chatRoomTitle').text('Select a conversation');
        $('#chatRoomParticipants').text('');
        $('#leaveChatBtn').hide();
        $('#chatMessages').html(`
            <div class="empty-state">
                <i class="fas fa-comments fa-3x text-muted"></i>
                <p class="text-muted">Select a conversation to start chatting</p>
            </div>
        `);
        this.disableChatInput();
    },

    // Enable chat input
    enableChatInput: function() {
        $('#messageInput').prop('disabled', false).focus();
        $('#sendMessageBtn').prop('disabled', false);
    },

    // Disable chat input
    disableChatInput: function() {
        $('#messageInput').prop('disabled', true);
        $('#sendMessageBtn').prop('disabled', true);
    },

    // Handle file upload
    handleFileUpload: function(file) {
        if (!file || !this.state.currentRoom) return;

        const self = this;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('chat_room', this.state.currentRoom);

        this.showLoadingState();

        $.ajax({
            url: this.config.apiBaseUrl + '/upload',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    // Send message with attachment
                    $.ajax({
                        url: self.config.apiBaseUrl + '/send',
                        method: 'POST',
                        data: {
                            chat_room: self.state.currentRoom,
                            message: response.data.filename,
                            message_type: response.data.type,
                            attachment_url: response.data.url,
                        },
                        success: function(sendResponse) {
                            if (sendResponse.status === 'success') {
                                console.log('File sent successfully');
                            }
                        }
                    });
                }
            },
            error: function() {
                self.showError('Failed to upload file');
            },
            complete: function() {
                $('#fileInput').val('');
            }
        });
    },

    // Update unread count
    updateUnreadCount: function(count) {
        this.state.unreadCount += count;

        const $badge = $('#unreadBadge');
        const $count = $('#unreadCount');

        if (this.state.unreadCount > 0) {
            $badge.show();
            $count.text(this.state.unreadCount);
        } else {
            $badge.hide();
        }
    },

    // Handle connection error
    handleConnectionError: function() {
        if (this.state.reconnectAttempts < this.state.maxReconnectAttempts) {
            this.state.reconnectAttempts++;
            console.log(`Reconnection attempt ${this.state.reconnectAttempts}`);

            setTimeout(() => {
                this.initializePusher();
            }, 5000 * this.state.reconnectAttempts);
        } else {
            this.showError('Connection lost. Please refresh the page.');
        }
    },

    // Show connection status
    showConnectionStatus: function() {
        const $status = $('.connection-status');

        if (this.state.isConnected) {
            $status
                .removeClass('disconnected')
                .addClass('connected')
                .attr('title', 'Connected');
        } else {
            $status
                .removeClass('connected')
                .addClass('disconnected')
                .attr('title', 'Disconnected');
        }
    },

    // Show loading state
    showLoadingState: function() {
        $('#chatMessages').html(`
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
    },

    // Show error message
    showError: function(message) {
        // Simple alert for now, could be enhanced with toast notifications
        alert(message);
    },

    // Show system message
    showSystemMessage: function(message) {
        const html = `
            <div class="system-message">
                <div class="message-text">${this.escapeHtml(message)}</div>
            </div>
        `;

        $('#chatMessages').append(html);
        this.scrollToBottom();
    },

    // Show new chat dialog (placeholder)
    showNewChatDialog: function() {
        const room = prompt('Enter chat room name:');
        if (room) {
            this.joinChatRoom(room);
        }
    },

    // Scroll to bottom of messages
    scrollToBottom: function() {
        const $messages = $('#chatMessages');
        $messages.scrollTop($messages[0].scrollHeight);
    },

    // Format timestamp
    formatTimestamp: function(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;

        // Less than a minute
        if (diff < 60000) {
            return 'Just now';
        }

        // Less than an hour
        if (diff < 3600000) {
            return Math.floor(diff / 60000) + 'm ago';
        }

        // Less than a day
        if (diff < 86400000) {
            return Math.floor(diff / 3600000) + 'h ago';
        }

        // Format as date
        return date.toLocaleDateString();
    },

    // Escape HTML
    escapeHtml: function(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
};

// Export for use in other files if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatApp;
}
