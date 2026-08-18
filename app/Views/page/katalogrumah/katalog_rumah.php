<?php
$rumah = is_array($rumah ?? null) ? $rumah : [];
$totalRumah = count($rumah);
$rumahPerPage = 2;
$page = (int) ($_GET['page'] ?? 1);
$totalPages = max(1, ceil($totalRumah / $rumahPerPage));
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $rumahPerPage;
$rumahPaginated = array_slice($rumah, $offset, $rumahPerPage, true);

$summary = [
    'total'    => count($rumah),
    'tersedia' => 0,
    'booked'   => 0,
    'terjual'  => 0,
];

foreach ($rumah as $item) {
    $status = strtolower((string) ($item['status'] ?? ''));
    if (in_array($status, ['dijual', 'tersedia'], true)) $summary['tersedia']++;
    if (in_array($status, ['booked', 'booking'], true))  $summary['booked']++;
    if (in_array($status, ['terjual', 'lunas'], true))   $summary['terjual']++;
}

function kat_status_class(string $s): string {
    return match (strtolower($s)) {
        'dijual', 'tersedia'                        => 'status-primary',
        'terjual', 'lunas'                          => 'status-success',
        'booked', 'booking', 'proses pembangunan'   => 'status-warning',
        default                                     => 'status-secondary',
    };
}

function kat_status_label(string $s): string {
    return match (strtolower($s)) {
        'dijual', 'tersedia'         => 'Tersedia',
        'terjual', 'lunas'           => 'Terjual',
        'booked', 'booking'          => 'Booked',
        'proses pembangunan'         => 'Proses Pembangunan',
        default                      => 'Tersedia',
    };
}

