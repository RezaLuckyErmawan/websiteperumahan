<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Progres Pembayaran Rumah | Sistem Manajemen Perumahan</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
  <div class="container1">
    <div class="sidebar1" id="sidebar">
      <div>
        <h4>Sistem Manajemen Informasi Perumahan</h4>
        <div class="nav1">
          <?php if (($userRole ?? '') !== 'customer'): ?>
            <a class="menu-link" href="/dashboard"><span class="material-icons rotate-icon">dashboard</span> Dashboard</a>
          <?php endif; ?>

          <div class="menu-dropdown aktif">
            <button class="dropdown-btn">
              <span class="material-icons rotate-icon">monetization_on</span> Keuangan
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container" style="margin-top: 10px;">
              <?php if (($userRole ?? '') !== 'customer'): ?>
                <a class="menu-link" href="/pembelian-rumah"><span class="material-icons rotate-icon">real_estate_agent</span> Data Penjualan Rumah</a>
              <?php endif; ?>
              <a class="menu-link" href="/pembayaran-rumah"><span class="material-icons rotate-icon">payments</span> Pembayaran Cicilan Rumah</a>
              <a class="menu-link active" href="/progres-pembayaran-rumah"><span class="material-icons rotate-icon">timeline</span> Data Progres Pembayaran Rumah</a>
            </div>
          </div>
        </div>
      </div>
      <div class="logout">
        <a href="/logout"><span class="material-icons">logout</span> Logout</a>
      </div>
    </div>

    <div class="main">
      <div class="navbar1">
        <span class="material-icons toggle-btn" onclick="toggleSidebar()">menu</span>
        <div class="page-title">Data Progres Pembayaran Rumah</div>
        <div class="actions">
          <div class="profile">
            <img src="https://i.pravatar.cc/40" alt="Profile">
          </div>
        </div>
      </div>

      <div class="content1">
        <table id="progresPembayaranTable" class="display table table-striped table-bordered w-100">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Kode Rumah</th>
              <th>Harga</th>
              <th>Total Dibayar</th>
              <th>Sisa Tagihan</th>
              <th>Progress</th>
              <th>Status</th>
              <th>Metode</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <style>
          #progresPembayaranTable thead th {
            background-color: #eef6f8;
            color: #203246;
            text-align: center;
          }

          .progress-payment {
            min-width: 150px;
          }

          .progress-payment-track {
            height: 8px;
            overflow: hidden;
            border-radius: 999px;
            background: #e2e8f0;
          }

          .progress-payment-fill {
            height: 100%;
            border-radius: 999px;
            background: #2563eb;
          }

          .progress-payment-text {
            display: block;
            margin-top: 5px;
            color: #647084;
            font-size: 12px;
            font-weight: 800;
          }
        </style>
      </div>

      <footer class="footer1">
        <p>&copy; <?= date('Y') ?> Sistem Manajemen Informasi Perumahan. All rights reserved.</p>
      </footer>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("active");
    }

    document.querySelectorAll('.dropdown-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        this.parentElement.classList.toggle('aktif');
      });
    });
  </script>
  <script src="<?= base_url('assets/js/progrespembayaranrumah.js') ?>"></script>
</body>
</html>
