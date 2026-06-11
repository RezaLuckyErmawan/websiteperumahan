<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Penjualan Rumah | Sistem Manajemen Perumahan</title>
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
          Sistem Manajemen Informasi Perumahan
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
           <div class="dropdown-container" style="margin-top: 10px;">
            <a class="menu-link active" href="/pembelian-rumah"><span class="material-icons rotate-icon">real_estate_agent</span> Data Penjualan Rumah</a>
            <a class="menu-link" href="/pembayaran-rumah"><span class="material-icons rotate-icon">payments</span> Pembayaran Cicilan Rumah</a>
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
          Penjualan Rumah
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
<table  id="pembelianRumahTable" class="display table table-striped table-bordered w-100">
              <thead>
                  <tr>
                    
                      <th>Nama Customer</th>
                      <th>Kode Rumah</th>
                      <th>Tanggal</th>
                      <th>Harga</th>
                      <th>Terbayar</th>
                      <th>Sisa</th>
                      <th>Status</th>
                      <th>Metode</th>
                      <th>action</th>
                  </tr>
              </thead>
              <tbody>
                 
              </tbody>
          </table>
           <style>
            #pembelianRumahTable thead th {
                background-color: #eef6f8;
                color: #203246;
                text-align: center;
            }
          </style>
      </div>
        <footer class="footer1">
            <p>&copy; <?= date('Y') ?> Sistem Manajemen Informasi Perumahan. All rights reserved.</p>
        </footer>
    </div>

    <!-- Modal Tambah dan Edit -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Form Pembelian Rumah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Form -->
            <form onsubmit="event.preventDefault(); simpanForm();">
                <div class="modal-body">

                    <input type="hidden" name="id">

                    <!-- Customer -->
                    <div class="mb-3">
                        <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
                        <select class="form-control" name="customer_id" required>
                            <option value="">-- Nama Customer --</option>
                            <?php foreach ($customer as $cs): ?>
                                <option value="<?= $cs['id']; ?>">
                                    <?= $cs['nama']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Kode Rumah -->
                    <div class="mb-3">
                        <label class="form-label">Kode Rumah <span class="text-danger">*</span></label>
                        <select name="perumahan_id" class="form-control" required>
                            <option value="">-- Pilih Rumah --</option>

                            <?php foreach ($perumahan as $item): ?>
                                <?php
                                    switch (strtolower($item['status'])) {
                                        case 'terjual':
                                            $style = 'background-color:#f8d7da;color:#842029;';
                                            $labelStatus = ' (Sudah Terjual)';
                                            break;
                                        case 'proses pembangunan':
                                            $style = 'background-color:#fff3cd;color:#856404;';
                                            $labelStatus = ' (Proses Pembangunan)';
                                            break;
                                        case 'dijual':
                                            $style = 'background-color:#d4edda;color:#155724;';
                                            $labelStatus = ' (Dijual)';
                                            break;
                                        default:
                                            $style = '';
                                            $labelStatus = '';
                                            break;
                                    }
                                ?>
                                <option value="<?= $item['id']; ?>"
                                data-harga="<?= $item['harga']; ?>"
                                data-sold="<?= strtolower($item['status']) === 'terjual' ? '1' : '0'; ?>"
                                style="<?= $style; ?>"
                                <?= strtolower($item['status']) === 'terjual' ? 'disabled' : ''; ?>>
                                <?= $item['kode_rumah']; ?><?= $labelStatus; ?>
                              </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_pembelian" required>
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control"
                               name="harga_beli"
                               placeholder="Harga otomatis dari data rumah"
                               readonly
                               required>
                    </div>

                    <!-- Status Pembelian -->
                    <div class="mb-3">
                        <label class="form-label">Status Pembelian <span class="text-danger">*</span></label>
                        <select name="status_pembelian" class="form-control" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Lunas">Lunas</option>
                            <option value="Cicil">Cicil</option>
                            <option value="DP">DP</option>
                            <option value="Batal">Batal</option>
                        </select>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="metode_pembayaran" class="form-control" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="Cash">Cash</option>
                            <option value="Cicilan Internal">Cicilan Internal</option>
                        </select>
                    </div>

                    <!-- Status Dokumen -->
                    <div class="mb-3">
                        <label class="form-label">Status Dokumen <span class="text-danger">*</span></label>
                        <select name="status_dokumen" class="form-control" required>
                            <option value="">-- Pilih Status Dokumen --</option>
                            <option value="Lengkap">Lengkap</option>
                            <option value="Pending">Pending</option>
                            <option value="Verifikasi">Verifikasi</option>
                        </select>
                    </div>

                    <!-- Request Khusus -->
                    <div class="mb-3">
                        <label class="form-label">Request Khusus</label>
                        <textarea class="form-control" name="request_khusus" rows="2"
                                  placeholder="Contoh: Tambah pagar depan"></textarea>
                    </div>

                    <!-- Catatan Marketing -->
                    <div class="mb-3">
                        <label class="form-label">Catatan Marketing</label>
                        <textarea class="form-control" name="catatan_marketing" rows="2"
                                  placeholder="Contoh: Dari pameran Expo, Instagram Ads, dll."></textarea>
                    </div>

                    <!-- Pembatalan -->
                    <div class="mb-3" id="sectionPembatalan">
                        <label class="form-label">Pembatalan Transaksi</label><br>

                        <button type="button"
                                id="btnBatal"
                                class="btn btn-sm btn-danger mb-1"
                                data-bs-toggle="collapse"
                                data-bs-target="#textareaBatal"
                                aria-expanded="false"
                                aria-controls="textareaBatal">
                            Batalkan Pembelian ?
                        </button>

                        <div class="collapse mt-2" id="textareaBatal">
                            <label class="form-label">Keterangan Pembatalan</label>
                            <textarea class="form-control"
                                      id="keterangan_batal"
                                      name="alasan_pembatalan"
                                      rows="3"></textarea>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>


    <!-- Modal Detail Data -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="detailModalLabel">Detail Pembelian Rumah</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <tbody>
                <tr><th>Nama Customer</th><td id="detailCustomer"></td></tr>
                <tr><th>Kode Rumah</th><td id="detailKodeRumah"></td></tr>
                <tr><th>Tanggal</th><td id="detailTanggal"></td></tr>
                <tr><th>Harga</th><td id="detailHarga"></td></tr>
                <tr><th>Total Dibayar</th><td id="detailTotalBayar"></td></tr>
                <tr><th>Sisa Tagihan</th><td id="detailSisaBayar"></td></tr>
                <tr><th>Status Pembelian</th><td id="detailStatusPembelian"></td></tr>
                <tr><th>Metode Pembayaran</th><td id="detailMetode"></td></tr>
                <tr><th>Status Dokumen</th><td id="detailDokumen"></td></tr>
                <tr><th>Request Khusus</th><td id="detailRequest"></td></tr>
                <tr><th>Catatan Marketing</th><td id="detailCatatan"></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL HAPUS -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                Apakah kamu yakin ingin menghapus data ini?
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary btn-sm"
                        data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="button"
                        class="btn btn-danger btn-sm"
                        id="confirmDeleteBtn">
                    Hapus
                </button>
            </div>

        </div>
    </div>
