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
        customerId: null,
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
        customerNames: {},
        pollTimer: null,
    },

    init: function(options) {
        Object.assign(this.config, options);
        this.initializePusher();
        this.setupEventListeners();
        this.resetChatUI();

        if (this.config.currentUserRole !== 'customer' && this.config.currentUserRole) {
            this.loadCustomersForChat();
        }

        if (this.config.customerId && this.config.currentUserName) {
            this.state.customerNames[this.config.customerId] = this.config.currentUserName;
        }

        this.loadConversations();
        this.startRealtimeSync();
    },

    initializePusher: function() {
        const key = this.config.pusherKey;
        if (!key || key === 'YOUR_PUSHER_KEY' || typeof Pusher === 'undefined') {
            this.state.isConnected = false;
            return;
        }

        try {
            this.state.pusher = new Pusher(key, {
                cluster: this.config.pusherCluster,
                encrypted: true,
            });

            this.state.pusher.connection.bind('connected', () => {
                this.state.isConnected = true;
                this.state.reconnectAttempts = 0;
            });

            this.state.pusher.connection.bind('disconnected', () => {
                this.state.isConnected = false;
            });

            this.state.pusher.connection.bind('error', () => {
                this.state.isConnected = false;
            });
        } catch (error) {
            console.error('Failed to initialize Pusher:', error);
            this.state.isConnected = false;
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

        // Emoji picker
        $('#emojiBtn').on('click', function(e) {
            e.preventDefault();

            const $picker = $('#emojiPicker');
            if ($picker.length) {
                $picker.toggle();
                return;
            }

            const emojis = ['😀', '😊', '😂', '😍', '🤔', '👍', '🎉', '🔥', '❤️', '👏', '😎', '😢'];
            const $container = $('<div id="emojiPicker" style="position:absolute; right:12px; bottom:68px; background:#fff; border:1px solid #dfe3e8; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.12); padding:10px; display:flex; flex-wrap:wrap; gap:8px; z-index:20; max-width:220px;"></div>');

            emojis.forEach(function(emoji) {
                const $btn = $('<button type="button" style="border:none; background:#f8f9fa; border-radius:8px; width:34px; height:34px; font-size:18px; cursor:pointer;">' + emoji + '</button>');
                $btn.on('click', function() {
                    const $input = $('#messageInput');
                    const value = $input.val();
                    const cursorPos = $input[0].selectionStart || value.length;
                    const newValue = value.slice(0, cursorPos) + emoji + value.slice(cursorPos);
                    $input.val(newValue);
                    $input.focus();
                    const newCursor = cursorPos + emoji.length;
                    $input[0].setSelectionRange(newCursor, newCursor);
                    $('#emojiPicker').remove();
                });
                $container.append($btn);
            });

            $('#chatInputArea').append($container);
        });

        // Typing indicator
        $('#messageInput').on('input', function() {
            const hasText = $(this).val().trim().length > 0;

            if (hasText && !self.state.isTyping) {
                self.state.isTyping = true;
                self.sendTypingIndicator(true);
            } else if (!hasText && self.state.isTyping) {
                self.state.isTyping = false;
                self.sendTypingIndicator(false);
            }

            // Clear previous timer
            if (self.state.typingTimer) {
                clearTimeout(self.state.typingTimer);
            }

            // Set new timer to stop typing after 2 seconds of inactivity
            self.state.typingTimer = setTimeout(function() {
                if (self.state.isTyping) {
                    self.state.isTyping = false;
                    self.sendTypingIndicator(false);
                }
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

    startRealtimeSync: function() {
        const self = this;
        if (this.state.pollTimer) {
            clearInterval(this.state.pollTimer);
        }
        this.state.pollTimer = setInterval(function() {
            self.pollUpdates();
        }, 2000);
    },

    pollUpdates: function() {
        const self = this;
        if (this.state.currentRoom) {
            this.pollRoomMessages(this.state.currentRoom, function() {
                self.pollTypingUsers(self.state.currentRoom);
                self.markMessagesAsRead(function() {
                    self.loadConversations();
                });
            });
            return;
        }
        this.loadConversations();
    },

    pollRoomMessages: function(room, done) {
        const self = this;
        $.ajax({
            url: this.config.apiBaseUrl + '/history/' + encodeURIComponent(room),
            method: 'GET',
            success: function(response) {
                if (response.status === 'success' && room === self.state.currentRoom) {
                    (response.data || []).forEach(function(message) {
                        self.addMessageToUI(message);
                    });
                }
                if (typeof done === 'function') {
                    done();
                }
            },
            error: function() {
                if (typeof done === 'function') {
                    done();
                }
            }
        });
    },

    // Render conversations list
    renderConversations: function(conversations) {
        const self = this;
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
            const isOwnLast = conv.last_message && String(conv.last_message_user_id) === String(self.config.currentUserId);
            const previewText = conv.last_message
                ? (isOwnLast ? ('Anda: ' + conv.last_message) : conv.last_message)
                : 'No messages yet';
            const showUnread = Number(conv.unread_count) > 0 && conv.chat_room !== self.state.currentRoom;
            const unreadBadge = showUnread
                ? `<span class="unread-badge-conversation">${conv.unread_count}</span>`
                : '';

            const lastMessage = `<div class="conversation-last-message">${self.escapeHtml(previewText)}</div>`;

            const displayName = self.getDisplayRoomName(conv.chat_room);
            const item = `
                <div class="conversation-item" data-room="${conv.chat_room}">
                    <div class="conversation-avatar">
                        ${displayName.charAt(0).toUpperCase()}
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-name">
                            ${displayName}
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

        if (this.state.currentRoom) {
            $(`.conversation-item[data-room="${this.state.currentRoom}"]`).addClass('active');
        }
    },

    // Join a chat room
    joinChatRoom: function(room) {
        const self = this;

        // Keep room state consistent before any async callback can fire.
        if (this.state.currentRoom && this.state.currentRoom !== room && this.state.channel) {
            if (this.state.pusher && this.state.channel) {
                this.state.pusher.unsubscribe(this.state.channel.name);
            }
            this.state.channel = null;
        }

        // Show loading state
        this.showLoadingState();

        $.ajax({
            url: this.config.apiBaseUrl + '/join',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.getCsrfToken()
            },
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

                    // Force input on whenever this room is active.
                    self.enableChatInput();

                    // Mark as read
                    self.markMessagesAsRead();

                    console.log('Joined room:', room);
                }
            },
            error: function(xhr, status, error) {
                console.error('Join chat room error:', xhr.responseText || error);
                self.showError('Failed to join chat room: ' + (xhr.responseJSON?.message || error));
                self.enableChatInput();
            }
        });
    },

    // Subscribe to Pusher channel
    subscribeToChannel: function(room) {
        const self = this;

        if (!this.state.pusher) {
            return;
        }

        if (this.state.channel) {
            this.state.pusher.unsubscribe(this.state.channel.name);
        }

        this.state.channel = this.state.pusher.subscribe(`chat-${room}`);

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
            headers: {
                'X-CSRF-TOKEN': this.getCsrfToken()
            },
            data: {
                chat_room: this.state.currentRoom,
                message: message,
                reply_to_id: replyToId,
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#messageInput').val('');

                    // Show the sent message immediately in the active room, without waiting for reload.
                    if (response.data && self.state.currentRoom) {
                        self.addMessageToUI(response.data);
                        self.loadConversations();
                    }

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
        if (data.chat_room && data.chat_room !== this.state.currentRoom) {
            this.loadConversations();
            return;
        }
        if (String(data.user_id) === String(this.config.currentUserId)) {
            return;
        }
        this.addMessageToUI(data);
        this.loadConversations();
    },

    // Add message to UI
    addMessageToUI: function(messageData) {
        if (!messageData || !messageData.id) {
            return;
        }

        const messageId = String(messageData.id);
        if ($(`#chatMessages .message[data-message-id="${messageId}"]`).length) {
            return;
        }

        const isSent = String(messageData.user_id) === String(this.config.currentUserId);
        const messageClass = isSent ? 'message sent' : 'message';
        const senderName = isSent
            ? (this.config.currentUserName || messageData.sender_name || 'Anda')
            : (messageData.sender_name || this.getDisplayRoomName(this.state.currentRoom) || 'User');

        const avatar = senderName.charAt(0).toUpperCase();

        let contentHtml = '';

        if (messageData.message_type === 'text') {
            contentHtml = `<div class="message-text">${this.escapeHtml(messageData.message)}</div>`;
        } else if (messageData.message_type === 'image' && messageData.attachment_url) {
            contentHtml = `
                <img src="${messageData.attachment_url}" class="message-image" alt="Image">
                <div class="message-text">${this.escapeHtml(messageData.message)}</div>
            `;
        } else if (messageData.message_type === 'file' && messageData.attachment_url) {
            const fileLabel = messageData.message || messageData.original_name || 'View File';
            contentHtml = `
                <div class="message-file">
                    <i class="fas fa-file"></i>
                    <a href="${messageData.attachment_url}" target="_blank">${this.escapeHtml(fileLabel)}</a>
                </div>
            `;
        }

        const messageHtml = `
            <div class="${messageClass}" data-message-id="${messageData.id}">
                <div class="message-avatar">${avatar}</div>
                <div class="message-content">
                    <div class="message-sender">${this.escapeHtml(senderName)}</div>
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
            headers: {
                'X-CSRF-TOKEN': this.getCsrfToken()
            },
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
        if (String(data.user_id) === String(this.config.currentUserId)) return;
        if (data.user_name === this.config.currentUserName) return;
        this.showTypingIndicator(data.user_name);
    },

    showTypingIndicator: function(name) {
        const $indicator = $('#typingIndicator');
        const $text = $indicator.find('.typing-text');
        $text.text((name || 'Seseorang') + ' sedang mengetik...');
        $indicator.show();
    },

    hideTypingIndicator: function() {
        $('#typingIndicator').hide();
    },

    pollTypingUsers: function(room) {
        const self = this;
        $.ajax({
            url: this.config.apiBaseUrl + '/typing-users/' + encodeURIComponent(room),
            method: 'GET',
            success: function(response) {
                if (response.status !== 'success' || room !== self.state.currentRoom) {
                    return;
                }

                const others = (response.data || []).filter(function(user) {
                    return String(user.user_id) !== String(self.config.currentUserId);
                });

                if (others.length === 0) {
                    self.hideTypingIndicator();
                    return;
                }

                self.showTypingIndicator(others[0].participant_name);
            }
        });
    },

    // Handle user joined event
    handleUserJoined: function() {
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

        const roomToLeave = this.state.currentRoom;

        $.ajax({
            url: this.config.apiBaseUrl + '/leave',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.getCsrfToken()
            },
            data: {
                chat_room: roomToLeave,
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (self.state.currentRoom === roomToLeave) {
                        self.state.currentRoom = null;
                    }

                    if (self.state.channel && self.state.channel.name === `chat-${roomToLeave}`) {
                        self.state.pusher.unsubscribe(`chat-${roomToLeave}`);
                        self.state.channel = null;
                    }

                    if (self.state.currentRoom === null) {
                        self.resetChatUI();
                    }

                    console.log('Left chat room');
                }
            },
            error: function() {
                console.error('Failed to leave chat room');
            }
        });
    },

    // Mark messages as read
    markMessagesAsRead: function(done) {
        if (!this.state.currentRoom) {
            if (typeof done === 'function') {
                done();
            }
            return;
        }

        $.ajax({
            url: this.config.apiBaseUrl + '/read',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.getCsrfToken()
            },
            data: {
                chat_room: this.state.currentRoom,
            },
            complete: function() {
                if (typeof done === 'function') {
                    done();
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
        const self = this;
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

        const payload = data && data.participants ? data : { participants: Array.isArray(data) ? data : [], counts: { online_participants: 0 } };
        const participants = payload.participants || [];
        const onlineCount = participants.filter(function(participant) {
            return participant && participant.is_online;
        }).length;

        participants.forEach(function(participant) {
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
        const counts = payload.counts || { online_participants: onlineCount };
        $('#chatRoomParticipants').text(`${counts.online_participants ?? onlineCount} online`);
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
        $('#chatRoomTitle').text(this.getDisplayRoomName(room));
        $('#leaveChatBtn').show();
        $('#chatInputArea').show();
        this.hideLoadingState();

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

                    self.hideLoadingState();
                    self.scrollToBottom();
                }
            },
            error: function() {
                self.hideLoadingState();
            }
        });
    },

    // Reset chat UI
    resetChatUI: function() {
        $('#chatRoomTitle').text('Select a conversation');
        $('#chatRoomParticipants').text('');
        $('#leaveChatBtn').hide();
        $('#chatInputArea').hide();
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
        $('#messageInput').prop('disabled', false);
        $('#sendMessageBtn').prop('disabled', false);
        $('#chatInputArea').show();
        $('#messageInput').focus();
    },

    // Disable chat input
    disableChatInput: function() {
        if (!this.state.currentRoom) {
            $('#messageInput').prop('disabled', true);
            $('#sendMessageBtn').prop('disabled', true);
            $('#chatInputArea').hide();
        }
    },

    // Handle file upload
    handleFileUpload: function(file) {
        if (!file || !this.state.currentRoom) return;

        const self = this;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('chat_room', this.state.currentRoom);
        formData.append('csrf_test_name', this.getCsrfToken());

        this.showLoadingState();

        $.ajax({
            url: this.config.apiBaseUrl + '/upload',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    const displayName = response.data.original_name || response.data.filename || file.name;

                    // Send message with attachment using original file name for display
                    $.ajax({
                        url: self.config.apiBaseUrl + '/send',
                        method: 'POST',
                        data: {
                            chat_room: self.state.currentRoom,
                            message: displayName,
                            message_type: response.data.type,
                            attachment_url: response.data.url,
                        },
                        success: function(sendResponse) {
                            if (sendResponse.status === 'success') {
                                self.hideLoadingState();

                                if (self.state.currentRoom) {
                                    self.loadMessageHistory(self.state.currentRoom);
                                    self.loadConversations();
                                }

                                console.log('File sent successfully');
                            }
                        },
                        error: function() {
                            self.hideLoadingState();
                            self.showError('Failed to send uploaded file');
                        }
                    });
                }
            },
            error: function() {
                self.hideLoadingState();
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

    hideLoadingState: function() {
        $('#chatMessages').find('.loading-spinner').remove();
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

    // Show new chat dialog with better UX
    showNewChatDialog: function() {
        const self = this;
        const userRole = this.config.currentUserRole || 'user';

        // Create modal dialog for new chat options
        const modalHtml = `
            <div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newChatModalLabel">Create New Chat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="chat-options">
                                ${self.getChatOptions(userRole)}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if present
        $('#newChatModal').remove();

        // Add modal to body
        $('body').append(modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('newChatModal'));
        modal.show();

        // Handle chat option clicks
        $(document).on('click', '.chat-option-btn', function() {
            const roomType = $(this).data('room-type');
            const roomId = $(this).data('room-id');

            let chatRoom = '';

            // Admin join role-specific room
            if (roomType === 'role-chat') {
                const targetRole = $('#targetRoleSelect').val();
                if (!targetRole) {
                    alert('Please select a target role first');
                    return;
                }

                if (targetRole === 'customer') {
                    const customerId = $('#customerSelect').val();
                    if (!customerId) {
                        alert('Please select a customer first');
                        return;
                    }
                    chatRoom = `customer-${customerId}`;
                } else {
                    chatRoom = `role-${targetRole}`;
                }
            }
            // Customer specific chat
            else if (roomType === 'customer-chat') {
                const customerId = $('#customerSelect').val();
                if (!customerId) {
                    alert('Please select a customer first');
                    return;
                }
                chatRoom = `customer-${customerId}`;
            }
            // Team chat
            else if (roomType === 'team' && roomId) {
                chatRoom = `team-${roomId}`;
            }
            // Customer chat with sales agent (booking chat)
            else if (roomType === 'booking' && roomId === 'my') {
                // Use current customer ID from session
                const customerId = self.config.customerId;
                if (customerId) {
                    chatRoom = `customer-${customerId}`;
                } else {
                    alert('Customer ID not found. Please login again.');
                    return;
                }
            }

            if (chatRoom) {
                modal.hide();
                self.joinChatRoom(chatRoom);
            }
        });

        // Enable/disable role chat button based on selection
        $(document).on('change', '#targetRoleSelect', function() {
            $('#joinRoleRoomBtn').prop('disabled', !$(this).val());
        });

        // Enable/disable customer chat button based on selection
        $(document).on('change', '#customerSelect', function() {
            $('#chatWithCustomerBtn').prop('disabled', !$(this).val());
        });

        // Load customers for admin and staff
        if (userRole === 'admin' || ['owner', 'mandor', 'spv'].includes(userRole)) {
            self.loadCustomersForChat();
        }

        // Clean up modal when hidden
        $('#newChatModal').on('hidden.bs.modal', function() {
            $(this).remove();
            $(document).off('click', '.chat-option-btn');
        });
    },

    // Get chat options based on user role
    getChatOptions: function(userRole) {
        let options = '';

        // ADMIN: Bisa akses semua room
        if (userRole === 'admin') {
            options += `
                <div class="chat-option">
                    <h6>👥 Chat dengan Role</h6>
                    <p>Admin bisa join ke room role siapa saja</p>
                    <div class="input-group">
                        <select id="targetRoleSelect" class="form-select">
                            <option value="">Pilih Role...</option>
                            <option value="admin">👑 Admin Team</option>
                            <option value="owner">🏢 Owner Team</option>
                            <option value="mandor">👷 Mandor Team</option>
                            <option value="spv">📋 Supervisor Team</option>
                            <option value="customer">👤 Customer</option>
                        </select>
                        <button class="btn btn-primary chat-option-btn ms-2" data-room-type="role-chat" disabled id="joinRoleRoomBtn">
                            <i class="fas fa-comments"></i> Chat
                        </button>
                    </div>
                </div>

                <hr>

                <div class="chat-option">
                    <h6>👤 Chat dengan Customer</h6>
                    <p>Chat dengan customer tertentu</p>
                    <div class="input-group">
                        <select id="customerSelect" class="form-select">
                            <option value="">Pilih Customer...</option>
                        </select>
                        <button class="btn btn-success chat-option-btn ms-2" data-room-type="customer-chat" disabled id="chatWithCustomerBtn">
                            <i class="fas fa-comments"></i> Chat
                        </button>
                    </div>
                </div>
            `;
        }

        // OWNER/MANDOR/SPV: Internal team + customer chat
        else if (['owner', 'mandor', 'spv'].includes(userRole)) {
            options += `
                <div class="chat-option">
                    <h6>👥 Internal Team Chat</h6>
                    <p>Chat dengan internal team</p>
                    <div class="team-options">
                        <button class="btn btn-outline-primary chat-option-btn me-2" data-room-type="team" data-room-id="management">
                            <i class="fas fa-briefcase"></i> Management
                        </button>
                        <button class="btn btn-outline-primary chat-option-btn me-2" data-room-type="team" data-room-id="sales">
                            <i class="fas fa-users"></i> Sales Team
                        </button>
                        <button class="btn btn-outline-primary chat-option-btn me-2" data-room-type="team" data-room-id="finance">
                            <i class="fas fa-calculator"></i> Finance
                        </button>
                        <button class="btn btn-outline-primary chat-option-btn" data-room-type="team" data-room-id="construction">
                            <i class="fas fa-hard-hat"></i> Construction
                        </button>
                    </div>
                </div>

                <hr>

                <div class="chat-option">
                    <h6>👤 Chat dengan Customer</h6>
                    <p>Chat dengan customer tertentu</p>
                    <div class="input-group">
                        <select id="customerSelect" class="form-select">
                            <option value="">Pilih Customer...</option>
                        </select>
                        <button class="btn btn-success chat-option-btn ms-2" data-room-type="customer-chat" disabled id="chatWithCustomerBtn">
                            <i class="fas fa-comments"></i> Chat
                        </button>
                    </div>
                </div>
            `;
        }

        // CUSTOMER: Sales agent chat
        else if (userRole === 'customer') {
            options += `
                <div class="chat-option">
                    <h6>🏠 Chat Sales Agent</h6>
                    <p>Chat dengan sales agent tentang pembelian rumah Anda</p>
                    <button class="btn btn-success chat-option-btn" data-room-type="booking" data-room-id="my">
                        <i class="fas fa-home"></i> Chat Sales Agent
                    </button>
                </div>
            `;
        }

        return options;
    },

    // Scroll to bottom of messages
    scrollToBottom: function() {
        const $messages = $('#chatMessages');
        $messages.scrollTop($messages[0].scrollHeight);
    },

    // Format timestamp
    formatTimestamp: function(timestamp) {
        if (!timestamp) {
            return 'Just now';
        }

        let date = new Date(timestamp);

        if (isNaN(date.getTime())) {
            const normalized = String(timestamp).replace(' ', 'T');
            date = new Date(normalized + 'Z');
        }

        if (isNaN(date.getTime())) {
            return 'Just now';
        }

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

    // Get CSRF token from cookie
    getCsrfToken: function() {
        const cookieName = 'csrf_cookie_name';
        const name = cookieName + '=';
        const cookies = document.cookie.split(';');

        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1);
            }
            if (cookie.indexOf(name) === 0) {
                return cookie.substring(name.length, cookie.length);
            }
        }
        return '';
    },

    // Get friendly display name for chat room
    getDisplayRoomName: function(room) {
        if (!room) return 'Chat';

        if (room.startsWith('customer-')) {
            const customerId = room.replace('customer-', '');
            const customerName = this.state.customerNames[customerId];

            if (customerName) {
                return customerName;
            }

            if (this.config.customerId && String(this.config.customerId) === String(customerId) && this.config.currentUserName) {
                return this.config.currentUserName;
            }

            return `Customer ${customerId}`;
        }

        if (room.startsWith('role-')) {
            const roleName = room.replace('role-', '');
            const labels = {
                admin: 'Admin Team',
                owner: 'Owner Team',
                mandor: 'Mandor Team',
                spv: 'Supervisor Team',
                customer: 'Customer Team',
            };
            return labels[roleName] || roleName.charAt(0).toUpperCase() + roleName.slice(1);
        }

        if (room.startsWith('team-')) {
            const teamName = room.replace('team-', '');
            return teamName.charAt(0).toUpperCase() + teamName.slice(1);
        }

        return room;
    },

    // Load customers for admin chat
    loadCustomersForChat: function() {
        const self = this;

        $.ajax({
            url: '/data-customer/json',
            method: 'GET',
            success: function(response) {
                const $select = $('#customerSelect');
                $select.empty();

                // Handle DataTables format response
                const customers = response.data || response;
                self.state.customerNames = {};

                if (customers && customers.length > 0) {
                    $select.append('<option value="">Pilih Customer...</option>');

                    customers.forEach(function(customer) {
                        self.state.customerNames[customer.id] = customer.nama;
                        $select.append(`<option value="${customer.id}">${customer.nama} (${customer.email || customer.telepon || 'No contact'})</option>`);
                    });

                    // Enable chat button when customer selected
                    $select.on('change', function() {
                        $('#chatWithCustomerBtn').prop('disabled', !$(this).val());
                    });
                } else {
                    $select.append('<option value="">No customers available</option>');
                }
            },
            error: function() {
                const $select = $('#customerSelect');
                $select.empty();
                $select.append('<option value="">Error loading customers</option>');
            }
        });
    },
};

// Export for use in other files if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatApp;
}
