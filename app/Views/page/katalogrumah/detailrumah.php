<?php
$rumah = is_array($rumah ?? null) ? $rumah : [];

$status = strtolower((string) ($rumah['status'] ?? ''));
$statusClass = 'status-secondary';
$statusLabel = 'Tersedia';

if (in_array($status, ['dijual', 'tersedia'], true)) {
    $statusClass = 'status-primary';
    $statusLabel = 'Tersedia';
} elseif (in_array($status, ['terjual', 'lunas'], true)) {
    $statusClass = 'status-success';
    $statusLabel = 'Terjual';
} elseif (in_array($status, ['booked', 'booking', 'proses pembangunan'], true)) {
    $statusClass = 'status-warning';
    $statusLabel = 'Booked';
}

$detailRumahImageList = static function ($raw): array {
    $items = [];

    if (is_array($raw)) {
        $items = $raw;
    } else {
        $value = trim((string) $raw);
        if ($value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = preg_split('/[\r\n,|]+/', $value) ?: [];
            }
        }
    }

    $images = [];
    foreach ($items as $item) {
        $path = trim((string) $item);
        if ($path === '') {
            continue;
        }

        if (preg_match('~^https?://~i', $path)) {
            $images[] = $path;
            continue;
        }

        $images[] = base_url(ltrim($path, '/'));
    }

    return array_values(array_unique($images));
};

