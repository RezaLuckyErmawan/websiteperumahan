<?php
$rumah = is_array($rumah ?? null) ? $rumah : [];
$status = strtolower((string) ($rumah['status'] ?? ''));
$statusClass = 'status-secondary';
$statusLabel = 'Info';

if (in_array($status, ['dijual', 'tersedia'], true)) {
    $statusClass = 'status-primary';
    $statusLabel = 'Stok tersedia';
} elseif (in_array($status, ['terjual', 'lunas'], true)) {
    $statusClass = 'status-success';
    $statusLabel = 'Terjual';
} elseif (in_array($status, ['proses pembangunan', 'booking', 'booked'], true)) {
    $statusClass = 'status-warning';
    $statusLabel = 'Booked';
}

$dapatCheckout = (bool) ($dapatCheckout ?? false);
$customer = is_array($customer ?? null) ? $customer : [];
$error = session()->getFlashdata('error');
$success = session()->getFlashdata('success');
$gambarList = \App\Models\PerumahanModel::gambarUrls($rumah['gambar'] ?? null);
$gambarCount = count($gambarList);
$gambarUrl = $gambarList[0] ?? '';
$dokumen = trim((string) ($rumah['dokumen'] ?? ''));
$hargaLabel = 'Rp ' . number_format((float) ($rumah['harga'] ?? 0), 0, ',', '.');
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .detail-toolbar {
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .breadcrumb-like {
        color: var(--muted, #647084);
        font-size: 13px;
        font-weight: 700;
    }

    .detail-shell {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(280px, 0.7fr);
        gap: 18px;
        align-items: start;
    }

    .rumah-panel,
    .dash-panel {
        margin-bottom: 0;
        padding: 18px;
        border: 1px solid var(--line, #e4e8ef);
        border-radius: var(--radius, 8px);
        background: var(--surface, #ffffff);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .rumah-panel-head,
    .dash-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .rumah-panel-title,
    .dash-head-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rumah-panel-title .material-icons,
    .dash-head-title .material-icons {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius, 8px);
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 20px;
    }

    .rumah-panel h2,
    .dash-panel h2 {
        margin: 0;
        color: var(--text, #172033);
        font-size: 16px;
        font-weight: 800;
    }

    .rumah-panel-pills { display: flex; flex-wrap: wrap; gap: 8px; }

    .code-pill,
    .status-pill {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
    }

    .code-pill { background: #e2e8f0; color: #334155; }
    .status-primary { background: #dbeafe; color: #1d4ed8; }
    .status-success { background: #d1fae5; color: #047857; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-secondary { background: #e2e8f0; color: #334155; }

    .galeri-main {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 16px;
        width: 100%;
        height: 360px;
        border-radius: var(--radius, 8px);
        background: #eef2f7;
    }

    .galeri-main img {
        width: auto;
        max-width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        cursor: zoom-in;
    }

    .galeri-main .no-image {
        height: 360px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--muted, #647084);
        font-weight: 700;
    }

    .galeri-main .no-image .material-icons { font-size: 56px; color: #94a3b8; }

    .img-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        padding: 0;
        margin: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.62);
        color: #fff;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        z-index: 3;
        appearance: none;
    }
    .img-slider-btn.prev { left: 16px; }
    .img-slider-btn.next { right: 16px; }

    .img-slider-count {
        position: absolute;
        right: 12px;
        bottom: 12px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.6);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
    }

    .spec-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .spec-box {
        min-height: 66px;
        padding: 13px 14px;
        border: 1px solid var(--line, #e4e8ef);
        border-radius: var(--radius, 8px);
        background: var(--surface, #ffffff);
    }

    .spec-box.full { grid-column: 1 / -1; }

    .spec-box span {
        display: block;
        margin-bottom: 5px;
        color: var(--muted, #647084);
        font-size: 12px;
        font-weight: 800;
    }

    .spec-box strong {
        color: var(--text, #172033);
        font-size: 14px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .spec-price { color: var(--success, #059669) !important; font-size: 16px !important; font-weight: 800 !important; }

    .spec-box p {
        margin: 0;
        color: var(--text, #172033);
        font-size: 14px;
        font-weight: 700;
        line-height: 1.55;
        white-space: pre-wrap;
    }

    .spec-link {
        color: var(--primary, #2563eb);
        font-weight: 700;
        text-decoration: none;
    }

    .spec-link:hover { text-decoration: underline; }

    .booking-price {
        margin: 0 0 16px;
        color: var(--success, #059669);
        font-size: 22px;
        font-weight: 800;
    }

    .booking-note {
        margin: 10px 0 0;
        color: var(--muted, #647084);
        font-size: 12px;
        font-weight: 600;
        line-height: 1.55;
    }

    .booking-actions {
        display: grid;
        gap: 8px;
        margin-top: 16px;
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

    @media (max-width: 1024px) {
        .detail-shell { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .spec-grid { grid-template-columns: 1fr; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>

<div class="detail-toolbar">
    <a href="/perumahan/data-rumah" class="btn btn-secondary btn-sm">
        <span class="material-icons" style="font-size:16px; vertical-align:middle;">arrow_back</span>
        Kembali ke katalog
    </a>
    <div class="breadcrumb-like">Beranda / Katalog Rumah / Detail Rumah</div>
</div>

<div class="detail-shell">
    <div class="rumah-panel">
        <div class="rumah-panel-head">
            <div class="rumah-panel-title">
                <span class="material-icons">home</span>
                <h2>Tipe <?= esc($rumah['tipe'] ?? '-') ?></h2>
            </div>
            <div class="rumah-panel-pills">
                <span class="code-pill"><?= esc($rumah['kode_rumah'] ?? '-') ?></span>
                <span class="status-pill <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
            </div>
        </div>

        <div class="galeri-main" data-slider data-images='<?= esc(json_encode($gambarList, JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_AMP), 'attr') ?>'>
            <?php if ($gambarUrl !== ''): ?>
                <img data-slider-image src="<?= esc($gambarUrl) ?>" alt="<?= esc($rumah['kode_rumah'] ?? 'Rumah') ?>">
                <?php if ($gambarCount > 1): ?>
                    <button type="button" class="img-slider-btn prev" data-slider-prev aria-label="Sebelumnya">&lsaquo;</button>
                    <button type="button" class="img-slider-btn next" data-slider-next aria-label="Berikutnya">&rsaquo;</button>
                    <span class="img-slider-count" data-slider-count>1 / <?= (int) $gambarCount ?></span>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-image">
                    <span class="material-icons">home_work</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="spec-grid">
            <div class="spec-box">
                <span>Kode Rumah</span>
                <strong><?= esc($rumah['kode_rumah'] ?? '-') ?></strong>
            </div>
            <div class="spec-box">
                <span>Tipe</span>
                <strong><?= esc($rumah['tipe'] ?? '-') ?></strong>
            </div>
            <div class="spec-box">
                <span>Luas Tanah</span>
                <strong><?= esc($rumah['luas_tanah'] ?? '-') ?> m²</strong>
            </div>
            <div class="spec-box">
                <span>Luas Bangunan</span>
                <strong><?= esc($rumah['luas_bangunan'] ?? '-') ?> m²</strong>
            </div>
            <div class="spec-box">
                <span>Harga</span>
                <strong class="spec-price"><?= esc($hargaLabel) ?></strong>
            </div>
            <div class="spec-box">
                <span>Status</span>
                <strong><?= esc($statusLabel) ?></strong>
            </div>
            <div class="spec-box full">
                <span>Lokasi</span>
                <strong><?= esc($rumah['lokasi'] ?? '-') ?></strong>
            </div>
            <div class="spec-box full">
                <span>Deskripsi</span>
                <p><?= !empty($rumah['deskripsi']) ? esc($rumah['deskripsi']) : '-' ?></p>
            </div>
            <?php if ($dokumen !== ''): ?>
                <div class="spec-box full">
                    <span>Dokumen Rumah</span>
                    <strong><a class="spec-link" href="/<?= esc(ltrim($dokumen, '/')) ?>" target="_blank" rel="noopener">Lihat dokumen</a></strong>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <aside class="dash-panel">
        <div class="dash-head">
            <div class="dash-head-title">
                <span class="material-icons">event_available</span>
                <h2>Booking Sekarang</h2>
            </div>
        </div>
        <div class="booking-price"><?= esc($hargaLabel) ?></div>
        <div class="spec-grid">
            <div class="spec-box">
                <span>Kode Rumah</span>
                <strong><?= esc($rumah['kode_rumah'] ?? '-') ?></strong>
            </div>
            <div class="spec-box">
                <span>Status</span>
                <strong><?= esc($statusLabel) ?></strong>
            </div>
            <div class="spec-box">
                <span>Luas Tanah</span>
                <strong><?= esc($rumah['luas_tanah'] ?? '-') ?> m²</strong>
            </div>
            <div class="spec-box">
                <span>Luas Bangunan</span>
                <strong><?= esc($rumah['luas_bangunan'] ?? '-') ?> m²</strong>
            </div>
            <div class="spec-box full">
                <span>Lokasi</span>
                <strong><?= esc($rumah['lokasi'] ?? '-') ?></strong>
            </div>
        </div>
        <div class="booking-actions">
            <?php if ($dapatCheckout): ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCheckout">
                    Booking Sekarang
                </button>
                <p class="booking-note">Rumah ini masih tersedia. Isi data customer dan pilih lama cicilan untuk melanjutkan booking.</p>
            <?php else: ?>
                <button type="button" class="btn btn-primary" disabled>Sudah Booked</button>
                <p class="booking-note">Rumah ini sudah tidak tersedia untuk booking.</p>
            <?php endif; ?>
            <a class="btn btn-secondary" href="/perumahan/data-rumah">Lihat Katalog</a>
        </div>
    </aside>
</div>

<div class="img-preview-overlay" id="imgPreviewOverlay" role="dialog" aria-modal="true" aria-label="Preview gambar">
    <button type="button" class="img-preview-close" id="imgPreviewClose" aria-label="Tutup">&times;</button>
    <button type="button" class="img-preview-nav prev" id="imgPreviewPrev" aria-label="Sebelumnya">&lsaquo;</button>
    <img id="imgPreviewPhoto" src="" alt="Preview rumah">
    <button type="button" class="img-preview-nav next" id="imgPreviewNext" aria-label="Berikutnya">&rsaquo;</button>
    <div class="img-preview-count" id="imgPreviewCount"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<div class="modal fade" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="/perumahan/data-rumah/<?= (int) ($rumah['id'] ?? 0) ?>/checkout">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCheckoutLabel">Booking Sekarang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" value="<?= esc(old('nama', $customer['nama'] ?? '')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telp</label>
                        <input type="text" class="form-control" name="telepon" value="<?= esc(old('telepon', $customer['telepon'] ?? '')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= esc(old('email', $customer['email'] ?? '')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3" required><?= esc(old('alamat', $customer['alamat'] ?? '')) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lama Cicilan (Tahun)</label>
                        <input type="number" class="form-control" name="lama_cicilan_tahun" min="1" max="30" value="<?= esc(old('lama_cicilan_tahun', '5')) ?>" required>
                        <small class="text-muted">Cicilan dihitung per bulan. Contoh 5 tahun = 60 kali cicilan.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Cicilan</label>
                        <input type="date" class="form-control" name="tanggal_cicilan" value="<?= esc(old('tanggal_cicilan', date('Y-m-d', strtotime('+1 month')))) ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi Booking</button>
                </div>
            </form>
        </div>
    </div>
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