</div>

    <!-- MODAL KONFIRMASI PESAN BERHASIL DIHAPUS -->
    <div class="modal fade"
     id="successModal"
     tabindex="-1"
     aria-labelledby="successModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-success text-black">

            <!-- Header -->
            <div class="modal-header border-0">
                <h5 class="modal-title" id="successModalLabel">✔️ Berhasil</h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body text-center">
                <p id="successMessage">Data berhasil dihapus!</p>
            </div>

        </div>
    </div>
</div>
</div>

  <script>
let idHapus = null;

/* =======================
   SIDEBAR
======================= */
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
}

/* =======================
   MODAL SUCCESS
======================= */
function showSuccess(message = 'Data berhasil diproses.') {
    $('#successMessage').text(message);

    const modal = new bootstrap.Modal(
        document.getElementById('successModal')
    );

    modal.show();

    setTimeout(() => {
        modal.hide();
    }, 2500);
}

/* =======================
   DROPDOWN MENU
======================= */
document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        this.parentElement.classList.toggle('aktif');
    });
});

/* =======================
   AUTO ISI HARGA RUMAH
======================= */
document
    .querySelector('select[name="perumahan_id"]')
    .addEventListener('change', function () {

        const harga = this.options[this.selectedIndex]
            .getAttribute('data-harga');

        document.querySelector('input[name="harga_beli"]').value =
            harga ? harga : "";
    });

/* =======================
   MODAL TAMBAH / EDIT
======================= */
function bukaTambah() {
    document.getElementById('modalFormLabel').innerText =
        'Tambah Pembelian Rumah';

    document.querySelector('input[name="id"]').value = "";
}

function bukaEdit() {
    document.getElementById('modalFormLabel').innerText =
        'Edit Pembelian Rumah';

    document.getElementById('sectionPembatalan').style.display = 'block';
}

/* =======================
   TOGGLE SECTION PEMBATALAN
======================= */
const modalForm = document.getElementById('modalForm');

modalForm.addEventListener('shown.bs.modal', function () {
    const id = modalForm.querySelector('input[name="id"]').value;
    const sectionPembatalan = document.getElementById('sectionPembatalan');

    if (!id) {
        sectionPembatalan.style.display = 'none';
    } else {
        sectionPembatalan.style.display = 'block';
    }
});

/* =======================
   HAPUS DATA
======================= */
function hapusData(id) {
    idHapus = id;

    const modalEl = document.getElementById('confirmDeleteModal');
    const modal = new bootstrap.Modal(modalEl);

    modal.show();
}

/* =======================
   KONFIRMASI HAPUS
======================= */
document
    .getElementById('confirmDeleteBtn')
    .addEventListener('click', function () {

        if (!idHapus) {
            console.warn('ID belum diset');
            return;
        }

        $.ajax({
            url: "<?= base_url('pembelian-rumah/delete') ?>/" + idHapus,
            type: "DELETE",
            success: function () {

                const modalEl =
                    document.getElementById('confirmDeleteModal');

                const modal =
                    bootstrap.Modal.getInstance(modalEl);

                modal.hide();

                $('#pembelianRumahTable')
                    .DataTable()
                    .ajax
                    .reload(null, false);

                showSuccess('Data berhasil dihapus!');
                idHapus = null;
            },
            error: function () {
                alert('Gagal menghapus data!');
            }
        });
    });
</script>

  <script src="<?= base_url('assets/js/pembelianrumah.js') ?>"></script>
    
</body>
</html>
