<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .rumah-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr));
        gap: 18px;
        width: 100%;
        min-width: 0;
    }

    .rumah-card {
        min-width: 0;
        overflow: hidden;
        background: var(--surface, #ffffff);
        border: 1px solid var(--line, #e4e8ef);
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
    }

    .rumah-card-image,
    .card-gallery {
        position: relative;
        width: 100%;
        height: 190px;
        background: #eef2f7;
        overflow: hidden;
    }

    .card-gallery img,
    .rumah-card-image img {
        width: 100%;
        height: 190px;
        object-fit: cover;
        display: block;
        background: #eef2f7;
        cursor: zoom-in;
    }

    .rumah-card-image .no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
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
        z-index: 2000;
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
        line-height: 1;
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

    .rumah-card-body {
        padding: 16px;
    }

    .rumah-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 10px;
    }

    .code-pill {
        display: inline-flex;
        align-items: center;
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

    .rumah-card-title {
        margin: 0 0 6px;
        color: #172033;
        font-size: 16px;
        font-weight: 800;
    }

    .rumah-card-meta {
        margin: 0 0 12px;
        color: #647084;
        font-size: 13px;
    }

    .rumah-card-price {
        margin-bottom: 14px;
        color: #059669;
        font-size: 16px;
        font-weight: 800;
    }

    .empty-page-card {
        background: var(--surface, #ffffff);
        border: 1px solid var(--line, #e4e8ef);
        border-radius: var(--radius, 8px);
        padding: 28px 24px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        color: var(--muted, #647084);
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $rumah = is_array($rumah ?? null) ? $rumah : [];
?>

<?php if (empty($rumah)): ?>
    <div class="empty-page-card">Belum ada data rumah.</div>
<?php else: ?>
    <div class="rumah-grid">
        <?php foreach ($rumah as $item): ?>
            <?php
                $status = strtolower((string) ($item['status'] ?? ''));
                $statusClass = 'status-secondary';
                if (in_array($status, ['dijual', 'tersedia'], true)) $statusClass = 'status-primary';
                elseif (in_array($status, ['terjual', 'lunas'], true)) $statusClass = 'status-success';
                elseif (in_array($status, ['proses pembangunan', 'booking', 'booked'], true)) $statusClass = 'status-warning';
                $gambarList = \App\Models\PerumahanModel::gambarUrls($item['gambar'] ?? null);
            ?>
            <div class="rumah-card">
                <?php if ($gambarList !== []): ?>
                    <div class="card-gallery<?= count($gambarList) > 1 ? ' has-many' : '' ?>" data-slider data-images='<?= esc(json_encode($gambarList, JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_AMP), 'attr') ?>'>
                        <img data-slider-image src="<?= esc($gambarList[0]) ?>" alt="<?= esc($item['kode_rumah'] ?? 'Rumah') ?>">
                        <?php if (count($gambarList) > 1): ?>
                            <button type="button" class="img-slider-btn prev" data-slider-prev aria-label="Sebelumnya">&lsaquo;</button>
                            <button type="button" class="img-slider-btn next" data-slider-next aria-label="Berikutnya">&rsaquo;</button>
                            <span class="img-slider-count" data-slider-count>1 / <?= count($gambarList) ?></span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="rumah-card-image">
                        <div class="no-image"><span class="material-icons">home_work</span></div>
                    </div>
                <?php endif; ?>
                <div class="rumah-card-body">
                    <div class="rumah-card-top">
                        <span class="code-pill"><?= esc($item['kode_rumah'] ?? '-') ?></span>
                        <span class="status-pill <?= $statusClass ?>"><?= esc($item['status'] ?? '-') ?></span>
                    </div>
                    <h3 class="rumah-card-title">Tipe <?= esc($item['tipe'] ?? '-') ?></h3>
                    <p class="rumah-card-meta"><?= esc($item['lokasi'] ?? '-') ?></p>
                    <div class="rumah-card-price">Rp <?= number_format((float) ($item['harga'] ?? 0), 0, ',', '.') ?></div>
                    <a href="/perumahan/data-rumah/<?= (int) $item['id'] ?>" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="img-preview-overlay" id="imgPreviewOverlay" role="dialog" aria-modal="true" aria-label="Preview gambar">
    <button type="button" class="img-preview-close" id="imgPreviewClose" aria-label="Tutup">&times;</button>
    <button type="button" class="img-preview-nav prev" id="imgPreviewPrev" aria-label="Sebelumnya">&lsaquo;</button>
    <img id="imgPreviewPhoto" src="" alt="Preview rumah">
    <button type="button" class="img-preview-nav next" id="imgPreviewNext" aria-label="Berikutnya">&rsaquo;</button>
    <div class="img-preview-count" id="imgPreviewCount"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
<?= $this->endSection() ?>
