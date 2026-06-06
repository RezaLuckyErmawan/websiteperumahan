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
</head>
<body>
  <div class="container1">
    <!-- Sidebar -->
    <div class="sidebar1" id="sidebar">
      <div>
        <h4>
          Sistem Manajemen Perumahan
        </h4>
        <div class="nav1">
          <a class=" menu-link" href="/dashboard"><span class="material-icons rotate-icon">dashboard</span> Dashboard</a>
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
              <a class="menu-link active" href="data-pembelian-bahan" style="margin-top: 10px;"><span class="material-icons rotate-icon">shopping_cart</span> Data Pembelian Bahan</a>
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
          <div class="notifications">
            <span class="material-icons">notifications</span>
            <span class="badge">3</span>
          </div>
          <div class="profile">
            <img src="https://i.pravatar.cc/40" alt="Profile">
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="content1">
        <h2>Sistem Manajemen Informasi Perumahan</h1>
        <h2>Data Pembelian</h2>
        <button onclick="openCreateForm()" class="add-btn1">
              <i class="fas fa-plus me-1"></i> Tambah Data
        </button>
          <table id="pembelianTable" class="table table-bordered">
              <thead>
                <tr>
                 
                  <th>Nomor Nota</th>
                  <th>Tanggal</th>
                  <th>Supplier</th>
                  <th>Total Harga</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
          </table>
           <style>
              #pembelianTable thead th {
                  background-color: #eef6f8;
                  color: #203246;
                  text-align: center;
              }
            </style>

          <!-- Modal Form Tambah/Edit Pembelian -->
          <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalFormLabel">Tambah Pembelian</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form onsubmit="event.preventDefault(); simpanForm();">
                  <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                      <label for="nomor_nota" class="form-label">Nomor Nota</label>
                      <input type="text" class="form-control" name="nomor_nota" required>
                    </div>
                    <div class="mb-3">
                      <label for="tanggal" class="form-label">Tanggal</label>
                      <input type="date" class="form-control" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                      <label for="supplier" class="form-label">Supplier</label>
                      <input type="text" class="form-control" name="supplier" required>
                    </div>
                    <div class="mb-3">
                      <label for="total_harga" class="form-label">Total Harga</label>
                      <input type="number" class="form-control" name="total_harga" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Modal konirmasi dihapus -->
          <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                  <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="close"></button>
                </div>
                <div class="modal-body">
                  Apakah Kamu Yakin Ingin Menghapus Data Ini?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal pesan berhasil -->
           <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content bg-success text-black">
                  <div class="modal-header border-0">
                    <h5 class="modal-title" id="successModalLabel">✔️ Berhasil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="close"></button>
                  </div>
                  <div class="modal-body text-center">
                      <p id="successMessage">Data Berhasil dihapus</p>
                  </div>
                </div>
              </div>
           </div>
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

    function showSuccess(message = 'Data berhasil diproses.') {
  $('#successMessage').text(message); // Ganti isi modal
  const modal = new bootstrap.Modal(document.getElementById('successModal'));
  modal.show();
  
  // Auto-close setelah 2.5 detik
  setTimeout(() => {
    modal.hide();
  }, 2500);
}

 document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', function () {
    this.parentElement.classList.toggle('aktif');
    });
  });
  </script>
  <script src="<?= base_url('assets/js/datapembelian.js') ?>"></script>
</body>
</html>
