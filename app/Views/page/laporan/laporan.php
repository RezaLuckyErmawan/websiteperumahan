<?php
$pageTitle = 'Laporan Keuangan';
$useDataTables = true;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
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
<?= $this->endSection() ?>

<?= $this->section('content') ?>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
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
<?= $this->endSection() ?>
