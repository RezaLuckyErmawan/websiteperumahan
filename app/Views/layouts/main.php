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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <?php endif; ?>

    <!-- Additional CSS -->
    <?php if (isset($customCSS)): ?>
    <style><?= $customCSS ?></style>
    <?php endif; ?>

    <?= $this->renderSection('styles') ?>
</head>
<body>
    <div class="container1">
        <!-- Sidebar Component -->
        <?php
        $currentUrl = current_url(true);
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
                    <div class="notifications">
                        <span class="material-icons">notifications</span>
                        <span class="badge">3</span>
                    </div>
                    <div class="profile">
                        <img
                          src="https://i.pravatar.cc/40"
                          alt="Profile"
                          id="userProfileBtn"
                          role="button"
                          tabindex="0"
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
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("active");
        }

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
            document.querySelectorAll('.menu-dropdown').forEach(dropdown => {
                dropdown.classList.remove('aktif');
            });

            document.querySelectorAll('.dropdown-btn').forEach(btn => {
                btn.setAttribute('aria-expanded', 'false');
                btn.addEventListener('click', function () {
                    const parentDropdown = this.parentElement;
                    const isOpen = parentDropdown.classList.toggle('aktif');
                    this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            });
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
