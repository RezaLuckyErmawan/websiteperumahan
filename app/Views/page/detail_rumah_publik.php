<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= esc($rumah['kode_rumah'] ?? 'Detail Rumah') ?> · GreenHome.id</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      background: #ffffff;
      color: #1A2E44;
      font-family: 'Inter', Arial, sans-serif;
    }

    header {
      min-height: 68px;
      padding: 0 32px;
      color: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      inset: 0 0 auto 0;
      z-index: 10;
      background: rgba(26, 46, 68, 0.78);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.14);
    }

    header h2 { margin: 0; font-size: 20px; font-weight: 800; }
    header h2 a { color: inherit; text-decoration: none; }

    nav { display: flex; align-items: center; gap: 18px; }

    nav a {
      color: rgba(255, 255, 255, 0.88);
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
    }

    nav a:hover { color: #ffffff; }

    main {
      padding: 108px 32px 60px;
      background: #f6f8fb;
      min-height: calc(100vh - 88px);
    }

    .detail-wrap {
      width: min(960px, 100%);
      margin: 0 auto;
    }

    .back-link {
      display: inline-flex;
      margin-bottom: 16px;
      color: #2B6BE5;
      text-decoration: none;
      font-size: 14px;
      font-weight: 700;
    }

    .detail-card {
      overflow: hidden;
      background: #ffffff;
      border: 1px solid #e4e8ef;
      border-radius: 12px;
      box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .detail-hero {
      width: 100%;
      height: 360px;
      background: #eef2f7;
    }

    .detail-hero img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .detail-body { padding: 24px; }

    .detail-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 10px;
    }

    .detail-body h1 {
      margin: 0 0 6px;
      font-size: 28px;
      font-weight: 800;
    }

    .detail-price {
      margin: 0 0 18px;
      color: #059669;
      font-size: 22px;
      font-weight: 800;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 5px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      white-space: nowrap;
    }

    .status-success { background: #d1fae5; color: #047857; }
    .status-primary { background: #dbeafe; color: #1d4ed8; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-secondary { background: #e2e8f0; color: #334155; }

    .detail-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 18px;
    }

    .detail-item {
      min-height: 66px;
      padding: 13px 14px;
      border: 1px solid #e4e8ef;
      border-radius: 8px;
      background: #ffffff;
    }

    .detail-item.full { grid-column: 1 / -1; }

    .detail-label {
      margin-bottom: 5px;
      color: #647084;
      font-size: 12px;
      font-weight: 800;
    }

    .detail-value {
      color: #172033;
      font-size: 14px;
      font-weight: 700;
      overflow-wrap: anywhere;
    }

    .detail-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      padding: 0 18px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 700;
    }

    .btn-primary { background: #2B6BE5; color: #ffffff; }
    .btn-secondary { background: #e2e8f0; color: #1A2E44; }

    footer {
      background: #1A2E44;
      color: rgba(255, 255, 255, 0.86);
      text-align: center;
      padding: 24px 16px;
    }

    footer p { margin: 0; }

    footer a {
      color: #ffffff;
      margin: 0 8px;
      text-decoration: none;
      font-weight: 700;
    }

    @media (max-width: 760px) {
      header { padding: 0 18px; }
      nav { display: none; }
      main { padding: 96px 18px 48px; }
      .detail-hero { height: 240px; }
      .detail-grid { grid-template-columns: 1fr; }
      .detail-actions { display: block; }
      .btn { width: 100%; margin-bottom: 8px; }
    }
  </style>
</head>
<body>
  <?php
    $rumah = is_array($rumah ?? null) ? $rumah : [];
    $gambar = trim((string) ($rumah['gambar'] ?? ''));
    $gambarUrl = $gambar !== ''
      ? '/' . ltrim($gambar, '/')
      : 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1600&q=85';
    $dokumen = trim((string) ($rumah['dokumen'] ?? ''));
    $status = strtolower((string) ($rumah['status'] ?? ''));
    $statusClass = 'status-secondary';
    if (in_array($status, ['dijual', 'tersedia'], true)) $statusClass = 'status-primary';
    elseif (in_array($status, ['terjual', 'lunas'], true)) $statusClass = 'status-success';
    elseif (in_array($status, ['proses pembangunan', 'booking', 'booked'], true)) $statusClass = 'status-warning';
    $loggedIn = (bool) session()->get('isLoggedIn');
    $role = (string) session()->get('role');
    $pesanUrl = ($loggedIn && $role === 'customer')
      ? '/perumahan/data-rumah/' . (int) ($rumah['id'] ?? 0)
      : '/login';
    $pesanLabel = ($loggedIn && $role === 'customer') ? 'Lanjut ke pemesanan' : 'Login untuk memesan';
  ?>
  <header>
    <h2><a href="/">GreenHome.id</a></h2>
    <nav>
      <a href="/#beranda">Beranda</a>
      <a href="/#tentang">Tentang</a>
      <a href="/#daftar-rumah">Daftar Rumah</a>
      <a href="/login">Login</a>
    </nav>
  </header>

  <main>
    <div class="detail-wrap">
      <a class="back-link" href="/#daftar-rumah">&larr; Kembali ke daftar rumah</a>
      <article class="detail-card">
        <div class="detail-hero">
          <img src="<?= esc($gambarUrl) ?>" alt="<?= esc($rumah['kode_rumah'] ?? 'Rumah') ?>">
        </div>
        <div class="detail-body">
          <div class="detail-top">
            <div>
              <h1><?= esc($rumah['kode_rumah'] ?? 'Rumah') ?> · Tipe <?= esc($rumah['tipe'] ?? '-') ?></h1>
              <p class="detail-price">Rp <?= number_format((float) ($rumah['harga'] ?? 0), 0, ',', '.') ?></p>
            </div>
            <span class="status-pill <?= $statusClass ?>"><?= esc($rumah['status'] ?? '-') ?></span>
          </div>

          <div class="detail-grid">
            <div class="detail-item">
              <div class="detail-label">Kode Rumah</div>
              <div class="detail-value"><?= esc($rumah['kode_rumah'] ?? '-') ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Tipe</div>
              <div class="detail-value"><?= esc($rumah['tipe'] ?? '-') ?></div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Luas Tanah</div>
              <div class="detail-value"><?= esc($rumah['luas_tanah'] ?? '-') ?> m²</div>
            </div>
            <div class="detail-item">
              <div class="detail-label">Luas Bangunan</div>
              <div class="detail-value"><?= esc($rumah['luas_bangunan'] ?? '-') ?> m²</div>
            </div>
            <div class="detail-item full">
              <div class="detail-label">Lokasi</div>
              <div class="detail-value"><?= esc($rumah['lokasi'] ?? '-') ?></div>
            </div>
            <div class="detail-item full">
              <div class="detail-label">Deskripsi</div>
              <div class="detail-value">
                <?= !empty($rumah['deskripsi']) ? nl2br(esc($rumah['deskripsi'])) : '-' ?>
              </div>
            </div>
            <?php if ($dokumen !== ''): ?>
              <div class="detail-item full">
                <div class="detail-label">Dokumen</div>
                <div class="detail-value">
                  <a href="/<?= esc($dokumen) ?>" target="_blank">Download dokumen</a>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="detail-actions">
            <a class="btn btn-primary" href="<?= esc($pesanUrl) ?>"><?= esc($pesanLabel) ?></a>
            <a class="btn btn-secondary" href="/#daftar-rumah">Lihat rumah lain</a>
          </div>
        </div>
      </article>
    </div>
  </main>

  <footer>
    <p>&copy; <?= date('Y') ?> GreenHome.id. Semua hak dilindungi.</p>
    <div style="margin-top: 12px;">
      <a href="https://instagram.com/greenhomeid" target="_blank">Instagram</a>
      <a href="https://facebook.com/greenhomeid" target="_blank">Facebook</a>
      <a href="mailto:info@greenhome.id">Email</a>
      <span>0812-3456-7890</span>
    </div>
  </footer>
</body>
</html>
