<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= isset($pageTitle) ? esc($pageTitle) : 'Sistem Manajemen Perumahan' ?></title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS (conditionally loaded) -->
    <?php if (isset($useDataTables) && $useDataTables): ?>
        <?php
            $dataTablesCss = FCPATH . 'assets/vendor/datatables/jquery.dataTables.min.css';
            $dataTablesJs = FCPATH . 'assets/vendor/datatables/jquery.dataTables.min.js';
        ?>
        <?php if (is_file($dataTablesCss) && is_file($dataTablesJs)): ?>
            <link rel="stylesheet" href="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.css') ?>" />
            <script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
        <?php else: ?>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Additional CSS -->
    <?php if (isset($customCSS)): ?>
    <style><?= $customCSS ?></style>
    <?php endif; ?>

    <style>
        .app-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14);
            background: #e2e8f0;
        }

        .notifications-wrap {
            position: relative;
        }

        .notifications-toggle {
            position: relative;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(37, 99, 235, 0.12);
            border-radius: 999px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            color: var(--muted);
            box-shadow: var(--shadow-sm);
            cursor: pointer;
        }

        .notifications-toggle:hover {
            color: var(--primary);
            border-color: rgba(37, 99, 235, 0.2);
        }

        .notifications-toggle .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            min-height: 18px;
            padding: 0 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: linear-gradient(135deg, #ef4444, #f97316);
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.28);
        }

        .notifications-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: min(380px, calc(100vw - 32px));
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
            overflow: hidden;
            display: none;
            z-index: 40;
        }

        .notifications-dropdown.show {
            display: block;
        }

        .notifications-header {
            padding: 14px 16px;
            border-bottom: 1px solid #e4e8ef;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }

        .notifications-header strong {
            display: block;
            color: var(--text);
            font-size: 14px;
            font-weight: 800;
        }

        .notifications-header span {
            color: var(--muted);
            font-size: 12px;
        }

        .chat-notification-item {
            display: flex;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            border-bottom: 1px solid #eef2f7;
            transition: background-color 0.16s ease;
        }

        .chat-notification-item:hover {
            background: #f8fbff;
        }

        .chat-notification-item:last-child {
            border-bottom: 0;
        }

        .chat-notification-icon {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(94, 234, 212, 0.18));
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-notification-body {
            min-width: 0;
            flex: 1;
        }

        .chat-notification-room {
            color: var(--text);
            font-weight: 800;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .chat-notification-preview {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .chat-notification-meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-top: 6px;
            color: #94a3b8;
            font-size: 11px;
        }

        .chat-notification-empty {
            display: flex;
            gap: 12px;
            padding: 18px 16px;
            color: var(--muted);
        }

        .chat-notification-empty .material-icons {
            color: var(--primary);
        }

        /* Legacy module views still render their own shell inside the content area.
           Hide the duplicated chrome so the shared layout/sidebar remains the only visible frame. */
        .content1 > .container1 {
            display: flex;
            width: 100%;
        }

        .content1 > .container1 > .sidebar1 {
            display: none !important;
        }

        .content1 > .container1 > .main {
            width: 100% !important;
            margin-left: 0 !important;
        }

        .content1 > .container1 > .main > .navbar1,
        .content1 > .container1 > .main > .footer1 {
            display: none !important;
        }

        .content1 > .container1 > .main > .content1 {
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>
    <div class="container1">
        <!-- Sidebar Component -->
        <?php
        $currentUrl = current_url();
        $userRole = session()->get('role') ?? 'guest';
        echo view('components/sidebar', [
            'currentUrl' => $currentUrl,
            'userRole' => $userRole
        ]);
        ?>

        <!-- Main Content -->
        <div class="main">
            <!-- Navbar -->
            <div class="navbar1">
                <span class="material-icons toggle-btn" onclick="toggleSidebar()">menu</span>
                <div class="page-title">
                    <?= isset($pageTitle) ? esc($pageTitle) : 'Dashboard' ?>
                </div>

                <!-- User Profile Section -->
                <?php
                $loginNama = session()->get('nama') ?? '-';
                $loginUsername = session()->get('username') ?? '-';
                $loginRole = session()->get('role') ?? '-';
                $roleLabel = match (strtolower((string) $loginRole)) {
                    'owner' => 'Owner',
                    'admin' => 'Admin',
                    'mandor' => 'Mandor',
                    'spv' => 'SPV',
                    'customer' => 'Customer',
                    'karyawan' => 'Owner',
                    default => ucfirst((string) $loginRole),
                };
                $popoverContent = '<div class="user-popover">'
                  . '<div class="user-popover-row"><span>Nama</span><strong>' . esc($loginNama) . '</strong></div>'
                  . '<div class="user-popover-row"><span>Username</span><strong>' . esc($loginUsername) . '</strong></div>'
                  . '<div class="user-popover-row"><span>Role</span><strong>' . esc($roleLabel) . '</strong></div>'
                  . '<a class="user-popover-logout" href="/logout"><span class="material-icons" style="font-size:16px;">logout</span> Logout</a>'
                  . '</div>';
                ?>
                <div class="actions">
                    <div class="notifications-wrap">
                        <button type="button" class="notifications-toggle" id="chatNotificationToggle" aria-label="Notifikasi chat">
                            <span class="material-icons">notifications</span>
                            <span class="badge" id="chatNotificationBadge" style="display:none;">0</span>
                        </button>
                        <div class="notifications-dropdown" id="chatNotificationDropdown">
                            <div class="notifications-header">
                                <strong id="chatNotificationTitle">Memuat notifikasi...</strong>
                                <span>Pesan chat terbaru akan muncul di sini.</span>
                            </div>
                            <div id="chatNotificationMenu"></div>
                        </div>
                    </div>
                    <div class="profile">
                        <img
                          src="<?= base_url('assets/images/default-avatar.svg') ?>"
                          alt="Profile"
                          id="userProfileBtn"
                          role="button"
                          tabindex="0"
                          class="app-user-avatar"
                          style="cursor: pointer;"
                          data-bs-toggle="popover"
                          data-bs-placement="bottom"
                          data-bs-html="true"
                          data-bs-title="Info Akun"
                          data-bs-content="<?= esc($popoverContent, 'attr') ?>"
                          data-bs-custom-class="user-profile-popover"
                        >
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="content1">
                <?= $this->renderSection('content') ?>
            </div>

            <!-- Footer -->
            <footer class="footer1">
                <p>&copy; <?= date('Y') ?> Sistem Manajemen Informasi Perumahan. All rights reserved.</p>
            </footer>
        </div>
    </div>

    <!-- Common Modals -->
    <?= $this->renderSection('modals') ?>

    <!-- Common JavaScript -->
    <script>
        let chatNotificationPollTimer = null;

        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("active");
        }

        function getSidebarStateKey() {
            const role = <?= json_encode(session()->get('role') ?? 'guest') ?>;
            return `sidebar-dropdown-state:${role}`;
        }

        function loadSidebarState() {
            try {
                return JSON.parse(localStorage.getItem(getSidebarStateKey()) || '{}') || {};
            } catch (error) {
                return {};
            }
        }

        function saveSidebarState(state) {
            try {
                localStorage.setItem(getSidebarStateKey(), JSON.stringify(state));
            } catch (error) {
                // Ignore storage errors, sidebar still works without persistence.
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatChatNotificationTime(value) {
            if (!value) {
                return '';
            }

            const date = new Date(String(value).replace(' ', 'T'));
            if (Number.isNaN(date.getTime())) {
                return '';
            }

            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit',
            });
        }

        function renderChatNotifications(payload) {
            const unreadCount = Number(payload?.unread_count ?? 0);
            const items = Array.isArray(payload?.items) ? payload.items : [];
            const $badge = $('#chatNotificationBadge');
            const $menu = $('#chatNotificationMenu');
            const $title = $('#chatNotificationTitle');

            if ($badge.length) {
                if (unreadCount > 0) {
                    $badge.text(unreadCount).show();
                } else {
                    $badge.text('0').hide();
                }
            }

            if ($title.length) {
                $title.text(unreadCount > 0
                    ? `${unreadCount} chat belum dibaca`
                    : 'Tidak ada chat baru');
            }

            if (!$menu.length) {
                return;
            }

            if (items.length === 0) {
                $menu.html(`
                    <div class="chat-notification-empty">
                        <span class="material-icons">chat_bubble_outline</span>
                        <div>
                            <strong>Tidak ada notifikasi chat</strong>
                            <div>Semua pesan sudah dibaca.</div>
                        </div>
                    </div>
                `);
                return;
            }

            const html = items.map((item) => {
                const preview = item.last_message ? item.last_message : 'Pesan baru';
                const time = formatChatNotificationTime(item.last_message_at);

                return `
                    <a class="chat-notification-item" href="/chat">
                        <div class="chat-notification-icon">
                            <span class="material-icons">mark_chat_unread</span>
                        </div>
                        <div class="chat-notification-body">
                            <div class="chat-notification-room">${escapeHtml(item.room_label || 'Chat')}</div>
                            <div class="chat-notification-preview">${escapeHtml(preview)}</div>
                            <div class="chat-notification-meta">
                                <span>${item.unread_count} pesan</span>
                                <span>${escapeHtml(time)}</span>
                            </div>
                        </div>
                    </a>
                `;
            }).join('');

            $menu.html(html);
        }

        function refreshChatNotifications() {
            const $badge = $('#chatNotificationBadge');
            const $menu = $('#chatNotificationMenu');

            if (!$badge.length && !$menu.length) {
                return;
            }

            $.ajax({
                url: '/chat/notifications?limit=8',
                method: 'GET',
                success: function(response) {
                    if (response && response.status === 'success') {
                        renderChatNotifications(response.data || {});
                    }
                },
                error: function() {
                    if ($badge.length) {
                        $badge.hide();
                    }
                }
            });
        }

        window.refreshChatNotifications = refreshChatNotifications;

        function showSuccess(message = 'Data berhasil diproses.') {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.textContent = message;
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
                setTimeout(() => {
                    modal.hide();
                }, 2500);
            } else {
                alert(message);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const savedState = loadSidebarState();

            document.querySelectorAll('.menu-dropdown').forEach(dropdown => {
                const key = dropdown.dataset.dropdownKey;
                const isActive = dropdown.classList.contains('aktif');
                const shouldOpen = isActive || savedState[key] === true;
                const btn = dropdown.querySelector('.dropdown-btn');

                dropdown.classList.toggle('aktif', shouldOpen);
                if (btn) {
                    btn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
                }
            });

            document.querySelectorAll('.dropdown-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const parentDropdown = this.parentElement;
                    const key = this.dataset.dropdownKey || parentDropdown.dataset.dropdownKey;
                    const isOpen = parentDropdown.classList.toggle('aktif');
                    this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                    const state = loadSidebarState();
                    if (key) {
                        state[key] = isOpen;
                        saveSidebarState(state);
                    }
                });
            });

            refreshChatNotifications();
            if (chatNotificationPollTimer) {
                clearInterval(chatNotificationPollTimer);
            }
            chatNotificationPollTimer = setInterval(refreshChatNotifications, 30000);

            const chatBell = document.getElementById('chatNotificationToggle');
            const chatDropdown = document.getElementById('chatNotificationDropdown');

            if (chatBell && chatDropdown) {
                chatBell.addEventListener('click', function (event) {
                    event.stopPropagation();
                    chatDropdown.classList.toggle('show');
                });

                document.addEventListener('click', function (event) {
                    if (!chatDropdown.contains(event.target) && !chatBell.contains(event.target)) {
                        chatDropdown.classList.remove('show');
                    }
                });
            }
        });

        // Initialize User Profile Popover
        const profileBtn = document.getElementById('userProfileBtn');
        if (profileBtn) {
            const userPopover = new bootstrap.Popover(profileBtn, {
                trigger: 'click',
                container: 'body',
                sanitize: false
            });

            document.addEventListener('click', function (e) {
                if (!profileBtn.contains(e.target) && !e.target.closest('.popover')) {
                    userPopover.hide();
                }
            });
        }
    </script>

    <!-- Page-specific JavaScript -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>
