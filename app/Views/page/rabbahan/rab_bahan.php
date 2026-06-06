<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RAB Bahan | Sistem Manajemen Perumahan</title>
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
               <a class="menu-link active" href="/rab-bahan">
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
        <div class="page-title">
          RAB Bahan
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
<table  id="dataRabTable" class="display table table-striped table-bordered w-100">
              <thead>
                  <tr>
                      <th>kode Rab</th>
                      <th>Nama Bahan</th>
                      <th>Jumalah rencana</th>
                      <th>Harga Satuan</th>
                      <th>Total Harga</th>
                      <th>Aksi</th>
                  </tr>
              </thead>
              <tbody>
                 
              </tbody>
          </table>
      </div>
        <footer class="footer1">
            <p>&copy; <?= date('Y') ?> Sistem Manajemen Informasi Perumahan. All rights reserved.</p>
        </footer>
    </div>
   <!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalForm" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formRabBahan">
        <div class="modal-header">
          <h5 class="modal-title" id="modalFormLabel">Tambah Data RAB Bahan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id">
          <div class="mb-3">
            <label>Kode RAB</label>
            <select name="rab_id" class="form-control" required>
              <option value="">-- pilih Kode Rab -- </option>
              <?php foreach ($rabrumah as $rab): ?>
                <option value="<?= $rab['id'] ?>"><?= $rab['kode_rab'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <table class="table table-bordered" id="bahanTable">
            <thead>
              <tr>
                <th>Bahan</th>
                <th>stok</th>
                <th>Jumlah Rencana</th>
                <th>Harga Satuan</th>
                <th>satuan</th>
                <th><button type="button" id="addRow" class="btn btn-success btn-sm">+</button></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <select name="bahanbangunan_id[]" class="form-control bahanSelect">
                    <option value="">-- Bahan Bangunan --</option>
                    <?php foreach ($bahan as $b): ?>
                    <option value="<?= $b['id'] ?>" 
                            data-satuan="<?= $b['satuan'] ?>" 
                            data-stok="<?= $b['stok'] ?>">
                      <?= $b['nama_bahan'] ?> (Stok: <?= $b['stok'] ?>)
                    </option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td><input type="number" name="stok[]" class="form-control stokField" readonly></td>
                <td><input type="number" name="jumlah_rencana[]" class="form-control" required></td>
                <td><input type="number" name="harga_satuan[]" class="form-control" required></td>
                <td><input type="text" name="satuan[]" class="form-control satuanField" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
              </tr>
            </tbody>
            
          </table>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>


  <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-labelledby="modalConfirmDelete" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus data ini?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="btnDeleteConfirm">Hapus</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>


    <!-- MODAL KONFIRMASI PESAN BERHASIL -->
    <div class="modal fade" id="successmodal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content bg-success text-black">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="successModalLabel">✔️ Berhasil</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <p id="successMessage">Data berhasil diproses.</p>
        </div>
      </div>
      </div>
    </div>
   <style>
    #dataRabTable thead th {
        background-color: #eef6f8;
        color: #203246;
        text-align: center;
    }
    </style>     
  </div>
  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("active");
      
    }
    function showSuccess(message = 'Data berhasil diproses.') {
        $('#successMessage').text(message);
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
        setTimeout(() => {
          modal.hide();
        }, 2500);
    }
  </script>
  <script src="<?= base_url('assets/js/rabbahan.js') ?>"></script>
    
</body>
<script>
     document.querySelectorAll('.dropdown-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    this.parentElement.classList.toggle('aktif');
  });
});
</script>
<script>
let bahanOptions = `
  <option value="">-- Bahan Bangunan --</option>
  <?php foreach ($bahan as $b): ?>
    <option value="<?= $b['id'] ?>" 
            data-satuan="<?= $b['satuan'] ?>" 
            data-stok="<?= $b['stok'] ?>">
      <?= $b['nama_bahan'] ?> (Stok: <?= $b['stok'] ?>)
    </option>
  <?php endforeach; ?>
`;
</script>

</html>
