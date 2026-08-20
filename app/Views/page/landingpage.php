<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GreenHome.id</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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

    header h2 {
      margin: 0;
      font-size: 20px;
      font-weight: 800;
    }

    nav {
      display: flex;
      align-items: center;
      gap: 18px;
    }

    nav a {
      color: rgba(255, 255, 255, 0.88);
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
    }

    nav a:hover { color: #ffffff; }

    .hero {
      min-height: 620px;
      padding: 108px 32px 54px;
      display: flex;
      align-items: flex-end;
      color: #ffffff;
      background:
        linear-gradient(90deg, rgba(26, 46, 68, 0.78), rgba(26, 46, 68, 0.38)),
        url('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1800&q=85') center/cover no-repeat;
    }

    .hero-inner {
      width: min(1080px, 100%);
      margin: 0 auto;
    }

    .hero h1 {
      margin: 0 0 14px;
      max-width: 680px;
      font-size: clamp(40px, 5vw, 60px);
      line-height: 1;
      font-weight: 800;
    }

    .hero p {
      max-width: 620px;
      margin: 0 0 24px;
      color: rgba(255, 255, 255, 0.88);
      font-size: 18px;
      line-height: 1.6;
    }

    .search-box {
      width: min(560px, 100%);
      display: flex;
      gap: 10px;
      padding: 8px;
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 20px 40px rgba(15, 23, 42, 0.22);
    }

    .search-box input {
      flex: 1;
      min-width: 0;
      min-height: 44px;
      padding: 10px 13px;
      border: 0;
      outline: none;
      font: inherit;
    }

    .search-box button {
      min-height: 44px;
      padding: 0 18px;
      border: 0;
      border-radius: 8px;
      background: #2B6BE5;
      color: white;
      font-weight: 700;
      cursor: pointer;
    }

    main {
      padding: 42px 32px 60px;
      background: #ffffff;
    }

    .section-title {
      width: min(1080px, 100%);
      margin: 0 auto 20px;
      font-size: 28px;
      color: #1A2E44;
      font-weight: 800;
    }

    .about-text {
      width: min(1080px, 100%);
      margin: 0 auto 42px;
      color: #647084;
      font-size: 16px;
      line-height: 1.7;
    }

    .card-container {
      width: min(1080px, 100%);
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
    }

    .card {
      background: #ffffff;
      border: 1px solid #e4e8ef;
      border-radius: 10px;
      box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
      overflow: hidden;
      text-decoration: none;
      color: inherit;
      display: block;
      transition: transform 0.16s ease, box-shadow 0.16s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 16px 32px rgba(15, 23, 42, 0.1);
    }

    .card img {
      width: 100%;
      height: 190px;
      object-fit: cover;
      background: #eef2f7;
    }

    .card-content { padding: 16px; }

    .card-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 10px;
    }

    .card-top h3 { margin: 0; flex: 1; }

    .status-pill {
      display: inline-flex;
      align-items: center;
      min-height: 24px;
      padding: 5px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
    }

    .status-success { background: #d1fae5; color: #047857; }
    .status-primary { background: #dbeafe; color: #1d4ed8; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-secondary { background: #e2e8f0; color: #334155; }

    .card-content h3 {
      margin: 0 0 8px;
      color: #1A2E44;
      font-size: 18px;
      font-weight: 800;
    }

    .card-content p {
      margin: 0 0 8px;
      font-size: 14px;
      color: #647084;
      line-height: 1.55;
    }

    .card-meta {
      margin: 0 0 10px;
      font-size: 13px;
      color: #647084;
    }

    .card-price {
      margin: 0 0 12px;
      color: #059669;
      font-size: 16px;
      font-weight: 800;
    }

    .card-link {
      color: #2B6BE5;
      font-size: 13px;
      font-weight: 700;
    }

    .card-gallery {
      position: relative;
      background: #eef2f7;
    }

    .card-gallery img {
      width: 100%;
      height: 190px;
      object-fit: cover;
      display: block;
      background: #eef2f7;
      cursor: zoom-in;
    }

    .card-gallery .no-image {
      height: 190px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #94a3b8;
    }

    .card-gallery .no-image .material-icons {
      font-size: 48px;
    }

    .img-slider-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 32px;
      height: 32px;
      padding: 0;
      margin: 0;
      border: 0;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.62);
      color: #fff;
      font-size: 20px;
      line-height: 1;
      cursor: pointer;
      display: none;
      align-items: center;
      justify-content: center;
      appearance: none;
      z-index: 3;
    }

    .card-gallery.has-many .img-slider-btn { display: inline-flex; }
    .img-slider-btn.prev { left: 12px; }
    .img-slider-btn.next { right: 12px; }

    .img-slider-count {
      position: absolute;
      right: 8px;
      bottom: 8px;
      padding: 2px 8px;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.6);
      color: #fff;
      font-size: 11px;
      font-weight: 700;
    }

    .img-preview-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 80;
      align-items: center;
      justify-content: center;
      padding: 28px 64px;
      background: rgba(15, 23, 42, 0.78);
    }
    .img-preview-overlay.open { display: flex; }
    .img-preview-overlay img {
      max-width: min(1100px, 92vw);
      max-height: 86vh;
      object-fit: contain;
      border-radius: 8px;
      background: #0f172a;
    }
    .img-preview-close,
    .img-preview-nav {
      position: absolute;
      border: 0;
      border-radius: 999px;
      background: #ffffff;
      color: #172033;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }
    .img-preview-close {
      top: 18px;
      right: 18px;
      width: 40px;
      height: 40px;
      font-size: 22px;
      font-weight: 700;
    }
    .img-preview-nav {
      top: 50%;
      transform: translateY(-50%);
      width: 44px;
      height: 44px;
      font-size: 28px;
    }
    .img-preview-nav.prev { left: 16px; }
    .img-preview-nav.next { right: 16px; }
    .img-preview-count {
      position: absolute;
      left: 50%;
      bottom: 18px;
      transform: translateX(-50%);
      padding: 6px 12px;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.7);
      color: #fff;
      font-size: 13px;
      font-weight: 700;
    }

    .empty-catalog {
      width: min(1080px, 100%);
      margin: 0 auto;
      padding: 32px;
      border: 1px dashed #d5dbe6;
      border-radius: 10px;
      color: #647084;
      text-align: center;
    }

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

    @media (max-width: 900px) {
      .card-container { grid-template-columns: 1fr; }
    }

    @media (max-width: 760px) {
      header { padding: 0 18px; }
      nav { display: none; }
      .hero { min-height: 560px; padding: 96px 18px 42px; }
      .search-box { display: block; }
      .search-box button { width: 100%; margin-top: 8px; }
      main { padding: 32px 18px 48px; }
    }
  </style>