$imageList = $detailRumahImageList($rumah['gambar'] ?? null);
$imageUrl = $imageList[0] ?? null;
$imageCount = count($imageList);
$deskripsi = trim((string) ($rumah['deskripsi'] ?? ''));
$error = session()->getFlashdata('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? 'Detail Rumah - GreenHome.id') ?></title>
  <meta name="description" content="Detail rumah lengkap di GreenHome.id">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    :root {
      --bg: #f0f5fb;
      --text: #0f1f35;
      --muted: #607287;
      --border: #d9e5f0;
      --primary: #1d4ed8;
      --primary-light: #dbeafe;
      --success: #059669;
      --warning: #d97706;
      --shadow: 0 22px 50px rgba(15,23,42,0.12);
      --shadow-soft: 0 6px 22px rgba(15,23,42,0.07);
      --radius: 22px;
      --shell: min(1440px, calc(100% - clamp(24px, 5vw, 64px)));
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background:
        radial-gradient(circle at top left, rgba(219,234,254,0.75), transparent 28%),
        radial-gradient(circle at top right, rgba(209,250,229,0.55), transparent 24%),
        var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    a { color: inherit; }

    .page-shell {
      width: var(--shell);
      margin: 0 auto;
      padding: 30px 0 64px;
      flex: 1;
    }

    .breadcrumb {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 18px;
      color: var(--muted);
      font-size: 13px;
      font-weight: 700;
      text-decoration: none;
    }

    .breadcrumb:hover { color: var(--primary); }

    .detail-grid {
      display: grid;
      grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.78fr);
      gap: 20px;
      align-items: start;
    }

    .panel {
      background: rgba(255,255,255,0.94);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-soft);
      overflow: hidden;
    }

    .hero-media {
      position: relative;
      background: linear-gradient(135deg, #dbeafe, #d1fae5);
    }

    .hero-media-stage {
      position: relative;
      min-height: 420px;
      overflow: hidden;
    }

    .hero-media img,
    .hero-media .no-image {
      width: 100%;
      height: 100%;
      min-height: 420px;
      object-fit: cover;
      display: block;
    }

    .hero-media .no-image {
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(15,31,53,0.34);
    }

    .hero-gallery-nav {
      position: absolute;
      inset: 50% 14px auto;
      display: flex;
      justify-content: space-between;
      transform: translateY(-50%);
      pointer-events: none;
      z-index: 2;
    }

    .hero-gallery-btn {
      width: 44px;
      height: 44px;
      border: 0;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(15, 23, 42, 0.72);
      color: #fff;
      cursor: pointer;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
      pointer-events: auto;
      transition: transform 0.16s ease, background-color 0.16s ease;
    }

    .hero-gallery-btn:hover {
      transform: scale(1.04);
      background: rgba(29, 78, 216, 0.9);
    }

    .hero-gallery-btn:disabled {
      opacity: 0.35;
      cursor: not-allowed;
      transform: none;
    }

    .hero-gallery-btn .material-icons {
      font-size: 22px;
    }

    .hero-gallery-thumbs {
      display: flex;
      gap: 10px;
      padding: 14px 14px 16px;
      overflow-x: auto;
      background: rgba(255,255,255,0.9);
      border-top: 1px solid var(--border);
    }

    .hero-thumb {
      flex: 0 0 auto;
      width: 84px;
      height: 62px;
      padding: 0;
      border: 2px solid transparent;
      border-radius: 14px;
      overflow: hidden;
      cursor: pointer;
      background: #eef4ff;
      transition: border-color 0.16s ease, transform 0.16s ease, box-shadow 0.16s ease;
    }

    .hero-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .hero-thumb:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 20px rgba(15,23,42,0.08);
    }

    .hero-thumb.active {
      border-color: var(--primary);
    }

    .hero-overlay {
      position: absolute;
      inset: auto 0 0 0;
      padding: 24px;
      background: linear-gradient(180deg, transparent 0%, rgba(5,15,30,0.84) 100%);
      color: #fff;
      z-index: 1;
    }

    .chip-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 16px;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-height: 32px;
      padding: 0 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
    }

    .pill.code { background: rgba(255,255,255,0.9); color: #334155; }
    .status-success { background: #d1fae5; color: #047857; }
    .status-primary { background: #dbeafe; color: #1d4ed8; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-secondary { background: #e2e8f0; color: #334155; }

    .hero-overlay h1 {
      margin: 0 0 8px;
      font-size: clamp(28px, 4vw, 42px);
      font-weight: 900;
      letter-spacing: -0.03em;
    }

    .hero-overlay p {
      margin: 0;
      max-width: 720px;
      line-height: 1.6;
      color: rgba(255,255,255,0.92);
    }

    .section-block { padding: 22px; }

    .section-title {
      margin: 0 0 14px;
      font-size: 18px;
      font-weight: 900;
      letter-spacing: -0.02em;
    }

    .feature-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
    }

    .feature-card {
      min-height: 76px;
      padding: 14px 16px;
      border: 1px solid var(--border);
      border-radius: 18px;
      background: linear-gradient(180deg, #fff, #f8fbff);
    }

    .feature-label {
      margin-bottom: 6px;
      color: var(--muted);
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .feature-value {
      color: var(--text);
      font-size: 15px;
      font-weight: 800;
      overflow-wrap: anywhere;
    }

    .description-copy {
      color: #435166;
      line-height: 1.75;
      font-size: 14px;
      white-space: pre-line;
    }

    .summary-card {
      position: sticky;
      top: 92px;
    }

    .summary-header {
      padding: 22px 22px 18px;
      background: linear-gradient(135deg, #0f172a, #174e8a 60%, #0f766e);
      color: #fff;
    }

    .summary-header .muted {
      color: rgba(255,255,255,0.8);
      font-size: 13px;
      font-weight: 700;
    }

    .price-box {
      padding: 22px;
      border-bottom: 1px solid var(--border);
    }

    .price-label {
      margin-bottom: 4px;
      color: var(--muted);
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .price-value {
      color: var(--success);
      font-size: 30px;
      font-weight: 900;
      letter-spacing: -0.03em;
    }

    .summary-list {
      padding: 20px 22px;
      display: grid;
      gap: 12px;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding: 12px 0;
      border-bottom: 1px dashed var(--border);
      font-size: 14px;
    }

    .summary-item:last-child { border-bottom: 0; }
    .summary-item .label { color: var(--muted); font-weight: 700; }
    .summary-item .value { color: var(--text); font-weight: 800; text-align: right; }

    .summary-actions {
      padding: 0 22px 22px;
      display: grid;
      gap: 10px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 44px;
      padding: 0 16px;
      border-radius: 999px;
      border: 1px solid transparent;
      font-size: 14px;
      font-weight: 800;
      text-decoration: none;
      transition: transform 0.16s ease, background-color 0.16s ease, color 0.16s ease;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), #1d4ed8);
      color: #fff;
    }

    .btn-secondary {
      background: #eef4ff;
      color: var(--primary);
      border-color: #dbeafe;
    }

    .btn:hover { transform: translateY(-1px); }

    .notice {
      margin-bottom: 18px;
      padding: 14px 16px;
      border-radius: 16px;
      background: rgba(219,234,254,0.85);
      color: #1d4ed8;
      font-weight: 700;
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

    @media (max-width: 1024px) {
      .detail-grid { grid-template-columns: 1fr; }
      .summary-card { position: static; }
    }

    @media (max-width: 767px) {
      .page-shell {
        width: min(100%, calc(100% - 20px));
        padding: 22px 0 48px;
      }

      .hero-media-stage,
      .hero-media img,
      .hero-media .no-image {
        min-height: 320px;
      }

      .hero-gallery-nav {
        inset: 50% 10px auto;
      }

      .hero-gallery-btn {
        width: 40px;
        height: 40px;
      }

      .hero-gallery-thumbs {
        padding: 12px;
      }

      .hero-thumb {
        width: 74px;
        height: 54px;
      }

      .section-block,
      .summary-header,
      .price-box,
      .summary-list,
      .summary-actions,
      .hero-overlay {
        padding-left: 18px;
        padding-right: 18px;
      }

      .feature-grid {
        grid-template-columns: 1fr;
      }

      .price-value {
        font-size: 26px;
      }
    }
  </style>
</head>
<body>
  <?= $this->include('page/navbar') ?>

  <main class="page-shell">
    <a class="breadcrumb" href="/katalog-rumah">
      <span class="material-icons" style="font-size:18px;">arrow_back</span>
      Kembali ke katalog
    </a>

    <?php if ($error): ?>
      <div class="notice"><?= esc($error) ?></div>
    <?php endif; ?>

    <div class="detail-grid">
      <section class="panel">
        <div class="hero-media">
          <div class="hero-media-stage">
            <?php if ($imageUrl): ?>
              <img
                src="<?= esc($imageUrl, 'attr') ?>"
                alt="<?= esc($rumah['kode_rumah'] ?? 'Rumah') ?>"
                data-gallery-image
              >
            <?php else: ?>
              <div class="no-image"><span class="material-icons" style="font-size:72px;">home_work</span></div>
            <?php endif; ?>

            <?php if ($imageCount > 1): ?>
              <div class="hero-gallery-nav">
                <button class="hero-gallery-btn" type="button" data-gallery-prev aria-label="Gambar sebelumnya">
                  <span class="material-icons">chevron_left</span>
                </button>
                <button class="hero-gallery-btn" type="button" data-gallery-next aria-label="Gambar berikutnya">
                  <span class="material-icons">chevron_right</span>
                </button>
              </div>
            <?php endif; ?>

            <div class="hero-overlay">
              <div class="chip-row">
                <span class="pill code"><?= esc($rumah['kode_rumah'] ?? '-') ?></span>
                <span class="pill <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
              </div>
              <h1>Tipe <?= esc($rumah['tipe'] ?? '-') ?></h1>
              <p><?= esc($rumah['lokasi'] ?? '-') ?></p>
            </div>
          </div>

          <?php if ($imageCount > 1): ?>
            <div class="hero-gallery-thumbs">
              <?php foreach ($imageList as $index => $thumbUrl): ?>
                <button
                  class="hero-thumb <?= $index === 0 ? 'active' : '' ?>"
                  type="button"
                  data-gallery-thumb
                  data-gallery-src="<?= esc($thumbUrl, 'attr') ?>"
                  aria-label="Tampilkan gambar <?= (int) $index + 1 ?>"
                >
                  <img src="<?= esc($thumbUrl, 'attr') ?>" alt="<?= esc($rumah['kode_rumah'] ?? 'Rumah') ?> foto <?= (int) $index + 1 ?>">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="section-block">
          <h2 class="section-title">Spesifikasi Rumah</h2>
          <div class="feature-grid">
            <div class="feature-card">
              <div class="feature-label">Kode Rumah</div>
              <div class="feature-value"><?= esc($rumah['kode_rumah'] ?? '-') ?></div>
            </div>
            <div class="feature-card">
              <div class="feature-label">Status</div>
              <div class="feature-value"><?= esc($statusLabel) ?></div>
            </div>
            <div class="feature-card">
              <div class="feature-label">Luas Tanah</div>
              <div class="feature-value"><?= esc($rumah['luas_tanah'] ?? '-') ?> m&sup2;</div>
            </div>
            <div class="feature-card">
              <div class="feature-label">Luas Bangunan</div>
              <div class="feature-value"><?= esc($rumah['luas_bangunan'] ?? '-') ?> m&sup2;</div>
            </div>
            <div class="feature-card">
              <div class="feature-label">Lokasi</div>
              <div class="feature-value"><?= esc($rumah['lokasi'] ?? '-') ?></div>
            </div>
            <div class="feature-card">
              <div class="feature-label">Harga</div>
              <div class="feature-value">Rp <?= number_format((float) ($rumah['harga'] ?? 0), 0, ',', '.') ?></div>
            </div>
          </div>
        </div>

        <div class="section-block" style="border-top:1px solid var(--border);">
          <h2 class="section-title">Deskripsi</h2>
          <div class="description-copy">
            <?= $deskripsi !== '' ? nl2br(esc($deskripsi)) : 'Informasi deskripsi rumah belum tersedia.' ?>
          </div>
        </div>
      </section>

      <aside class="panel summary-card">
        <div class="summary-header">
          <div class="muted">Ringkasan</div>
          <h2 style="margin:6px 0 0;font-size:24px;letter-spacing:-0.03em;"><?= esc($rumah['kode_rumah'] ?? '-') ?></h2>
        </div>
        <div class="price-box">
          <div class="price-label">Harga Rumah</div>
          <div class="price-value">Rp <?= number_format((float) ($rumah['harga'] ?? 0), 0, ',', '.') ?></div>
        </div>
        <div class="summary-list">
          <div class="summary-item">
            <span class="label">Tipe</span>
            <span class="value"><?= esc($rumah['tipe'] ?? '-') ?></span>
          </div>
          <div class="summary-item">
            <span class="label">Status</span>
            <span class="value"><?= esc($statusLabel) ?></span>
          </div>
          <div class="summary-item">
            <span class="label">Luas Tanah</span>
            <span class="value"><?= esc($rumah['luas_tanah'] ?? '-') ?> m&sup2;</span>
          </div>
          <div class="summary-item">
            <span class="label">Luas Bangunan</span>
            <span class="value"><?= esc($rumah['luas_bangunan'] ?? '-') ?> m&sup2;</span>
          </div>
          <div class="summary-item">
            <span class="label">Lokasi</span>
            <span class="value"><?= esc($rumah['lokasi'] ?? '-') ?></span>
          </div>
        </div>
        <div class="summary-actions">
          <a class="btn btn-primary" href="/katalog-rumah">
            <span class="material-icons" style="font-size:18px;">grid_view</span>
            Kembali ke Katalog
          </a>
          <a class="btn btn-secondary" href="/login">
            <span class="material-icons" style="font-size:18px;">login</span>
            Pesan Sekarang
          </a>
        </div>
      </aside>
    </div>
  </main>

  <?= $this->include('page/footer') ?>

  <script>
    (function () {
      const galleryImage = document.querySelector('[data-gallery-image]');
      const prevBtn = document.querySelector('[data-gallery-prev]');
      const nextBtn = document.querySelector('[data-gallery-next]');
      const thumbs = Array.from(document.querySelectorAll('[data-gallery-thumb]'));

      if (!galleryImage || thumbs.length === 0) {
        return;
      }

      const sources = thumbs.map((btn) => btn.dataset.gallerySrc || '').filter(Boolean);
      if (!sources.length) {
        return;
      }

      let currentIndex = 0;

      function updateGallery(index) {
        currentIndex = (index + sources.length) % sources.length;
        galleryImage.src = sources[currentIndex];
        thumbs.forEach((btn, i) => {
          btn.classList.toggle('active', i === currentIndex);
        });
        if (prevBtn) prevBtn.disabled = sources.length <= 1;
        if (nextBtn) nextBtn.disabled = sources.length <= 1;
      }

      prevBtn?.addEventListener('click', function () {
        updateGallery(currentIndex - 1);
      });

      nextBtn?.addEventListener('click', function () {
        updateGallery(currentIndex + 1);
      });

      thumbs.forEach((btn, index) => {
        btn.addEventListener('click', function () {
          updateGallery(index);
        });
      });

      updateGallery(0);
    })();
  </script>
</body>
</html>
