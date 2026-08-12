<?php
$pageTitle = 'Laporan Keuangan';
$useDataTables = true;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
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
              <a class="menu-link" href="/detail-pembelian-list"><span class="material-icons rotate-icon">person</span> Detail Pembelian Rumah</a>
              <a class="menu-link" href="/pembayaran-rumah"><span class="material-icons rotate-icon">payments</span> Pembayaran Cicilan Rumah</a>
              <a class="menu-link" href="/progres-pembayaran-rumah"><span class="material-icons rotate-icon">timeline</span> Data Progres Pembayaran Rumah</a>
              <a class="menu-link active" href="/laporan"><span class="material-icons rotate-icon">picture_as_pdf</span> Laporan</a>
            </div>
          </div>

          <?php if (in_array(($userRole ?? ''), ['admin', 'owner'], true)): ?>
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
        <div class="page-title">Laporan Keuangan</div>
        <div class="actions">
          <div class="profile">
            <img src="https://i.pravatar.cc/40" alt="Profile">
          </div>
        </div>
      </div>

      <div class="content1">
        <?php
          /** @var list<array<string, mixed>> $penjualan */
          /** @var list<array<string, mixed>> $cicilan */
          $penjualan = is_array($penjualan ?? null) ? $penjualan : [];
          $cicilan = is_array($cicilan ?? null) ? $cicilan : [];
        ?>
        <div class="laporan-grid">
          <div class="laporan-card">
            <div class="laporan-card-head">
              <div>
                <h3>Laporan Penjualan Rumah</h3>
                <p>Rekap seluruh penjualan rumah (kecuali batal).</p>
              </div>
              <a href="/laporan/pdf-penjualan" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Cetak PDF
              </a>
            </div>
            <table id="penjualanTable" class="display table table-striped table-bordered w-100">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Customer</th>
                  <th>Kode Rumah</th>
                  <th>Tanggal</th>
                  <th>Harga Beli</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach ($penjualan as $row): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['nama_customer']) ?></td>
                    <td><?= esc($row['kode_rumah']) ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal_pembelian'])) ?></td>
                    <td>Rp <?= number_format((float) $row['harga_beli'], 0, ',', '.') ?></td>
                    <td><?= esc(ucfirst((string) $row['status_pembelian'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="laporan-card">
            <div class="laporan-card-head">
              <div>
                <h3>Laporan Cicilan</h3>
                <p>Data customer yang masih cicilan / DP beserta sisa tagihan.</p>
              </div>
              <a href="/laporan/pdf-cicilan" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Cetak PDF
              </a>
            </div>
            <table id="cicilanTable" class="display table table-striped table-bordered w-100">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Customer</th>
                  <th>Kode Rumah</th>
                  <th>Total Dibayar</th>
                  <th>Sisa Tagihan</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach ($cicilan as $row): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['nama_customer']) ?></td>
                    <td><?= esc($row['kode_rumah']) ?></td>
                    <td>Rp <?= number_format((float) $row['total_bayar'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format((float) $row['sisa_bayar'], 0, ',', '.') ?></td>
                    <td><?= esc(ucfirst((string) $row['status_pembelian'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <style>
          .laporan-grid {
            display: grid;
            gap: 18px;
          }

          .laporan-card {
            background: #fff;
            border: 1px solid #e4e8ef;
            border-radius: 10px;
            padding: 18px;
          }

          .laporan-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
          }

          .laporan-card-head h3 {
            margin: 0 0 4px;
            font-size: 18px;
            font-weight: 800;
            color: #172033;
          }

          .laporan-card-head p {
            margin: 0;
            color: #647084;
            font-size: 13px;
          }

          #penjualanTable thead th,
          #cicilanTable thead th {
            background-color: #eef6f8;
            color: #203246;
            text-align: center;
          }

          @media (max-width: 768px) {
            .laporan-card-head {
              flex-direction: column;
            }
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
      document.getElementById("sidebar").classList.toggle("active");
    }

    document.querySelectorAll('.dropdown-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        this.parentElement.classList.toggle('aktif');
      });
    });

    $(document).ready(function () {
      $('#penjualanTable, #cicilanTable').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        }
      });
    });
  </script>
  </div>
<?= $this->endSection() ?>
