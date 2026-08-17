<?php
$rumah = is_array($rumah ?? null) ? $rumah : [];

$summary = [
    'total' => count($rumah),
    'tersedia' => 0,
    'booked' => 0,
    'terjual' => 0,
];

foreach ($rumah as $item) {
    $status = strtolower((string) ($item['status'] ?? ''));
    if (in_array($status, ['dijual', 'tersedia'], true)) {
        $summary['tersedia']++;
    }
    if (in_array($status, ['booked', 'booking'], true)) {
        $summary['booked']++;
    }
    if (in_array($status, ['terjual', 'lunas'], true)) {
        $summary['terjual']++;
    }
}

function landing_status_class(string $status): string
{
    return match (strtolower($status)) {
        'dijual', 'tersedia' => 'status-primary',
        'terjual', 'lunas' => 'status-success',
        'booked', 'booking', 'proses pembangunan' => 'status-warning',
        default => 'status-secondary',
    };
}

function landing_status_label(string $status): string
{
    return match (strtolower($status)) {
        'dijual', 'tersedia' => 'Stok tersedia',
        'terjual', 'lunas' => 'Terjual',
        'booked', 'booking' => 'Booked',
        'proses pembangunan' => 'Proses pembangunan',
        default => 'Info',
    };
}

function landing_image_url(?string $path): ?string
{
    $path = trim((string) $path);
    if ($path === '') {
        return null;
    }

    return base_url(ltrim($path, '/'));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GreenHome.id</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    :root {
      --bg: #f3f7fb;
      --surface: rgba(255, 255, 255, 0.92);
      --surface-strong: #ffffff;
      --text: #132238;
      --muted: #66748a;
      --border: #dce5ef;
      --primary: #2563eb;
      --primary-2: #0f766e;
      --success: #059669;
      --shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
      --shadow-soft: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      background:
        radial-gradient(circle at top left, rgba(37, 99, 235, 0.08), transparent 36%),
        radial-gradient(circle at top right, rgba(15, 118, 110, 0.08), transparent 30%),
        var(--bg);
      color: var(--text);
      font-family: 'Inter', sans-serif;
    }

    a { color: inherit; }

    .site-header {
      position: sticky;
      top: 0;
      z-index: 20;
      backdrop-filter: blur(16px);
      background: rgba(255, 255, 255, 0.78);
      border-bottom: 1px solid rgba(220, 229, 239, 0.9);
    }

    .site-header-inner,
    .hero-inner,
    .content-inner,
    .footer-inner {
      width: min(1180px, calc(100% - 32px));
      margin: 0 auto;
    }

    .site-header-inner {
      min-height: 74px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .brand {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      font-weight: 900;
      letter-spacing: -0.02em;
    }

    .brand-mark {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary), var(--primary-2));
      color: #fff;
      box-shadow: var(--shadow-soft);
    }

    .brand-name {
      font-size: 18px;
      color: var(--text);
    }

    .header-nav {
      display: flex;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .header-nav a {
      text-decoration: none;
      color: var(--muted);
      font-size: 14px;
      font-weight: 700;
    }

    .header-nav a:hover { color: var(--primary); }

    .btn-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 42px;
      padding: 0 16px;
      border-radius: 999px;
      border: 1px solid rgba(37, 99, 235, 0.16);
      background: rgba(255, 255, 255, 0.92);
      text-decoration: none;
      color: var(--primary);
      font-weight: 800;
      box-shadow: 0 8px 20px rgba(37, 99, 235, 0.08);
    }

    .hero {
      padding: 30px 0 18px;
    }

    .hero-panel {
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.35);
      border-radius: 28px;
      background:
        linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(15, 118, 110, 0.55)),
        url('https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
      color: #fff;
      box-shadow: var(--shadow);
    }

    .hero-panel-inner {
      padding: 44px;
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: 26px;
      align-items: end;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.14);
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 14px;
    }

    .hero h1 {
      margin: 0 0 12px;
      font-size: clamp(36px, 5vw, 60px);
      line-height: 1;
      letter-spacing: -0.04em;
      max-width: 12ch;
    }

    .hero p {
      margin: 0;
      max-width: 640px;
      font-size: 17px;
      line-height: 1.7;
      color: rgba(255, 255, 255, 0.9);
    }

    .search-panel {
      padding: 22px;
      border-radius: 22px;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.16);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    .search-label {
      margin-bottom: 10px;
      font-size: 13px;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.88);
    }

    .search-box {
      display: flex;
      gap: 10px;
      padding: 8px;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.94);
      box-shadow: var(--shadow-soft);
    }

    .search-box input {
      flex: 1;
      min-width: 0;
      min-height: 48px;
      border: 0;
      border-radius: 14px;
      padding: 0 14px;
      font: inherit;
      outline: none;
      color: var(--text);
    }

    .search-box button {
      min-height: 48px;
      padding: 0 18px;
      border: 0;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--primary), #1e40af);
      color: #fff;
      font-weight: 800;
      cursor: pointer;
    }

    .stats {
      margin-top: 18px;
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
    }

    .stat-card {
      padding: 18px 20px;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid var(--border);
      box-shadow: var(--shadow-soft);
    }

    .stat-card .label {
      color: var(--muted);
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .stat-card .value {
      margin-top: 8px;
      font-size: 24px;
      font-weight: 900;
      color: var(--text);
    }

    main {
      padding: 20px 0 54px;
    }

    .section-head {
      display: flex;
      align-items: end;
      justify-content: space-between;
      gap: 16px;
      margin: 28px 0 16px;
    }

    .section-head h2 {
      margin: 0;
      font-size: 28px;
      font-weight: 900;
      letter-spacing: -0.03em;
    }

    .section-head p {
      margin: 6px 0 0;
      color: var(--muted);
    }

    .results-count {
      color: var(--muted);
      font-size: 13px;
      font-weight: 700;
    }

    .catalog-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
      gap: 18px;
    }

    .property-card {
      overflow: hidden;
      border-radius: 24px;
      background: var(--surface-strong);
      border: 1px solid var(--border);
      box-shadow: var(--shadow-soft);
      transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .property-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow);
    }

    .property-media {
      position: relative;
      height: 220px;
      background: linear-gradient(135deg, #dbeafe, #d1fae5);
      overflow: hidden;
    }

    .property-media img {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: cover;
    }

    .property-media .no-image {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(19, 34, 56, 0.45);
    }

    .property-media .no-image .material-icons {
      font-size: 54px;
    }

    .badge-row {
      position: absolute;
      inset: 14px 14px auto 14px;
      display: flex;
      justify-content: space-between;
      gap: 10px;
      pointer-events: none;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-height: 30px;
      padding: 0 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      backdrop-filter: blur(14px);
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
    }

    .pill.code {
      background: rgba(255, 255, 255, 0.88);
      color: #0f172a;
    }

    .pill.status-success { background: rgba(16, 185, 129, 0.92); color: #fff; }
    .pill.status-primary { background: rgba(37, 99, 235, 0.92); color: #fff; }
    .pill.status-warning { background: rgba(245, 158, 11, 0.92); color: #fff; }
    .pill.status-secondary { background: rgba(100, 116, 139, 0.92); color: #fff; }

    .property-body {
      padding: 18px;
    }

    .property-title {
      margin: 0 0 6px;
      font-size: 18px;
      font-weight: 900;
      letter-spacing: -0.02em;
    }

    .property-location {
      margin: 0 0 12px;
      color: var(--muted);
      font-size: 14px;
      line-height: 1.5;
      min-height: 42px;
    }

    .price-row {
      display: flex;
      justify-content: space-between;
      align-items: end;
      gap: 10px;
      margin-bottom: 14px;
    }

    .price {
      color: var(--success);
      font-size: 20px;
      font-weight: 900;
      letter-spacing: -0.02em;
    }

    .card-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      min-height: 46px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--primary), #1d4ed8);
      color: #fff;
      text-decoration: none;
      font-weight: 800;
    }

    .spec-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
      margin-bottom: 14px;
    }

    .spec {
      padding: 10px 12px;
      border-radius: 14px;
      background: #f8fbff;
      border: 1px solid #e7eef7;
    }

    .spec-label {
      color: var(--muted);
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 4px;
    }

    .spec-value {
      color: var(--text);
      font-size: 13px;
      font-weight: 800;
    }

    .description {
      margin-bottom: 14px;
      color: var(--muted);
      font-size: 13px;
      line-height: 1.6;
      min-height: 62px;
    }

    .empty-state {
      padding: 28px;
      border: 1px dashed #c8d4e2;
      border-radius: 22px;
      background: rgba(255, 255, 255, 0.75);
      text-align: center;
      color: var(--muted);
      box-shadow: var(--shadow-soft);
    }

    .site-footer {
      padding: 24px 0 30px;
      color: var(--muted);
      text-align: center;
      font-size: 14px;
    }

    .site-footer strong {
      color: var(--text);
    }

    @media (max-width: 980px) {
      .hero-panel-inner {
        grid-template-columns: 1fr;
      }

      .stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 640px) {
      .site-header-inner,
      .header-nav {
        gap: 10px;
      }

      .brand-name {
        font-size: 16px;
      }

      .hero-panel-inner {
        padding: 24px;
      }

      .stats {
        grid-template-columns: 1fr;
      }

      .section-head {
        flex-direction: column;
        align-items: start;
      }

      .search-box {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="/">
        <span class="brand-mark"><span class="material-icons">home</span></span>
        <span class="brand-name">GreenHome.id</span>
      </a>
      <nav class="header-nav">
        <a href="/">Beranda</a>
        <a href="/perumahan/data-rumah">Katalog Rumah</a>
        <a href="/login" class="btn-link">Login</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="hero-inner">
      <div class="hero-panel">
        <div class="hero-panel-inner">
          <div>
            <div class="hero-badge"><span class="material-icons" style="font-size:18px;">verified</span> Hunian modern, lokasi strategis, siap dilihat</div>
            <h1>Temukan rumah terbaik untuk keluarga Anda.</h1>
            <p>
              Jelajahi daftar rumah dari sistem perumahan kami dalam tampilan katalog yang rapi, modern, dan mudah dibandingkan.
              Setiap kartu menampilkan detail penting seperti kode rumah, lokasi, tipe, luas, harga, status, dan deskripsi.
            </p>
          </div>

          <div class="search-panel">
            <div class="search-label">Cari rumah berdasarkan kode, lokasi, tipe, atau status</div>
            <div class="search-box">
              <input type="text" id="houseSearch" placeholder="Contoh: kode rumah, lokasi, tipe..." autocomplete="off">
              <button type="button" id="clearSearchBtn">Reset</button>
            </div>
          </div>
        </div>
      </div>

      <div class="stats">
        <div class="stat-card">
          <div class="label">Total Rumah</div>
          <div class="value"><?= esc($summary['total']) ?></div>
        </div>
        <div class="stat-card">
          <div class="label">Tersedia</div>
          <div class="value"><?= esc($summary['tersedia']) ?></div>
        </div>
        <div class="stat-card">
          <div class="label">Booked</div>
          <div class="value"><?= esc($summary['booked']) ?></div>
        </div>
        <div class="stat-card">
          <div class="label">Terjual</div>
          <div class="value"><?= esc($summary['terjual']) ?></div>
        </div>
      </div>
    </div>
  </section>

  <main>
    <div class="content-inner">
      <div class="section-head">
        <div>
          <h2>Daftar Rumah</h2>
          <p>Card layout bergaya e-commerce untuk melihat detail perumahan dengan cepat.</p>
        </div>
        <div class="results-count" id="resultsCount"><?= count($rumah) ?> hasil ditemukan</div>
      </div>

      <?php if (empty($rumah)): ?>
        <div class="empty-state">
          Belum ada data rumah yang tersedia saat ini.
        </div>
      <?php else: ?>
        <div class="catalog-grid" id="catalogGrid">
          <?php foreach ($rumah as $item): ?>
            <?php
              $status = (string) ($item['status'] ?? '-');
              $statusClass = landing_status_class($status);
              $statusLabel = landing_status_label($status);
              $imageUrl = landing_image_url($item['gambar'] ?? null);
              $deskripsi = trim((string) ($item['deskripsi'] ?? ''));
              $isAvailable = in_array(strtolower($status), ['dijual', 'tersedia'], true);
            ?>
            <article class="property-card"
              data-search="<?= esc(
                strtolower(trim(
                  implode(' ', [
                    $item['kode_rumah'] ?? '',
                    $item['lokasi'] ?? '',
                    $item['tipe'] ?? '',
                    $item['status'] ?? '',
                    $item['luas_tanah'] ?? '',
                    $item['luas_bangunan'] ?? '',
                    $item['harga'] ?? '',
                    $deskripsi,
                  ])
                ))
              ) ?>"
            >
              <div class="property-media">
                <?php if ($imageUrl): ?>
                  <img src="<?= esc($imageUrl, 'attr') ?>" alt="<?= esc($item['kode_rumah'] ?? 'Rumah') ?>">
                <?php else: ?>
                  <div class="no-image">
                    <span class="material-icons">home_work</span>
                  </div>
                <?php endif; ?>
                <div class="badge-row">
                  <span class="pill code"><?= esc($item['kode_rumah'] ?? '-') ?></span>
                  <span class="pill <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
                </div>
              </div>

              <div class="property-body">
                <h3 class="property-title">Tipe <?= esc($item['tipe'] ?? '-') ?></h3>
                <p class="property-location"><?= esc($item['lokasi'] ?? '-') ?></p>

                <div class="spec-grid">
                  <div class="spec">
                    <div class="spec-label">Luas Tanah</div>
                    <div class="spec-value"><?= esc($item['luas_tanah'] ?? '-') ?> m²</div>
                  </div>
                  <div class="spec">
                    <div class="spec-label">Luas Bangunan</div>
                    <div class="spec-value"><?= esc($item['luas_bangunan'] ?? '-') ?> m²</div>
                  </div>
                </div>

                <div class="description">
                  <?= $deskripsi !== '' ? nl2br(esc($deskripsi)) : 'Tambahkan deskripsi detail perumahan...' ?>
                </div>

                <div class="price-row">
                  <div>
                    <div class="price">Rp <?= number_format((float) ($item['harga'] ?? 0), 0, ',', '.') ?></div>
                    <?php if ($isAvailable): ?>
                      <div style="margin-top:6px; color:#059669; font-size:12px; font-weight:800;">Ready stock</div>
                    <?php endif; ?>
                  </div>
                </div>

                <a class="card-link" href="/perumahan/data-rumah/<?= (int) ($item['id'] ?? 0) ?>">
                  <span class="material-icons" style="font-size:18px;">visibility</span>
                  <?= $isAvailable ? 'Booking Sekarang' : 'Lihat Detail' ?>
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <footer class="site-footer">
    <div class="footer-inner">
      <strong>GreenHome.id</strong> &copy; <?= date('Y') ?> Sistem Manajemen Perumahan.
    </div>
  </footer>

  <script>
    (function () {
      const searchInput = document.getElementById('houseSearch');
      const clearBtn = document.getElementById('clearSearchBtn');
      const cards = Array.from(document.querySelectorAll('.property-card'));
      const resultsCount = document.getElementById('resultsCount');

      function updateResultsCount(visible) {
        if (resultsCount) {
          resultsCount.textContent = `${visible} hasil ditemukan`;
        }
      }

      function filterCards(query) {
        const normalized = String(query || '').trim().toLowerCase();
        let visible = 0;

        cards.forEach((card) => {
          const haystack = card.dataset.search || '';
          const match = normalized === '' || haystack.includes(normalized);
          card.style.display = match ? '' : 'none';
          if (match) visible++;
        });

        updateResultsCount(visible);
      }

      if (searchInput) {
        searchInput.addEventListener('input', (event) => filterCards(event.target.value));
      }

      if (clearBtn && searchInput) {
        clearBtn.addEventListener('click', () => {
          searchInput.value = '';
          filterCards('');
          searchInput.focus();
        });
      }
    })();
  </script>
</body>
</html>
