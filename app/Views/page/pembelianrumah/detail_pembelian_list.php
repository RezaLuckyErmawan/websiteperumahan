<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Pembelian Rumah | Sistem Manajemen Perumahan</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <a class="menu-link active" href="/detail-pembelian-list"><span class="material-icons rotate-icon">person</span> Detail Pembelian Rumah</a>
              <?php else: ?>
                <a class="menu-link active" href="/detail-pembelian-list"><span class="material-icons rotate-icon">person</span> Detail Pembelian Rumah</a>
              <?php endif; ?>
              <a class="menu-link" href="/pembayaran-rumah"><span class="material-icons rotate-icon">payments</span> Pembayaran Cicilan Rumah</a>
              <a class="menu-link" href="/progres-pembayaran-rumah"><span class="material-icons rotate-icon">timeline</span> Data Progres Pembayaran Rumah</a>
              <?php if (($userRole ?? '') === 'admin'): ?>
                <a class="menu-link" href="/laporan"><span class="material-icons rotate-icon">picture_as_pdf</span> Laporan</a>
              <?php endif; ?>
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
        <div class="page-title">
          Detail Pembelian Rumah
        </div>
        <div class="actions">
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
            >
          </div>
        </div>
      </div>

      <div class="content1">
        <?php
          /**
           * Data dari PembelianRumahController::detailPembelianList()
           *
           * @var array<int, array<string, mixed>> $pembelian
           */
          if (!isset($pembelian) || !is_array($pembelian)) {
            $pembelian = [];
          }

          $totalPembelian = count($pembelian);
          $totalLunas = count(array_filter($pembelian, static fn($p) => strtolower((string) ($p['status_pembelian'] ?? '')) === 'lunas'));
          $totalCicilan = count(array_filter($pembelian, static fn($p) => in_array(strtolower((string) ($p['status_pembelian'] ?? '')), ['cicil', 'dp'], true)));
          $totalOmzet = array_sum(array_column($pembelian, 'harga_beli'));
        ?>

        <div class="purchase-summary">
          <div class="purchase-summary-item">
            <span>Total Pembelian</span>
            <strong><?= $totalPembelian ?></strong>
          </div>
          <div class="purchase-summary-item">
            <span>Total Lunas</span>
            <strong><?= $totalLunas ?></strong>
          </div>
          <div class="purchase-summary-item">
            <span>Sedang Cicilan</span>
            <strong><?= $totalCicilan ?></strong>
          </div>
          <div class="purchase-summary-item">
            <span>Total Omzet</span>
            <strong>Rp <?= number_format($totalOmzet, 0, ',', '.') ?></strong>
          </div>
        </div>

        <table id="pembelianListTable" class="display table table-striped table-bordered w-100">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Customer</th>
              <th>Kode Rumah</th>
              <th>Tipe</th>
              <th>Tanggal Pembelian</th>
              <th>Harga Beli</th>
              <th>Total Dibayar</th>
              <th>Sisa Tagihan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; foreach ($pembelian as $p): ?>
              <?php
                $status = strtolower($p['status_pembelian']);
                $statusClass = 'status-secondary';
                if ($status === 'lunas') $statusClass = 'status-success';
                elseif ($status === 'cicil') $statusClass = 'status-primary';
                elseif ($status === 'dp') $statusClass = 'status-warning';
                elseif ($status === 'batal') $statusClass = 'status-danger';
              ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>
                  <div class="customer-cell">
                    <strong><?= esc($p['nama_customer']) ?></strong>
                    <small><?= esc($p['telepon_customer']) ?></small>
                  </div>
                </td>
                <td><span class="code-pill"><?= esc($p['kode_rumah']) ?></span></td>
                <td><?= esc($p['tipe_rumah']) ?></td>
                <td><?= date('d-m-Y', strtotime($p['tanggal_pembelian'])) ?></td>
                <td>Rp <?= number_format($p['harga_beli'], 0, ',', '.') ?></td>
                <td class="amount-paid">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                <td class="amount-remain">Rp <?= number_format($p['sisa_bayar'], 0, ',', '.') ?></td>
                <td><span class="status-pill <?= $statusClass ?>"><?= ucfirst($p['status_pembelian']) ?></span></td>
                <td>
                  <div class="payment-actions">
                    <a href="/detail-pembelian-rumah/<?= $p['id'] ?>" class="btn btn-sm btn-primary" title="Detail">
                      <i class="fas fa-eye"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <style>
          .purchase-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
          }

          .purchase-summary-item {
            padding: 14px;
            border: 1px solid #e4e8ef;
            border-radius: 8px;
            background: #f8fafc;
          }

          .purchase-summary-item span {
            display: block;
            margin-bottom: 6px;
            color: #647084;
            font-size: 12px;
            font-weight: 800;
          }

          .purchase-summary-item strong {
            color: #172033;
            font-size: 16px;
            font-weight: 800;
          }

          #pembelianListTable thead th {
            background-color: #eef6f8;
            color: #203246;
            text-align: center;
          }

          #pembelianListTable td {
            vertical-align: middle;
          }

          #pembelianListTable td:last-child {
            text-align: center;
          }

          .customer-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
          }

          .customer-cell strong {
            color: #172033;
            font-weight: 700;
          }

          .customer-cell small {
            color: #647084;
          }

          .code-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 24px;
            padding: 5px 10px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 800;
          }

          .amount-paid {
            color: #059669;
            font-weight: 700;
          }

          .amount-remain {
            color: #dc2626;
            font-weight: 700;
          }

          .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 62px;
            min-height: 24px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
          }

          .status-success {
            background: #d1fae5;
            color: #047857;
          }

          .status-primary {
            background: #dbeafe;
            color: #1d4ed8;
          }

          .status-warning {
            background: #fef3c7;
            color: #92400e;
          }

          .status-danger {
            background: #fee2e2;
            color: #b91c1c;
          }

          .status-secondary {
            background: #e2e8f0;
            color: #334155;
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

          @media (max-width: 992px) {
            .purchase-summary {
              grid-template-columns: repeat(2, minmax(0, 1fr));
            }
          }

          @media (max-width: 576px) {
            .purchase-summary {
              grid-template-columns: 1fr;
            }
          }

          .user-popover {
            min-width: 180px;
            padding: 2px 0;
          }

          .user-popover-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-bottom: 10px;
          }

          .user-popover-row span {
            color: #647084;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
          }

          .user-popover-row strong {
            color: #172033;
            font-size: 13px;
            font-weight: 700;
          }

          .user-popover-logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
            padding-top: 10px;
            border-top: 1px solid #e4e8ef;
            color: #dc2626;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
          }

          .user-popover-logout:hover {
            color: #b91c1c;
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

    $(document).ready(function() {
      $('#pembelianListTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
        }
      });

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
    });
  </script>
</body>
</html>
