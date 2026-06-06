<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Admin</title>
 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
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
              <a class="menu-link active" href="/data-bahan-pembangunan">
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
        <h2>Data Bahan Bangunan</h2>
          <button onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus me-1"></i> Tambah Data
          </button>


        <div class="table-responsive">

        <table id="bahanPembangunanTable"  class="display table table-bordered">
            <thead>
                <tr>
                    <th>Kode Rumah</th>
                    <th>Nama Bahan</th>
                    <th>Jumlah Pemakaian</th>
                    <th>Tanggal Pemakaian</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
               
            </tbody>
        </table>
         <style>
          #bahanPembangunanTable thead th {
              background-color: #eef6f8;
              color: #203246;
              text-align: center;
          }
    </style>
        <!-- Modal Tambah/Edit Bahan Pembangunan Rumah -->
        <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <form id="formBahanPembangunan" onsubmit="event.preventDefault(); simpanForm();">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalFormLabel">Form Tambah Bahan Pembangunan Rumah</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="id"> <!-- untuk kebutuhan edit -->

                  <div class="mb-3">
                    <label for="perumahan_id" class="form-label">Rumah (Perumahan)</label>
                    <select name="perumahan_id" class="form-select" required>
                      <option value="">-- Pilih Rumah --</option>
                      <?php foreach ($perumahan as $p): ?>
                        <option value="<?= $p['id'] ?>">
                          <?= $p['id'] ?> - <?= $p['kode_rumah'] ?> (<?= $p['lokasi'] ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label for="bahan_bangunan_id" class="form-label">Bahan Bangunan</label>
                    <select name="bahan_bangunan_id" class="form-select" required>
                      <option value="">-- Pilih Bahan --</option>
                      <?php foreach ($bahan as $b): ?>
                        <option value="<?= $b['id'] ?>">
                          <?= $b['id'] ?> - <?= $b['nama_bahan'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label for="jumlah_pemakaian" class="form-label">Jumlah Pemakaian</label>
                    <input type="number" class="form-control" name="jumlah_pemakaian" required>
                  </div>

                  <div class="mb-3">
                    <label for="tanggal_penggunaan" class="form-label">Tanggal Penggunaan</label>
                    <input type="date" class="form-control" name="tanggal_penggunaan" required>
                  </div>

                  <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                    <input type="text" class="form-control" name="keterangan" placeholder="Contoh: Lantai 1">
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
        
        <!-- MODAL KONFIRMASI DELETE -->
        <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-labelledby="modalConfirmDelete" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                  <h5 class="modal-title">Konfirmasi Hapus</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="close"></button>
                </div>
                <div class="modal-body">
                  <p>Yakin iingin menghapus data ini ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnDeleteConfirm">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
              </div>
            </div>
        </div>
        
        <!-- MODAL PESAN BERHASIL -->
         <div class="modal fade" id="successmodal" tabindex="-1" aria-labelledby="successModalLabel" aria-label="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                  <div class="modal-content bg-success text-black">
                    <div class="modal-header border-0">
                      <h5 class="modal-title" id="successModalLabel">✔️ Berhasil</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="close"></button>
                    </div>
                    <div class="modal-body text-center">
                      <p id="successMessage">Data Berhasil diproses.</p>
                    </div>
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
  <script src="<?= base_url('assets/js/bahanpembangunanrumah.js') ?>"></script>

  <script>
    // Tutup otomatis setelah 5 detik jika tidak diklik
    setTimeout(() => {
      const modal = document.getElementById('flashModal');
      if (modal) modal.style.display = 'none';
    }, 5000);
    document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', function () {
    this.parentElement.classList.toggle('aktif');
    });
  });
  </script>
</body>
</html>
