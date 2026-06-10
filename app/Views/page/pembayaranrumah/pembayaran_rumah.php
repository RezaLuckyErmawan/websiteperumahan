<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Pembayaran Rumah | Sistem Manajemen Perumahan</title>
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
          <a class="menu-link" href="/dashboard"><span class="material-icons rotate-icon">dashboard</span> Dashboard</a>

          <div class="menu-dropdown">
            <button class="dropdown-btn">
              <span class="material-icons rotate-icon">analytics</span> Marketing
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container">
              <a class="menu-link" href="/data-customer"><span class="material-icons rotate-icon">groups</span> Data Customer</a>
              <a class="menu-link" href="/pembatalan-transaksi"><span class="material-icons rotate-icon">remove_shopping_cart</span> Pembatalan Transaksi</a>
            </div>
          </div>

          <div class="menu-dropdown">
            <button class="dropdown-btn">
              <span class="material-icons rotate-icon">business_center</span> Manajemen Proyek
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container">
              <a class="menu-link" href="/data-bahan" style="margin-top: 10px;"><span class="material-icons rotate-icon">construction</span> Bahan Bangunan</a>
              <a class="menu-link" href="/data-rumah"><span class="material-icons rotate-icon">home_work</span> Data Rumah</a>
              <a class="menu-link" href="/rab-rumah"><span class="material-icons rotate-icon">description</span> RAB Rumah</a>
              <a class="menu-link" href="/rab-bahan"><span class="material-icons rotate-icon">description</span> RAB Bahan</a>
              <a class="menu-link" href="/rab-pekerja"><span class="material-icons rotate-icon">description</span> RAB Pekerja</a>
              <a class="menu-link" href="/realisasi-rumah"><span class="material-icons rotate-icon">description</span> Realisasi Rumah</a>
              <a class="menu-link" href="/realisasi-bahan"><span class="material-icons rotate-icon">description</span> Realisasi Bahan</a>
              <a class="menu-link" href="/realisasi-pekerja"><span class="material-icons rotate-icon">description</span> Realisasi Pekerja</a>
              <a class="menu-link" href="/data-bahan-pembangunan"><span class="material-icons rotate-icon">business</span> Data Bahan Pembangunan</a>
              <a class="menu-link" href="/pekerjaan-insidentil"><span class="material-icons rotate-icon">architecture</span> Data Pekerjaan Insidentil</a>
            </div>
          </div>

          <div class="menu-dropdown">
            <button class="dropdown-btn">
              <span class="material-icons rotate-icon">fact_check</span> Manajemen Logistik
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container">
              <a class="menu-link" href="/data-pembelian-bahan"><span class="material-icons rotate-icon">shopping_cart</span> Data Pembelian Bahan</a>
              <a class="menu-link" href="/detail-pembelian-bahan"><span class="material-icons rotate-icon">receipt_long</span> Detail Pembelian Bahan</a>
            </div>
          </div>

          <div class="menu-dropdown aktif">
            <button class="dropdown-btn">
              <span class="material-icons rotate-icon">monetization_on</span> Keuangan
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container" style="margin-top: 10px;">
              <a class="menu-link" href="/pembelian-rumah"><span class="material-icons rotate-icon">real_estate_agent</span> Data Penjualan Rumah</a>
              <a class="menu-link active" href="/pembayaran-rumah"><span class="material-icons rotate-icon">payments</span> Data Pembayaran Rumah</a>
            </div>
          </div>

          <div class="menu-dropdown">
            <button class="dropdown-btn">
              <span class="material-icons rotate-icon">folder_open</span> Menu Master
              <span class="material-icons arrow">expand_more</span>
            </button>
            <div class="dropdown-container">
              <a class="menu-link" href="/data-user"><span class="material-icons rotate-icon">groups</span> Data User</a>
              <a class="menu-link" href="/data-mandor"><span class="material-icons rotate-icon">engineering</span> Data Mandor</a>
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
        <div class="page-title">
          Data Pembayaran Rumah
        </div>
        <div class="actions">
          <div class="profile">
            <img src="https://i.pravatar.cc/40" alt="Profile">
          </div>
        </div>
      </div>

      <div class="content1">
<table id="pembayaranRumahTable" class="display table table-striped table-bordered w-100">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Kode Rumah</th>
              <th>Tanggal Bayar</th>
              <th>Jenis</th>
              <th>Jumlah Bayar</th>
              <th>Total Dibayar</th>
              <th>Sisa Tagihan</th>
              <th>Status</th>
              <th>Bukti</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <style>
          #pembayaranRumahTable thead th {
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

    <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalFormLabel">Tambah Pembayaran Rumah</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form onsubmit="event.preventDefault(); simpanForm();">
            <div class="modal-body">
              <input type="hidden" name="id">

              <div class="mb-3">
                <label class="form-label">Transaksi Rumah <span class="text-danger">*</span></label>
                <select class="form-control" name="pembelian_rumah_id" onchange="updateRingkasan()" required>
                  <option value="">Pilih customer dan rumah yang akan dibayar</option>
                  <?php foreach ($pembelian as $item): ?>
                    <option value="<?= esc($item['id']) ?>">
                      <?= esc($item['nama_customer']) ?> - <?= esc($item['kode_rumah']) ?> - Rp <?= number_format($item['harga_beli'], 0, ',', '.') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="alert alert-info" id="ringkasanPembayaran">
                <span class="text-muted">Pilih transaksi rumah untuk melihat sisa tagihan.</span>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" name="tanggal_bayar" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" name="jumlah_bayar" min="1" placeholder="Contoh: 5000000" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                  <select class="form-control" name="jenis_pembayaran" required>
                    <option value="">Pilih jenis pembayaran</option>
                    <option value="booking_fee">Booking Fee</option>
                    <option value="dp">DP</option>
                    <option value="cicilan">Cicilan</option>
                    <option value="pelunasan">Pelunasan</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Metode Bayar <span class="text-danger">*</span></label>
                  <select class="form-control" name="metode_bayar" required>
                    <option value="">Pilih metode pembayaran</option>
                    <option value="Cash">Cash</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="Cicilan Internal">Cicilan Internal</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control" name="keterangan" rows="3" placeholder="Contoh: Cicilan bulan pertama, transfer BCA, atau catatan pembayaran lainnya"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Bukti Pembayaran</label>
                <input type="file" class="form-control" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf">
                <small class="text-muted">Format JPG, PNG, atau PDF. Maksimal 2 MB.</small>
                <div id="buktiSaatIni" class="mt-2"></div>
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

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">Apakah kamu yakin ingin menghapus pembayaran ini?</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-success text-black">
          <div class="modal-header border-0">
            <h5 class="modal-title" id="successModalLabel">Berhasil</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <p id="successMessage">Data berhasil diproses.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    window.selectedPembelianId = "<?= esc($selectedPembelianId ?? '') ?>";

    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("active");
    }

    function showSuccess(message = 'Data berhasil diproses.') {
      $('#successMessage').text(message);
      const modal = new bootstrap.Modal(document.getElementById('successModal'));
      modal.show();
      setTimeout(() => modal.hide(), 2500);
    }

    document.querySelectorAll('.dropdown-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        this.parentElement.classList.toggle('aktif');
      });
    });
  </script>
  <script src="<?= base_url('assets/js/pembayaranrumah.js') ?>"></script>
</body>
</html>
