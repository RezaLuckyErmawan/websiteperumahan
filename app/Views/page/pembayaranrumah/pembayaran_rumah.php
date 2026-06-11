<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pembayaran Cicilan Rumah | Sistem Manajemen Perumahan</title>
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
              <a class="menu-link active" href="/pembayaran-rumah"><span class="material-icons rotate-icon">payments</span> Pembayaran Cicilan Rumah</a>
              <a class="menu-link" href="/progres-pembayaran-rumah"><span class="material-icons rotate-icon">timeline</span> Data Progres Pembayaran Rumah</a>
            </div>
          </div>

          <?php if (($userRole ?? '') !== 'customer'): ?>
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
          <?php endif; ?>
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
          Pembayaran Cicilan Rumah
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
              <th>Verifikasi</th>
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

          #pembayaranRumahTable td:last-child {
            text-align: center;
          }

          .payment-actions {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
          }

          .payment-actions .btn {
            width: 32px;
            height: 32px;
            padding: 0;
          }

          .payment-detail-header {
            background: #f8fafc;
            border-bottom: 1px solid #e4e8ef;
          }

          .payment-detail-title {
            display: flex;
            align-items: center;
            gap: 10px;
          }

          .payment-detail-title .material-icons {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 20px;
          }

          .payment-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
          }

          .payment-summary-item {
            padding: 14px;
            border: 1px solid #e4e8ef;
            border-radius: 8px;
            background: #f8fafc;
          }

          .payment-summary-item span {
            display: block;
            margin-bottom: 6px;
            color: #647084;
            font-size: 12px;
            font-weight: 800;
          }

          .payment-summary-item strong {
            color: #172033;
            font-size: 16px;
            font-weight: 800;
          }

          .payment-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
          }

          .payment-detail-item {
            min-height: 66px;
            padding: 13px 14px;
            border: 1px solid #e4e8ef;
            border-radius: 8px;
            background: #ffffff;
          }

          .payment-detail-item.full {
            grid-column: 1 / -1;
          }

          .payment-detail-label {
            margin-bottom: 5px;
            color: #647084;
            font-size: 12px;
            font-weight: 800;
          }

          .payment-detail-value {
            color: #172033;
            font-size: 14px;
            font-weight: 700;
            overflow-wrap: anywhere;
          }

          @media (max-width: 768px) {
            .payment-summary,
            .payment-detail-grid {
              grid-template-columns: 1fr;
            }
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
                <div class="col-md-6 mb-3 admin-payment-field">
                  <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" name="tanggal_bayar" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" name="jumlah_bayar" min="1" placeholder="Contoh: 5000000" required>
                </div>
                <div class="col-md-6 mb-3 admin-payment-field">
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

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header payment-detail-header">
            <h5 class="modal-title payment-detail-title" id="detailModalLabel">
              <span class="material-icons">receipt_long</span>
              Detail Pembayaran Cicilan Rumah
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="payment-summary">
              <div class="payment-summary-item">
                <span>Jumlah Bayar</span>
                <strong id="detailJumlahBayar"></strong>
              </div>
              <div class="payment-summary-item">
                <span>Total Dibayar</span>
                <strong id="detailTotalBayar"></strong>
              </div>
              <div class="payment-summary-item">
                <span>Sisa Tagihan</span>
                <strong id="detailSisaBayar"></strong>
              </div>
            </div>

            <div class="payment-detail-grid">
              <div class="payment-detail-item">
                <div class="payment-detail-label">Customer</div>
                <div class="payment-detail-value" id="detailCustomer"></div>
              </div>
              <div class="payment-detail-item">
                <div class="payment-detail-label">Kode Rumah</div>
                <div class="payment-detail-value" id="detailKodeRumah"></div>
              </div>
              <div class="payment-detail-item">
                <div class="payment-detail-label">Tanggal Bayar</div>
                <div class="payment-detail-value" id="detailTanggalBayar"></div>
              </div>
              <div class="payment-detail-item">
                <div class="payment-detail-label">Status Pembelian</div>
                <div class="payment-detail-value" id="detailStatusPembelian"></div>
              </div>
              <div class="payment-detail-item">
                <div class="payment-detail-label">Status Verifikasi</div>
                <div class="payment-detail-value" id="detailStatusPengajuan"></div>
              </div>
              <div class="payment-detail-item">
                <div class="payment-detail-label">Jenis Pembayaran</div>
                <div class="payment-detail-value" id="detailJenisPembayaran"></div>
              </div>
              <div class="payment-detail-item">
                <div class="payment-detail-label">Metode Bayar</div>
                <div class="payment-detail-value" id="detailMetodeBayar"></div>
              </div>
              <div class="payment-detail-item full">
                <div class="payment-detail-label">Keterangan</div>
                <div class="payment-detail-value" id="detailKeterangan"></div>
              </div>
              <div class="payment-detail-item full">
                <div class="payment-detail-label">Bukti Pembayaran</div>
                <div class="payment-detail-value" id="detailBuktiBayar"></div>
              </div>
            </div>
          </div>
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
    window.canCreatePayments = <?= ($canCreatePayments ?? true) ? 'true' : 'false' ?>;
    window.canModifyPayments = <?= ($canModifyPayments ?? true) ? 'true' : 'false' ?>;

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
