<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
  .detail-toolbar {
    margin-bottom: 16px;
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

  .lunas-banner {
    margin-bottom: 18px;
  }

  .detail-section {
    margin-bottom: 18px;
    padding: 18px;
    border: 1px solid #e4e8ef;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
  }

  .detail-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
  }

  .detail-section-header .material-icons {
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

  .detail-section-header h5 {
    margin: 0;
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

  .status-success { background: #d1fae5; color: #047857; }
  .status-primary { background: #dbeafe; color: #1d4ed8; }
  .status-warning { background: #fef3c7; color: #92400e; }
  .status-danger { background: #fee2e2; color: #b91c1c; }
  .status-secondary { background: #e2e8f0; color: #334155; }

  .amount-paid { color: #059669; font-weight: 700; }
  .amount-remain { color: #dc2626; font-weight: 700; }

  .media-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }

  .media-item {
    border: 1px solid #e4e8ef;
    border-radius: 8px;
    padding: 12px;
    background: #f8fafc;
  }

  .media-item .media-label {
    font-size: 12px;
    font-weight: 700;
    color: #647084;
    margin-bottom: 8px;
  }

  .media-item .media-content {
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
  }

  .media-item img {
    max-width: 100%;
    max-height: 200px;
    object-fit: contain;
  }

  .media-item .document-preview {
    text-align: center;
    padding: 20px;
  }

  .media-item .document-preview .material-icons {
    font-size: 48px;
    color: #647084;
  }

  .description-box {
    background: #f8fafc;
    border: 1px solid #e4e8ef;
    border-radius: 8px;
    padding: 16px;
    margin-top: 12px;
  }

  .berkas-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .berkas-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    border: 1px solid #e4e8ef;
    border-radius: 8px;
    background: #f8fafc;
  }

  .berkas-row strong {
    display: block;
    color: #172033;
    font-size: 13px;
    font-weight: 800;
  }

  .berkas-row small {
    color: #647084;
  }

  .berkas-meta {
    display: inline-flex;
    margin-left: 6px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
  }

  .berkas-meta.wajib { background: #fee2e2; color: #b91c1c; }
  .berkas-meta.opsional { background: #e2e8f0; color: #334155; }
  }

  .description-box .description-label {
    font-size: 12px;
    font-weight: 700;
    color: #647084;
    margin-bottom: 8px;
  }

  .description-box .description-content {
    font-size: 14px;
    color: #172033;
    line-height: 1.6;
  }

  .empty-state {
    text-align: center;
    padding: 36px 16px;
    color: #647084;
  }

  .empty-state .material-icons {
    font-size: 42px;
    margin-bottom: 8px;
    color: #94a3b8;
  }

  .empty-state p {
    margin: 0;
    font-weight: 600;
  }

  #riwayatPembayaranTable thead th {
    background-color: #eef6f8;
    color: #203246;
    text-align: center;
  }

  #riwayatPembayaranTable td {
    vertical-align: middle;
  }

  @media (max-width: 768px) {
    .payment-summary,
    .payment-detail-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
  /**
   * @var array<string, mixed> $pembelian
   * @var array<int, array<string, mixed>> $pembayaran
   * @var float|int $total_dibayar
   * @var float|int $sisa_tagihan
   * @var string|null $userRole
   */
  if (!isset($pembelian) || !is_array($pembelian)) {
    $pembelian = [];
  }
  if (!isset($pembayaran) || !is_array($pembayaran)) {
    $pembayaran = [];
  }
  $total_dibayar = $total_dibayar ?? 0;
  $sisa_tagihan = $sisa_tagihan ?? 0;
  $userRole = $userRole ?? '';
  $berkasCustomer = is_array($berkasCustomer ?? null) ? $berkasCustomer : [];
  $infoBerkas = is_array($infoBerkas ?? null) ? $infoBerkas : [];
  $backUrl = '/detail-pembelian-list';

  $statusPembelian = strtolower((string) ($pembelian['status_pembelian'] ?? ''));
  $statusPembelianClass = 'status-secondary';
  if ($statusPembelian === 'lunas') $statusPembelianClass = 'status-success';
  elseif ($statusPembelian === 'cicil') $statusPembelianClass = 'status-primary';
  elseif ($statusPembelian === 'dp') $statusPembelianClass = 'status-warning';
  elseif ($statusPembelian === 'batal') $statusPembelianClass = 'status-danger';

  $statusDokumen = strtolower((string) ($pembelian['status_dokumen'] ?? ''));
  $statusDokumenClass = 'status-secondary';
  if ($statusDokumen === 'lengkap') $statusDokumenClass = 'status-success';
  elseif ($statusDokumen === 'verifikasi') $statusDokumenClass = 'status-warning';
  elseif ($statusDokumen === 'pending') $statusDokumenClass = 'status-danger';

  $jenisLabels = [
    'booking_fee' => 'Booking Fee',
    'dp' => 'DP',
    'cicilan' => 'Cicilan',
    'pelunasan' => 'Pelunasan',
  ];
?>

<div class="detail-toolbar">
  <a href="<?= esc($backUrl) ?>" class="btn btn-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Kembali
  </a>
</div>

<div class="detail-section">
  <div class="detail-section-header">
    <span class="material-icons">person</span>
    <h5>Informasi Pembeli</h5>
  </div>
  <div class="payment-detail-grid">
    <div class="payment-detail-item">
      <div class="payment-detail-label">Nama Customer</div>
      <div class="payment-detail-value"><?= esc($pembelian['nama_customer'] ?? '-') ?></div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Email</div>
      <div class="payment-detail-value"><?= esc($pembelian['email_customer'] ?? '-') ?></div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Telepon</div>
      <div class="payment-detail-value"><?= esc($pembelian['telepon_customer'] ?? '-') ?></div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Tanggal Pembelian</div>
      <div class="payment-detail-value">
        <?= !empty($pembelian['tanggal_pembelian']) ? date('d-m-Y', strtotime($pembelian['tanggal_pembelian'])) : '-' ?>
      </div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Metode Pembayaran</div>
      <div class="payment-detail-value"><?= esc(ucfirst((string) ($pembelian['metode_pembayaran'] ?? '-'))) ?></div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Status Pembelian</div>
      <div class="payment-detail-value">
        <span class="status-pill <?= $statusPembelianClass ?>"><?= esc(ucfirst((string) ($pembelian['status_pembelian'] ?? '-'))) ?></span>
      </div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Status Dokumen</div>
      <div class="payment-detail-value">
        <span class="status-pill <?= $statusDokumenClass ?>"><?= esc(ucfirst((string) ($pembelian['status_dokumen'] ?? '-'))) ?></span>
      </div>
    </div>
    <div class="payment-detail-item full">
      <div class="payment-detail-label">Alamat</div>
      <div class="payment-detail-value"><?= esc($pembelian['alamat_customer'] ?? '-') ?></div>
    </div>
    <?php if (!empty($pembelian['request_khusus'])): ?>
      <div class="payment-detail-item full">
        <div class="payment-detail-label">Request Khusus</div>
        <div class="payment-detail-value"><?= esc($pembelian['request_khusus']) ?></div>
      </div>
    <?php endif; ?>
    <?php if (!empty($pembelian['catatan_marketing'])): ?>
      <div class="payment-detail-item full">
        <div class="payment-detail-label">Catatan Marketing</div>
        <div class="payment-detail-value"><?= esc($pembelian['catatan_marketing']) ?></div>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="detail-section">
  <div class="detail-section-header">
    <span class="material-icons">home_work</span>
    <h5>Informasi Rumah</h5>
  </div>
  <div class="payment-detail-grid">
    <div class="payment-detail-item">
      <div class="payment-detail-label">Kode Rumah</div>
      <div class="payment-detail-value"><span class="code-pill"><?= esc($pembelian['kode_rumah'] ?? '-') ?></span></div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Tipe</div>
      <div class="payment-detail-value"><?= esc($pembelian['tipe_rumah'] ?? '-') ?></div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Lokasi</div>
      <div class="payment-detail-value"><?= esc($pembelian['lokasi_rumah'] ?? '-') ?></div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Luas Tanah</div>
      <div class="payment-detail-value"><?= esc($pembelian['luas_tanah'] ?? '-') ?> m²</div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Luas Bangunan</div>
      <div class="payment-detail-value"><?= esc($pembelian['luas_bangunan'] ?? '-') ?> m²</div>
    </div>
    <div class="payment-detail-item">
      <div class="payment-detail-label">Harga Beli</div>
      <div class="payment-detail-value amount-paid">Rp <?= number_format((float) ($pembelian['harga_beli'] ?? 0), 0, ',', '.') ?></div>
    </div>
    <div class="payment-detail-item full">
      <div class="payment-detail-label">Deskripsi Rumah</div>
      <div class="payment-detail-value">
        <?= !empty($pembelian['deskripsi_rumah']) ? nl2br(esc($pembelian['deskripsi_rumah'])) : '-' ?>
      </div>
    </div>
  </div>

  <!-- Media Section: Gambar dan Dokumen -->
  <?php if (!empty($pembelian['gambar_rumah']) || !empty($pembelian['dokumen_rumah'])): ?>
  <div class="media-section" style="margin-top: 18px;">
    <?php if (!empty($pembelian['gambar_rumah'])): ?>
    <div class="media-item">
      <div class="media-label">Gambar Properti</div>
      <div class="media-content">
        <img src="/<?= esc($pembelian['gambar_rumah']) ?>" alt="Gambar <?= esc($pembelian['kode_rumah']) ?>">
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($pembelian['dokumen_rumah'])): ?>
    <div class="media-item">
      <div class="media-label">Dokumen Properti</div>
      <div class="media-content">
        <div class="document-preview">
          <span class="material-icons">description</span>
          <p style="margin: 8px 0 0 0; font-size: 12px; color: #647084;"><?= basename($pembelian['dokumen_rumah']) ?></p>
          <a href="/<?= esc($pembelian['dokumen_rumah']) ?>" target="_blank" class="btn btn-sm btn-primary" style="margin-top: 8px;">
            <i class="fas fa-download"></i> Download
          </a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php if (($pembelian['sumber'] ?? '') === 'customer'): ?>
<div class="detail-section">
  <div class="detail-section-header">
    <span class="material-icons">folder</span>
    <h5>Berkas Booking Customer</h5>
  </div>
  <p style="margin:0 0 12px;color:#647084;font-size:13px;">
    Status berkas: <strong><?= esc($infoBerkas['status'] ?? ($pembelian['status_berkas'] ?? 'pending')) ?></strong>
    · Verifikasi: <strong><?= esc($pembelian['status_verifikasi'] ?? 'pending') ?></strong>
  </p>
  <div class="berkas-list">
    <?php foreach ($berkasCustomer as $item): ?>
      <div class="berkas-row">
        <div>
          <strong>
            <?= esc($item['label']) ?>
            <span class="berkas-meta <?= !empty($item['wajib']) ? 'wajib' : 'opsional' ?>">
              <?= !empty($item['wajib']) ? 'Wajib' : 'Opsional' ?>
            </span>
          </strong>
          <small><?= esc($item['keterangan']) ?></small>
        </div>
        <div>
          <?php
            $berkasStatus = strtolower((string) ($item['status'] ?? 'pending'));
            $berkasStatusClass = 'status-warning';
            if ($berkasStatus === 'disetujui') $berkasStatusClass = 'status-success';
            elseif ($berkasStatus === 'ditolak') $berkasStatusClass = 'status-danger';
          ?>
          <?php if (!empty($item['file'])): ?>
            <a href="/<?= esc($item['file']) ?>" target="_blank" class="btn btn-sm btn-primary">
              <i class="fas fa-eye"></i> Lihat
            </a>
            <?php if (empty($isCustomer) && $berkasStatus === 'pending'): ?>
              <button type="button" class="btn btn-sm btn-success" title="Setujui" onclick="verifikasiBerkas(<?= (int) ($pembelian['id'] ?? 0) ?>, '<?= esc($item['key']) ?>', 'setujui')">
                <i class="fas fa-check"></i>
              </button>
              <button type="button" class="btn btn-sm btn-danger" title="Tolak" onclick="verifikasiBerkas(<?= (int) ($pembelian['id'] ?? 0) ?>, '<?= esc($item['key']) ?>', 'tolak')">
                <i class="fas fa-times"></i>
              </button>
            <?php endif; ?>
            <span class="status-pill <?= $berkasStatusClass ?>"><?= esc(ucfirst($berkasStatus)) ?></span>
          <?php else: ?>
            <span class="status-pill status-danger">Belum diunggah</span>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="detail-section">
  <div class="detail-section-header">
    <span class="material-icons">history</span>
    <h5>Riwayat Pembayaran</h5>
  </div>

  <?php if (empty($pembayaran)): ?>
    <div class="empty-state">
      <span class="material-icons">receipt_long</span>
      <p>Belum ada riwayat pembayaran</p>
    </div>
  <?php else: ?>
    <table id="riwayatPembayaranTable" class="display table table-striped table-bordered w-100">
      <thead>
        <tr>
          <th>Tanggal Bayar</th>
          <th>Jenis</th>
          <th>Jumlah Bayar</th>
          <th>Metode</th>
          <th>Status</th>
          <th>Bukti</th>
          <th>Aksi</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pembayaran as $bayar): ?>
          <?php
            $statusBayar = strtolower((string) ($bayar['status_pengajuan'] ?? ''));
            $statusBayarClass = 'status-secondary';
            if ($statusBayar === 'disetujui') $statusBayarClass = 'status-success';
            elseif ($statusBayar === 'pending') $statusBayarClass = 'status-warning';
            elseif ($statusBayar === 'ditolak') $statusBayarClass = 'status-danger';
            $jenis = (string) ($bayar['jenis_pembayaran'] ?? '');
          ?>
          <tr>
            <td>
              <?= esc($bayar['info_tanggal_display'] ?? '-') ?>
              <?php if (!empty($bayar['is_jatuh_tempo'])): ?>
                <br><small class="text-muted">Jatuh tempo</small>
              <?php endif; ?>
            </td>
            <td><?= esc($bayar['info_jenis'] ?? ($jenisLabels[$jenis] ?? ucfirst($jenis))) ?></td>
            <td class="amount-paid">Rp <?= number_format((float) ($bayar['jumlah_bayar'] ?? 0), 0, ',', '.') ?></td>
            <td><?= esc(ucfirst((string) ($bayar['metode_bayar'] ?? '-'))) ?></td>
            <td><span class="status-pill <?= $statusBayarClass ?>"><?= esc(ucfirst((string) ($bayar['status_pengajuan'] ?? '-'))) ?></span></td>
            <td>
              <?php if (!empty($bayar['bukti_bayar'])): ?>
                <a href="/<?= esc($bayar['bukti_bayar']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat</a>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (empty($isCustomer) && $statusBayar === 'pending'): ?>
                <button type="button" class="btn btn-sm btn-success" title="Setujui" onclick="verifikasiCicilan(<?= (int) ($bayar['id'] ?? 0) ?>, 'approve')">
                  <i class="fas fa-check"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger" title="Tolak" onclick="verifikasiCicilan(<?= (int) ($bayar['id'] ?? 0) ?>, 'reject')">
                  <i class="fas fa-times"></i>
                </button>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td><?= esc($bayar['keterangan'] ?: '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function verifikasiBerkas(id, jenis, aksi) {
    $.post(`/pembelian-rumah/${id}/berkas/${jenis}/verifikasi`, { aksi }, function (res) {
      if (res.status === 'success') {
        window.location.reload();
        return;
      }
      alert(res.message || 'Gagal memverifikasi berkas');
    }).fail(function (xhr) {
      alert((xhr.responseJSON && xhr.responseJSON.message) || 'Gagal memverifikasi berkas');
    });
  }

  function verifikasiCicilan(id, aksi) {
    const url = aksi === 'reject' ? `/pembayaran-rumah/reject/${id}` : `/pembayaran-rumah/approve/${id}`;
    $.post(url, function (res) {
      if (res.status === 'success') {
        window.location.reload();
        return;
      }
      alert(res.message || 'Gagal memverifikasi bukti cicilan');
    }).fail(function (xhr) {
      alert((xhr.responseJSON && xhr.responseJSON.message) || 'Gagal memverifikasi bukti cicilan');
    });
  }
</script>
<?= $this->endSection() ?>