</head>
<body>
  <?php
    $rumah = is_array($rumah ?? null) ? $rumah : [];
    $q = (string) ($q ?? '');
  ?>
  <header>
    <h2>GreenHome.id</h2>
    <nav>
      <a href="#beranda">Beranda</a>
      <a href="#tentang">Tentang</a>
      <a href="#daftar-rumah">Daftar Rumah</a>
      <a href="/login">Login</a>
    </nav>
  </header>

  <section class="hero" id="beranda">
    <div class="hero-inner">
      <h1>GreenHome.id</h1>
      <p>Hunian modern dengan lingkungan nyaman, lokasi strategis, dan pilihan rumah yang mudah dibandingkan.</p>
      <form class="search-box" action="/" method="get">
        <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Cari rumah berdasarkan lokasi, tipe, atau harga" />
        <button type="submit">Cari</button>
      </form>
    </div>
  </section>

  <main>
    <section id="tentang">
      <div class="section-title">Tentang</div>
      <p class="about-text">
        GreenHome.id menampilkan katalog rumah dari data perumahan yang dikelola sistem.
        Anda dapat membandingkan tipe, lokasi, luas, dan harga, lalu membuka detail rumah tanpa login. Pemesanan tetap memerlukan akun.
      </p>
    </section>

    <section id="daftar-rumah">
      <div class="section-title">Daftar Rumah</div>
      <?php if (empty($rumah)): ?>
        <div class="empty-catalog">
          <?= $q !== '' ? 'Tidak ada rumah yang cocok dengan pencarian.' : 'Belum ada data rumah.' ?>
        </div>
      <?php else: ?>
        <div class="card-container">
          <?php foreach ($rumah as $item): ?>
            <?php
              $harga = number_format((float) ($item['harga'] ?? 0), 0, ',', '.');
              $deskripsiDb = trim((string) ($item['deskripsi'] ?? ''));
              $status = strtolower((string) ($item['status'] ?? ''));
              $statusClass = 'status-secondary';
              if (in_array($status, ['dijual', 'tersedia'], true)) $statusClass = 'status-primary';
              elseif (in_array($status, ['terjual', 'lunas'], true)) $statusClass = 'status-success';
              elseif (in_array($status, ['proses pembangunan', 'booking', 'booked'], true)) $statusClass = 'status-warning';
            ?>
            <div class="card">
              <?php
                $gambarList = \App\Models\PerumahanModel::gambarUrls($item['gambar'] ?? null);
                $gambarCount = count($gambarList);
              ?>
              <div class="card-gallery<?= $gambarCount > 1 ? ' has-many' : '' ?>" data-slider data-images='<?= esc(json_encode($gambarList, JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_AMP), 'attr') ?>'>
                <?php if ($gambarCount > 0): ?>
                  <img data-slider-image src="<?= esc($gambarList[0]) ?>" alt="<?= esc($item['kode_rumah'] ?? 'Rumah') ?>">
                  <?php if ($gambarCount > 1): ?>
                    <button type="button" class="img-slider-btn prev" data-slider-prev aria-label="Sebelumnya">&lsaquo;</button>
                    <button type="button" class="img-slider-btn next" data-slider-next aria-label="Berikutnya">&rsaquo;</button>
                    <span class="img-slider-count" data-slider-count>1 / <?= (int) $gambarCount ?></span>
                  <?php endif; ?>
                <?php else: ?>
                  <div class="no-image"><span class="material-icons">home_work</span></div>
                <?php endif; ?>
              </div>
              <a class="card-content" href="/rumah/<?= (int) ($item['id'] ?? 0) ?>" style="display:block;text-decoration:none;color:inherit;">
                <div class="card-top">
                  <h3><?= esc($item['kode_rumah'] ?? 'Rumah') ?> · Tipe <?= esc($item['tipe'] ?? '-') ?></h3>
                  <span class="status-pill <?= $statusClass ?>"><?= esc($item['status'] ?? '-') ?></span>
                </div>
                <p class="card-meta"><?= esc($item['lokasi'] ?? '-') ?></p>
                <p class="card-meta">LT <?= esc($item['luas_tanah'] ?? '-') ?> m² · LB <?= esc($item['luas_bangunan'] ?? '-') ?> m²</p>
                <?php if ($deskripsiDb !== ''): ?>
                  <?php
                    $ringkas = $deskripsiDb;
                    if (strlen($ringkas) > 90) {
                      $ringkas = substr($ringkas, 0, 90) . '...';
                    }
                  ?>
                  <p><?= esc($ringkas) ?></p>
                <?php endif; ?>
                <div class="card-price">Rp <?= $harga ?></div>
                <span class="card-link">Lihat Detail</span>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
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
  <div class="img-preview-overlay" id="imgPreviewOverlay" role="dialog" aria-modal="true" aria-label="Preview gambar">
    <button type="button" class="img-preview-close" id="imgPreviewClose" aria-label="Tutup">&times;</button>
    <button type="button" class="img-preview-nav prev" id="imgPreviewPrev" aria-label="Sebelumnya">&lsaquo;</button>
    <img id="imgPreviewPhoto" src="" alt="Preview rumah">
    <button type="button" class="img-preview-nav next" id="imgPreviewNext" aria-label="Berikutnya">&rsaquo;</button>
    <div class="img-preview-count" id="imgPreviewCount"></div>
  </div>
  <script>
    (function () {
      const overlay = document.getElementById('imgPreviewOverlay');
      const photo = document.getElementById('imgPreviewPhoto');
      const countEl = document.getElementById('imgPreviewCount');
      const prevBtn = document.getElementById('imgPreviewPrev');
      const nextBtn = document.getElementById('imgPreviewNext');
      let images = [];
      let index = 0;

      function renderPreview() {
        if (!images.length) return;
        photo.src = images[index];
        countEl.textContent = (index + 1) + ' / ' + images.length;
        const many = images.length > 1;
        prevBtn.style.display = many ? 'inline-flex' : 'none';
        nextBtn.style.display = many ? 'inline-flex' : 'none';
      }

      function openPreview(list, start) {
        images = list;
        index = start || 0;
        renderPreview();
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
      }

      function closePreview() {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
      }

      function step(delta) {
        if (images.length < 2) return;
        index = (index + delta + images.length) % images.length;
        renderPreview();
      }

      document.querySelectorAll('[data-slider]').forEach((slider) => {
        let list = [];
        try { list = JSON.parse(slider.getAttribute('data-images') || '[]'); } catch (e) { list = []; }
        const img = slider.querySelector('[data-slider-image]');
        if (!Array.isArray(list) || !list.length) {
          list = img && img.src ? [img.src] : [];
        }
        let i = 0;
        const count = slider.querySelector('[data-slider-count]');
        const show = () => {
          if (img && list[i]) img.src = list[i];
          if (count) count.textContent = (i + 1) + ' / ' + list.length;
        };
        slider.querySelector('[data-slider-prev]')?.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (list.length < 2) return;
          i = (i - 1 + list.length) % list.length;
          show();
        });
        slider.querySelector('[data-slider-next]')?.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (list.length < 2) return;
          i = (i + 1) % list.length;
          show();
        });
        img?.addEventListener('click', () => openPreview(list, i));
      });

      prevBtn.addEventListener('click', (e) => { e.stopPropagation(); step(-1); });
      nextBtn.addEventListener('click', (e) => { e.stopPropagation(); step(1); });
      document.getElementById('imgPreviewClose').addEventListener('click', closePreview);
      overlay.addEventListener('click', (e) => { if (e.target === overlay) closePreview(); });
      document.addEventListener('keydown', (e) => {
        if (!overlay.classList.contains('open')) return;
        if (e.key === 'Escape') closePreview();
        if (e.key === 'ArrowLeft') step(-1);
        if (e.key === 'ArrowRight') step(1);
      });
    })();
  </script>
</body>
</html>