function kat_image_url(?string $path): ?string {
    $path = trim((string) $path);
    return $path === '' ? null : base_url(ltrim($path, '/'));
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= esc($pageTitle ?? 'Katalog Rumah - GreenHome.id') ?></title>
  <meta name="description" content="Lihat katalog lengkap rumah di GreenHome.id. Temukan hunian tersedia, booked, dan info tipe, harga, serta lokasi.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    :root {
      --bg: #f0f5fb;
      --surface: rgba(255,255,255,0.93);
      --surface-strong: #ffffff;
      --text: #0f1f35;
      --muted: #5f7289;
      --border: #d9e5f0;
      --primary: #1d4ed8;
      --primary-light: #dbeafe;
      --success: #059669;
      --shadow: 0 22px 50px rgba(15,23,42,0.13);
      --shadow-soft: 0 6px 22px rgba(15,23,42,0.07);
      --radius: 20px;
      --radius-sm: 12px;
      --shell: min(1440px, calc(100% - clamp(24px, 5vw, 64px)));
    }
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; }
    a { color: inherit; }

    /* HEADER */
    .site-header { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,0.88); backdrop-filter: blur(18px); border-bottom: 1px solid var(--border); box-shadow: 0 2px 14px rgba(15,23,42,0.06); }
    .site-header-inner { width: var(--shell); margin: 0 auto; min-height: 74px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
    .hero-inner,
    .content-inner,
    .footer-inner {
      width: var(--shell);
      margin: 0 auto;
    }
    .brand { display: inline-flex; align-items: center; gap: 12px; text-decoration: none; font-weight: 900; letter-spacing: -0.02em; }
    .brand-mark { width: 40px; height: 40px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary), #0ea5e9); color: #fff; box-shadow: var(--shadow-soft); }
    .brand-mark .material-icons { font-size: 20px; }
    .brand-name { font-size: 18px; color: var(--text); }
    .header-nav { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .header-nav a { text-decoration: none; color: var(--muted); font-size: 14px; font-weight: 700; }
    .header-nav a:hover { color: var(--primary); }
    .header-nav a.active { color: var(--primary); font-weight: 800; }
    .footer-nav { display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; margin-bottom: 10px; }
    .footer-nav a { color: var(--muted); font-size: 13px; font-weight: 700; text-decoration: none; }
    .footer-nav a:hover,
    .footer-nav a.active { color: var(--primary); }
    .btn-login { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 0 16px; border-radius: 999px; border: 1px solid rgba(29,78,216,0.16); background: rgba(255,255,255,0.92); text-decoration: none; color: var(--primary); font-weight: 800; box-shadow: 0 8px 20px rgba(29,78,216,0.08); }
    .btn-login:hover { background: var(--primary); color: #fff; }

    .btn-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 42px;
      padding: 0 16px;
      border-radius: 999px;
      border: 1px solid rgba(29,78,216,0.16);
      background: rgba(255,255,255,0.92);
      text-decoration: none;
      color: var(--primary);
      font-weight: 800;
      box-shadow: 0 8px 20px rgba(29,78,216,0.08);
    }

    .btn-link-primary {
      border-color: rgba(37, 99, 235, 0.18);
      background: linear-gradient(135deg, var(--primary), #1d4ed8);
      color: #fff;
    }

    .hero-actions {
      display: flex;
      gap: 12px;
      margin-top: 22px;
      flex-wrap: wrap;
    }

    /* HERO */
    .hero { padding: clamp(18px, 3vw, 30px) 0 clamp(14px, 2vw, 18px); }

    .hero-panel {
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.35);
      border-radius: 28px;
      background:
        linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(15, 118, 110, 0.55)),
        url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
      color: #fff;
      box-shadow: var(--shadow);
    }

    .hero-panel-inner {
      padding: clamp(24px, 4vw, 44px);
      display: grid;
      grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
      gap: clamp(18px, 3vw, 28px);
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

    .search-box input,
    .search-box select {
      min-height: 48px;
      border: 0;
      border-radius: 14px;
      font: inherit;
      outline: none;
      color: var(--text);
    }

    .search-box input {
      flex: 1;
      min-width: 0;
      padding: 0 14px;
    }

    .search-box select {
      padding: 0 12px;
      background: #eef4ff;
      color: var(--text);
      font-weight: 700;
      cursor: pointer;
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

    .search-box button:hover {
      opacity: 0.95;
    }

    .stats {
      margin-top: 18px;
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: clamp(10px, 1.4vw, 14px);
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

    /* MAIN */
    .page-content { flex: 1; width: var(--shell); margin: 0 auto; padding: clamp(24px, 3vw, 36px) 0 clamp(48px, 5vw, 64px); }
    .section-head { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
    .section-head h2 { font-size: 24px; font-weight: 900; letter-spacing: -0.03em; }
    .results-badge { background: var(--primary-light); color: var(--primary); font-size: 13px; font-weight: 700; padding: 5px 14px; border-radius: 999px; }

    /* GRID */
    .catalog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

    /* CARD */
    .property-card { background: var(--surface-strong); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-soft); overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column; }
    .property-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); }
    .property-media { position: relative; height: 210px; background: linear-gradient(135deg, #dbeafe, #d1fae5); overflow: hidden; flex-shrink: 0; }
    .property-media img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s; }
    .property-card:hover .property-media img { transform: scale(1.05); }
    .no-image { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: rgba(19,34,56,0.35); }
    .no-image .material-icons { font-size: 56px; }
    .badge-row { position: absolute; inset: 12px 12px auto 12px; display: flex; justify-content: space-between; gap: 8px; pointer-events: none; }
    .pill { display: inline-flex; align-items: center; gap: 5px; min-height: 28px; padding: 0 11px; border-radius: 999px; font-size: 11px; font-weight: 800; backdrop-filter: blur(14px); box-shadow: 0 4px 12px rgba(15,23,42,0.12); }
    .pill.code { background: rgba(255,255,255,0.9); color: #0f172a; }
    .pill.status-success { background: rgba(16,185,129,0.92); color: #fff; }
    .pill.status-primary { background: rgba(29,78,216,0.92); color: #fff; }
    .pill.status-warning { background: rgba(217,119,6,0.92); color: #fff; }
    .pill.status-secondary { background: rgba(100,116,139,0.92); color: #fff; }
    .property-body { padding: 18px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
    .property-title { font-size: 17px; font-weight: 800; letter-spacing: -0.02em; line-height: 1.3; }
    .property-location { display: flex; align-items: flex-start; gap: 5px; color: var(--muted); font-size: 13px; line-height: 1.5; }
    .property-location .material-icons { font-size: 16px; margin-top: 2px; flex-shrink: 0; }
    .spec-row { display: flex; gap: 10px; }
    .spec-item { flex: 1; background: var(--bg); border-radius: var(--radius-sm); padding: 9px 12px; }
    .spec-label { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; }
    .spec-value { font-size: 14px; font-weight: 800; margin-top: 3px; color: var(--text); }
    .description { font-size: 13px; color: var(--muted); line-height: 1.65; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
    .price-strip { display: flex; align-items: center; justify-content: space-between; padding-top: 10px; border-top: 1px solid var(--border); margin-top: auto; }
    .price { font-size: 18px; font-weight: 900; color: var(--primary); letter-spacing: -0.03em; }
    .price-note { font-size: 11px; color: var(--success); font-weight: 700; margin-top: 2px; }
    .card-cta { display: inline-flex; align-items: center; gap: 5px; text-decoration: none; background: var(--primary); color: #fff; font-size: 12px; font-weight: 700; padding: 8px 14px; border-radius: 10px; transition: background 0.15s, transform 0.1s; white-space: nowrap; }
    .card-cta:hover { background: #1e40af; transform: translateY(-1px); }
    .card-cta .material-icons { font-size: 15px; }

    /* EMPTY */
    .empty-state { text-align: center; padding: 80px 24px; color: var(--muted); }
    .empty-state .material-icons { font-size: 64px; margin-bottom: 16px; opacity: 0.4; display: block; }

    /* PAGINATION */
    .pagination { display: flex; justify-content: center; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 32px; }
    .page-link { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 40px; min-width: 40px; padding: 0 14px; border-radius: 999px; background: var(--surface-strong); border: 1px solid var(--border); color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 800; box-shadow: var(--shadow-soft); transition: transform 0.16s ease, background-color 0.16s ease, color 0.16s ease; }
    .page-link:hover { background: var(--primary); border-color: var(--primary); color: #fff; }
    .page-link.disabled { opacity: 0.45; pointer-events: none; }
    .page-link.active { background: linear-gradient(135deg, var(--primary), #1e40af); border-color: transparent; color: #fff; }
    .pagination-info { flex-basis: 100%; text-align: center; color: var(--muted); font-size: 13px; font-weight: 700; margin-top: 4px; }

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
      .site-header-inner,
      .hero-inner,
      .content-inner,
      .footer-inner,
      .page-content {
        width: min(100%, calc(100% - 32px));
      }

      .hero-panel-inner {
        grid-template-columns: 1fr;
      }

      .stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    /* RESPONSIVE */
    @media (max-width: 640px) {
      .site-header-inner,
      .hero-inner,
      .content-inner,
      .footer-inner,
      .page-content {
        width: min(100%, calc(100% - 20px));
      }

      .hero {
        padding: 20px 0 12px;
      }

      .hero-panel-inner {
        padding: 22px;
      }
      .page-content { padding: 22px 0 48px; }

      .search-box {
        flex-direction: column;
      }

      .stats {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <?= $this->include('page/navbar') ?>

  <section class="hero">
    <div class="hero-inner">
      <div class="hero-panel">
        <div class="hero-panel-inner">
          <div>
            <div class="hero-badge">
              <span class="material-icons" style="font-size:18px;">verified</span>
              Ketersediaan unit diperbarui dari data terbaru
            </div>
            <h1>Daftar rumah yang siap Anda bandingkan.</h1>
            <p>
              Lihat stok rumah yang tersedia, yang sedang dibooking, dan yang sudah terjual dalam satu tampilan yang rapi.
              Setiap kartu memuat detail inti seperti kode rumah, lokasi, tipe, luas, dan harga agar proses memilih terasa cepat dan jelas.
            </p>
            <div class="hero-actions">
              <a href="#daftar-rumah" class="btn-link btn-link-primary">
                <span class="material-icons" style="font-size:18px;">explore</span>
                Lihat Daftar Rumah
              </a>
            </div>
          </div>

          <div class="search-panel">
            <div class="search-label">Cari rumah berdasarkan kode, lokasi, tipe, atau status</div>
            <div class="search-box">
              <input type="text" id="kat-search" placeholder="Contoh: RUM001, Banyuwangi, Tipe 45..." autocomplete="off">
              <select id="kat-filter-status" aria-label="Filter status rumah">
                <option value="">Semua Status</option>
                <option value="tersedia">Tersedia</option>
                <option value="booked">Booked</option>
                <option value="terjual">Terjual</option>
              </select>
              <button type="button" id="kat-reset">Reset</button>
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

  <main class="page-content">
    <div class="section-head" id="daftar-rumah">
      <h2>Daftar Rumah</h2>
      <span class="results-badge" id="kat-count"><?= count($rumah) ?> rumah ditemukan</span>
    </div>

    <?php if (empty($rumah)): ?>
      <div class="empty-state">
        <span class="material-icons">home_work</span>
        <p>Belum ada data rumah yang tersedia saat ini.</p>
      </div>
    <?php else: ?>
      <div class="catalog-grid" id="kat-grid">
        <?php foreach ($rumahPaginated as $item): ?>
          <?php
            $status      = (string) ($item['status'] ?? '-');
            $stClass     = kat_status_class($status);
            $stLabel     = kat_status_label($status);
            $imageUrl    = kat_image_url($item['gambar'] ?? null);
            $deskripsi   = trim((string) ($item['deskripsi'] ?? ''));
            $isAvailable = in_array(strtolower($status), ['dijual', 'tersedia'], true);
            $searchData  = strtolower(implode(' ', [
                $item['kode_rumah']    ?? '',
                $item['lokasi']        ?? '',
                $item['tipe']          ?? '',
                $item['status']        ?? '',
                $item['luas_tanah']    ?? '',
                $item['luas_bangunan'] ?? '',
                $deskripsi,
            ]));
          ?>
          <article class="property-card"
            data-search="<?= esc($searchData) ?>"
            data-status="<?= esc(strtolower($status)) ?>"
          >
            <div class="property-media">
              <?php if ($imageUrl): ?>
                <img src="<?= esc($imageUrl, 'attr') ?>" alt="<?= esc($item['kode_rumah'] ?? 'Rumah') ?>">
              <?php else: ?>
                <div class="no-image"><span class="material-icons">home_work</span></div>
              <?php endif; ?>
              <div class="badge-row">
                <span class="pill code"><?= esc($item['kode_rumah'] ?? '-') ?></span>
                <span class="pill <?= $stClass ?>"><?= esc($stLabel) ?></span>
              </div>
            </div>
            <div class="property-body">
              <h3 class="property-title">Tipe <?= esc($item['tipe'] ?? '-') ?></h3>
              <div class="property-location">
                <span class="material-icons">location_on</span>
                <?= esc($item['lokasi'] ?? '-') ?>
              </div>
              <div class="spec-row">
                <div class="spec-item">
                  <div class="spec-label">Luas Tanah</div>
                  <div class="spec-value"><?= esc($item['luas_tanah'] ?? '-') ?> m&sup2;</div>
                </div>
                <div class="spec-item">
                  <div class="spec-label">Luas Bangunan</div>
                  <div class="spec-value"><?= esc($item['luas_bangunan'] ?? '-') ?> m&sup2;</div>
                </div>
              </div>
              <?php if ($deskripsi !== ''): ?>
                <div class="description"><?= nl2br(esc($deskripsi)) ?></div>
              <?php endif; ?>
              <div class="price-strip">
                <div>
                  <div class="price">Rp <?= number_format((float) ($item['harga'] ?? 0), 0, ',', '.') ?></div>
                  <?php if ($isAvailable): ?>
                    <div class="price-note">Ready stock</div>
                  <?php endif; ?>
                </div>
                <a class="card-cta" href="/detail-rumah/<?= (int) ($item['id'] ?? 0) ?>">
                  <span class="material-icons">visibility</span>
                  Detail
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Pagination daftar rumah">
        <a
          class="page-link <?= $page <= 1 ? 'disabled' : '' ?>"
          href="<?= $page > 1 ? '?page=' . ($page - 1) : '#' ?>"
          <?= $page <= 1 ? 'aria-disabled="true"' : '' ?>
        >
          <span class="material-icons" style="font-size:16px;">chevron_left</span>
          Sebelumnya
        </a>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a class="page-link <?= $i === $page ? 'active' : '' ?>" href="?page=<?= $i ?>" <?= $i === $page ? 'aria-current="page"' : '' ?>>
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <a
          class="page-link <?= $page >= $totalPages ? 'disabled' : '' ?>"
          href="<?= $page < $totalPages ? '?page=' . ($page + 1) : '#' ?>"
          <?= $page >= $totalPages ? 'aria-disabled="true"' : '' ?>
        >
          Selanjutnya
          <span class="material-icons" style="font-size:16px;">chevron_right</span>
        </a>

        <div class="pagination-info">
          Menampilkan <?= count($rumahPaginated) ?> dari <?= $totalRumah ?> rumah (halaman <?= $page ?> dari <?= $totalPages ?>)
        </div>
      </nav>
    <?php endif; ?>
  </main>

  <?= $this->include('page/footer') ?>

  <script>
    (function () {
      const searchInput  = document.getElementById('kat-search');
      const filterSelect = document.getElementById('kat-filter-status');
      const resetBtn     = document.getElementById('kat-reset');
      const countEl      = document.getElementById('kat-count');
      const cards        = Array.from(document.querySelectorAll('.property-card'));

      function updateCount(n) {
        if (countEl) countEl.textContent = n + ' rumah ditemukan';
      }

      function applyFilters() {
        const query  = (searchInput?.value  || '').trim().toLowerCase();
        const status = (filterSelect?.value || '').trim().toLowerCase();
        let visible  = 0;
        cards.forEach(card => {
          const haystack   = card.dataset.search || '';
          const cardStatus = card.dataset.status  || '';
          const matchQuery  = query  === '' || haystack.includes(query);
          const matchStatus = status === '' || cardStatus.includes(status);
          const show = matchQuery && matchStatus;
          card.style.display = show ? '' : 'none';
          if (show) visible++;
        });
        updateCount(visible);
      }

      searchInput?.addEventListener('input', applyFilters);
      filterSelect?.addEventListener('change', applyFilters);
      resetBtn?.addEventListener('click', () => {
        if (searchInput)  searchInput.value  = '';
        if (filterSelect) filterSelect.value = '';
        applyFilters();
        searchInput?.focus();
      });
    })();
  </script>
</body>
</html>
