<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan Rumah</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #222; margin: 24px; }
    .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 16px; }
    .header h2 { margin: 0; text-transform: uppercase; font-size: 16px; }
    .header p { margin: 4px 0 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #777; padding: 6px; vertical-align: top; }
    th { background: #eee; text-align: center; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .footer { margin-top: 18px; font-size: 10px; }
  </style>
</head>
<body>
  <?php
    /** @var list<array<string, mixed>> $penjualan */
    /** @var string $tanggalCetak */
    $penjualan = is_array($penjualan ?? null) ? $penjualan : [];
    if (!isset($tanggalCetak) || !is_string($tanggalCetak) || $tanggalCetak === '') {
      $tanggalCetak = date('d-m-Y H:i');
    }
  ?>
  <div class="header">
    <h2>Laporan Penjualan Rumah</h2>
    <p>Sistem Manajemen Informasi Perumahan</p>
    <p>Dicetak: <?= esc($tanggalCetak) ?></p>
  </div>

  <table>
    <thead>
      <tr>
        <th width="4%">No</th>
        <th>Customer</th>
        <th>Telepon</th>
        <th>Kode Rumah</th>
        <th>Tipe</th>
        <th>Lokasi</th>
        <th>Tanggal</th>
        <th>Harga Beli</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($penjualan)): ?>
        <tr><td colspan="9" class="text-center">Tidak ada data penjualan.</td></tr>
      <?php else: ?>
        <?php $no = 1; $total = 0; foreach ($penjualan as $row): $total += (float) $row['harga_beli']; ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= esc($row['nama_customer']) ?></td>
            <td><?= esc($row['telepon_customer']) ?></td>
            <td class="text-center"><?= esc($row['kode_rumah']) ?></td>
            <td class="text-center"><?= esc($row['tipe_rumah']) ?></td>
            <td><?= esc($row['lokasi_rumah']) ?></td>
            <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal_pembelian'])) ?></td>
            <td class="text-right">Rp <?= number_format((float) $row['harga_beli'], 0, ',', '.') ?></td>
            <td class="text-center"><?= esc(ucfirst((string) $row['status_pembelian'])) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="7" class="text-right"><strong>Total Omzet</strong></td>
          <td class="text-right"><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
          <td></td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="footer">
    Total data: <?= count($penjualan) ?> penjualan
  </div>
</body>
</html>
