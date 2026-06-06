<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Admin</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <style>
    /* Gaya dasar */

  </style>
  <div class="container1">
    <!-- Sidebar -->
    <div class="sidebar1" id="sidebar1">
      <div>
        <h4>
          Sistem Manajemen Perumahan
        </h4>
        <div class="nav1">
          <a class=" menu-link active" href="/dashboard"><span class="material-icons rotate-icon">dashboard</span> Dashboard</a>
          <!-- Manajemen Marketing -->
           <div class="menu-dropdown">
            <button class="dropdown-btn">
               <span class="material-icons rotate-icon">analytics</span> Marketing
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container">
            <a class="menu-link"href="/data-customer"><span class="material-icons rotate-icon">groups</span> Data Customer</a>
            <a class="menu-link"href="/pembatalan-transaksi"><span class="material-icons rotate-icon">remove_shopping_cart</span> Pembatalan Transaksi</a>
            </div>
           </div>

          <!-- Manejemen Proyek -->
          <div class="menu-dropdown">
            <button class="dropdown-btn">
              <span class="material-icons rotate-icon">business_center</span> Manajemen Proyek
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container">
              <a class="menu-link" href="/data-bahan" style="margin-top: 10px;">
                <span class="material-icons rotate-icon">construction</span> Bahan Bangunan
              </a>
              <a class="menu-link" href="/data-rumah">
                <span class="material-icons rotate-icon">home_work</span> Data Rumah
              </a>
             <a class="menu-link" href="/rab-rumah">
                <span class="material-icons rotate-icon">description</span> RAB Rumah
              </a>
               <a class="menu-link" href="/rab-bahan">
                <span class="material-icons rotate-icon">description</span> RAB Bahan
              </a>
              <a class="menu-link" href="/rab-pekerja">
                <span class="material-icons rotate-icon">description</span> RAB Pekerja
              </a>
              <a class="menu-link" href="/realisasi-rumah">
                <span class="material-icons rotate-icon">description</span> Realisasi Rumah
              </a>
               <a class="menu-link" href="/realisasi-bahan">
                <span class="material-icons rotate-icon">description</span> Realisasi Bahan
              </a>
              <a class="menu-link" href="/realisasi-pekerja">
                <span class="material-icons rotate-icon">description</span> Realisasi Pekerja
              </a>
              <a class="menu-link" href="/data-bahan-pembangunan">
                <span class="material-icons rotate-icon">business</span> Data Bahan Pembangunan
              </a>
               <a class="menu-link" href="/pekerjaan-insidentil">
                <span class="material-icons rotate-icon">architecture</span> Data Pekerjaan Insidentil
              </a>
            </div>
        </div>
        <!-- Manajemen LOgistik -->
        <div class="menu-dropdown">
            <button class="dropdown-btn">
              <span class="material-icons rotate-icon">fact_check</span> Manajemen Logistik
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container">
             <a class="menu-link" href="data-pembelian-bahan"><span class="material-icons rotate-icon">shopping_cart</span> Data Pembelian Bahan</a>
          <a class="menu-link" href="/detail-pembelian-bahan"><span class="material-icons rotate-icon">receipt_long</span> Detail Pembelian  Bahan</a> 
            </div>
        </div>
        <!-- Manajemen Keuangan -->
        <div class="menu-dropdown">
           <button class="dropdown-btn">
            <span class="material-icons rotate-icon">monetization_on </span> Keuangan
            <span class="material-icons arrow">expand_more</span>
           </button>
           <div class="dropdown-container">
            <a class="menu-link" href="/pembelian-rumah"><span class="material-icons rotate-icon">real_estate_agent</span> Data Pembelian Rumah</a>
           </div>
        </div> 
        <!-- Menu Master -->
         <div class="menu-dropdown">
           <button class="dropdown-btn">
            <span class="material-icons rotate-icon">folder_open</span> Menu Master
            <span class="material-icons arrow">expand_more</span>
           </button>
           <div class="dropdown-container">
            <a class="menu-link"href="/data-user"><span class="material-icons rotate-icon">groups</span> Data User</a>
            <a class="menu-link"href="/data-mandor"><span class="material-icons rotate-icon">engineering</span> Data Mandor</a>
            <!-- <a class="menu-link"href="/data-user"><span class="material-icons rotate-icon">supervisor_account</span> Data SPV</a> -->
           </div>
        </div>
        </div>
      </div>
      <div class="logout">
        <a href="/logout"><span class="material-icons">logout</span> Logout</a>
      </div>
    </div>
    <!-- Main -->
    <div class="main">
      <!-- Navbar -->
      <div class="navbar1">
        <span class="material-icons toggle-btn" onclick="toggleSidebar()">menu</span>
        <div class="search">
          <!-- <span class="material-icons">search</span>
          <input type="text" placeholder="Search..."> -->
        </div>
        <div class="actions">
          
          <div class="profile">
            <img src="https://i.pravatar.cc/40" alt="Profile">
          </div>
        </div>
      </div>

      <!-- Content -->
        <div class="content1">
          <h2>Sistem Manajemen Informasi Perumahan</h2>

          <div class="card-container">
            <div class="card">
              <i class="fas fa-house fa-2x text-success mb-2"></i>
              <h3>Jumlah Rumah</h3>
              <p><?= esc($jumlahrumah) ?> unit</p>
            </div>
            <div class="card">
              <i class="fas fa-users fa-2x text-primary mb-2"></i>
              <h3>Customer Terdaftar</h3>
              <p><?= esc($jumlahcustomer) ?> Orang</p>
            </div>
            <div class="card">
              <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
              <h3>Hasil Penjualan Rumah</h3>
              <p>Rp <?= number_format($totalpembelian, 0, ',', '.') ?></p>
            </div>
            <div class="card">
              <i class="fas fa-boxes fa-2x text-danger mb-2"></i>
              <h3>Stok Bahan Bangunan</h3>
              <p><?= number_format($stoktotal, 0, ',', '.') ?> stok</p>
            </div>
          </div>
            <div class="mt-4">
            <h4 class="mt-5">Grafik Penjualan Rumah per Bulan </h4>
            <div style="max-width: 100%; height: 400px;">
          <canvas id="salesChart"></canvas>
        </div>
          </div>
        </div>

        <footer class="footer1">
            <p>&copy; <?= date('Y') ?> Sistem Manajemen Informasi Perumahan. All rights reserved.</p>
        </footer>
    </div>

    <style>
      .content1 {
        padding: 20px;
      }

      .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
      }

      .card {
        flex: 1 1 250px;
        background: #f5f5f5;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
      }

      .card:hover {
        transform: scale(1.03);
      }

      .card h3 {
        margin-bottom: 10px;
        font-size: 20px;
        color: #333;
      }

      .card p {
        font-size: 16px;
        color: #666;
      }
    </style>
  </div>
</body>
<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar1");
    sidebar.classList.toggle("active");
  }

  window.onload = function () {
  $.get('/chart-penjualan-rumah', function (data) {
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
          label: 'Unit Terjual',
          data: data,
          backgroundColor: 'rgba(75, 192, 192, 0.6)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1,
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks : {
              precision: 0
            },
            title: {
              display: true,
              text: 'Jumlah Rumah'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Bulan'
            }
          }
        }
      }
    });
  });
};
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    this.parentElement.classList.toggle('aktif');
  });
});

 </script>

</html>
