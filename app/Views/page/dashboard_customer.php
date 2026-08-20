<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .dash-title {
        margin: 0 0 16px;
        color: #111827;
        font-size: 28px;
        font-weight: 800;
    }

    .rumah-panel,
    .dash-panel {
        margin-bottom: 18px;
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

    .rumah-split {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .galeri-main {
        position: relative;
        overflow: visible;
        border-radius: var(--radius, 8px);
        background: #eef2f7;
        min-height: 240px;
    }

    .galeri-main img {
        width: 100%;
        height: 280px;
        object-fit: cover;
        display: block;
        border-radius: var(--radius, 8px);
        cursor: zoom-in;
    }

    .img-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        padding: 0;
        margin: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.62);
        color: #fff;
        font-size: 22px;
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

    .galeri-main .no-image {
        height: 280px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--muted, #647084);
        font-weight: 700;
    }

    .galeri-main .no-image .material-icons { font-size: 56px; color: #94a3b8; }

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

    .berkas-note {
        margin: 0 0 14px;
        color: var(--muted, #647084);
        font-size: 13px;
        font-weight: 600;
    }

    .berkas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }

    .berkas-card {
        min-height: 148px;
        padding: 14px 12px;
        border: 1px solid var(--line, #e4e8ef);
        border-radius: var(--radius, 8px);
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        text-align: center;
        gap: 10px;
    }

    .berkas-name {
        color: var(--text, #172033);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.35;
    }

    .berkas-card .status-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 18px;
        color: #fff;
        margin-bottom: 8px;
    }

    .berkas-card .status-icon.ok { background: var(--success, #059669); }
    .berkas-card .status-icon.no { background: var(--danger, #dc2626); }
    .berkas-card .status-icon.wait { background: var(--warning, #d97706); }

    .berkas-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
    }

    .berkas-card .lihat-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 6px 14px;
        border: 1px solid var(--primary, #2563eb);
        border-radius: var(--radius, 8px);
        background: #fff;
        color: var(--primary, #2563eb);
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .berkas-card .lihat-btn:hover {
        background: #eff6ff;
        color: var(--primary-dark, #1d4ed8);
    }

    .berkas-card .upload-btn,
    .cicilan-btn {
        min-height: 34px;
        padding: 6px 14px;
        border: 0;
        border-radius: var(--radius, 8px);
        background: var(--primary, #2563eb);
        color: #fff;
        font-weight: 700;
        cursor: pointer;
    }

    .berkas-card .upload-btn:hover,
    .cicilan-btn:hover { background: var(--primary-dark, #1d4ed8); }

    .cicilan-summary {
        margin-bottom: 8px;
    }

    .timeline-wrap {
        margin-top: 8px;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 20px 12px 120px;
    }

    .timeline {
        position: relative;
        display: flex;
        align-items: flex-start;
        min-width: max-content;
        gap: 28px;
        padding: 8px 8px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        right: 8px;
        top: 46px;
        border-top: 2px dashed var(--line, #e4e8ef);
    }

    .tl-node {
        position: relative;
        z-index: 1;
        width: 72px;
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
    }

    .tl-icon {
        width: 28px;
        height: 28px;
        margin-bottom: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #94a3b8;
        color: #fff;
        font-size: 16px;
    }

    .tl-icon.ok { background: var(--success, #059669); }
    .tl-icon.wait { background: var(--warning, #d97706); }
    .tl-icon.q { background: #94a3b8; }

    .tl-pill {
        min-width: 42px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 13px;
        font-weight: 800;
        text-align: center;
    }

    .tl-pill.dp { background: #e2e8f0; color: #334155; }
    .tl-node.current .tl-pill { background: var(--primary, #2563eb); color: #fff; transform: scale(1.08); }

    .tl-tip {
        position: absolute;
        top: 86px;
        left: 50%;
        transform: translateX(-50%);
        width: 210px;
        padding: 10px 12px;
        border-radius: var(--radius, 8px);
        background: #fff;
        border: 1px solid var(--line, #e4e8ef);
        color: var(--text, #172033);
        font-size: 12px;
        font-weight: 600;
        line-height: 1.4;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        display: none;
    }

    .tl-node.current .tl-tip { display: block; }

    .tl-node:first-child .tl-tip {
        left: 0;
        transform: none;
    }

    .tl-node:last-child .tl-tip {
        left: auto;
        right: 0;
        transform: none;
    }

    .tl-tip::before {
        content: '';
        position: absolute;
        top: -7px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 8px solid #fff;
        filter: drop-shadow(0 -1px 0 #e4e8ef);
    }

    .tl-node:first-child .tl-tip::before {
        left: 36px;
        transform: none;
    }

    .tl-node:last-child .tl-tip::before {
        left: auto;
        right: 36px;
        transform: none;
    }

    .cicilan-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .cicilan-btn.primary {
        min-height: 42px;
        padding: 10px 16px;
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.18);
    }

    .cicilan-btn.primary:disabled {
        opacity: 0.65;
        box-shadow: none;
        cursor: not-allowed;
    }

    .status-danger { background: #fee2e2; color: #b91c1c; }

    .rumah-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 18px;
    }

    .rumah-card {
        overflow: hidden;
        background: #fff;
        border: 1px solid #e4e8ef;
        border-radius: 12px;
    }

    .rumah-card-image { position: relative; height: 160px; background: #eef2f7; }
    .rumah-card-image img { width: 100%; height: 100%; object-fit: cover; display: block; cursor: zoom-in; }
    .rumah-card-image .no-image {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 40px;
    }
    .rumah-card-body { padding: 16px; }
    .rumah-card-title { margin: 0 0 6px; font-size: 16px; font-weight: 800; }
    .rumah-card-meta { margin: 0 0 12px; color: #647084; font-size: 13px; }
    .rumah-card-price { margin-bottom: 14px; color: #059669; font-size: 16px; font-weight: 800; }
    .code-pill, .status-pill {
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
    .rumah-card-top { display: flex; justify-content: space-between; gap: 8px; margin-bottom: 10px; }

    @media (max-width: 768px) {
        .rumah-split { grid-template-columns: 1fr; }
        .spec-grid { grid-template-columns: 1fr; }
        .cicilan-actions { justify-content: stretch; }
        .cicilan-btn.primary { width: 100%; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $pembelian = is_array($pembelian ?? null) ? $pembelian : null;
    $rumah = is_array($rumah ?? null) ? $rumah : [];
    $rekomendasi = is_array($rekomendasi ?? null) ? $rekomendasi : [];
    $ringkasan = is_array($ringkasan ?? null) ? $ringkasan : [];
    $progressNodes = is_array($progressNodes ?? null) ? $progressNodes : [];
    $infoBerkas = is_array($infoBerkas ?? null) ? $infoBerkas : [];
    $berkasItems = is_array($berkasItems ?? null) ? $berkasItems : [];
    $error = session()->getFlashdata('error');
    $success = session()->getFlashdata('success');
    $activeNode = null;
    foreach ($progressNodes as $node) {
        if (!empty($node['is_current'])) {
            $activeNode = $node;
            break;
        }
    }
?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>

<?php if (!$pembelian): ?>
    <h2 class="dash-title">Rumah yang mungkin cocok untuk Anda</h2>
    <?php if (empty($rekomendasi)): ?>
        <p style="color:#647084;">Belum ada rumah yang bisa ditampilkan.</p>
    <?php else: ?>
        <div class="rumah-grid">
            <?php foreach ($rekomendasi as $item): ?>
                <?php
                    $status = strtolower((string) ($item['status'] ?? ''));
                    $statusClass = 'status-secondary';
                    if (in_array($status, ['dijual', 'tersedia'], true)) $statusClass = 'status-primary';
                    elseif (in_array($status, ['terjual', 'lunas'], true)) $statusClass = 'status-success';
                    elseif (in_array($status, ['proses pembangunan', 'booking', 'booked'], true)) $statusClass = 'status-warning';
                    $gambarList = \App\Models\PerumahanModel::gambarUrls($item['gambar'] ?? null);
                ?>
                <div class="rumah-card">
                    <div class="rumah-card-image<?= count($gambarList) > 1 ? ' has-many' : '' ?>" data-slider data-images='<?= esc(json_encode($gambarList, JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_AMP), 'attr') ?>'>
                        <?php if ($gambarList !== []): ?>
                            <img data-slider-image src="<?= esc($gambarList[0]) ?>" alt="<?= esc($item['kode_rumah'] ?? 'Rumah') ?>">
                            <?php if (count($gambarList) > 1): ?>
                                <button type="button" class="img-slider-btn prev" data-slider-prev aria-label="Sebelumnya">&lsaquo;</button>
                                <button type="button" class="img-slider-btn next" data-slider-next aria-label="Berikutnya">&rsaquo;</button>
                                <span class="img-slider-count" data-slider-count>1 / <?= count($gambarList) ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="no-image"><span class="material-icons">home_work</span></div>
                        <?php endif; ?>
                    </div>
                    <div class="rumah-card-body">
                        <div class="rumah-card-top">
                            <span class="code-pill"><?= esc($item['kode_rumah'] ?? '-') ?></span>
                            <span class="status-pill <?= $statusClass ?>"><?= esc($item['status'] ?? '-') ?></span>
                        </div>
                        <h3 class="rumah-card-title">Tipe <?= esc($item['tipe'] ?? '-') ?></h3>
                        <p class="rumah-card-meta"><?= esc($item['lokasi'] ?? '-') ?></p>
                        <div class="rumah-card-price">Rp <?= number_format((float) ($item['harga'] ?? 0), 0, ',', '.') ?></div>
                        <a href="/perumahan/data-rumah/<?= (int) ($item['id'] ?? 0) ?>" class="btn btn-primary btn-sm w-100">Lihat Detail</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <?php
        $gambarList = \App\Models\PerumahanModel::gambarUrls($rumah['gambar'] ?? null);
        if ($gambarList === []) {
            $gambarList = ['https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1600&q=85'];
        }
        $gambarCount = count($gambarList);
        $gambarUrl = $gambarList[0];
        $dokumen = trim((string) ($rumah['dokumen'] ?? ''));
        $statusPembelian = (string) ($rumah['status_pembelian'] ?? '-');
        $statusPembelianKey = strtolower($statusPembelian);
        $statusPembelianClass = 'status-secondary';
        if (in_array($statusPembelianKey, ['lunas', 'selesai', 'disetujui'], true)) $statusPembelianClass = 'status-success';
        elseif (in_array($statusPembelianKey, ['booking', 'booked', 'dp', 'cicilan', 'pending', 'menunggu'], true)) $statusPembelianClass = 'status-warning';
        elseif (in_array($statusPembelianKey, ['batal', 'ditolak'], true)) $statusPembelianClass = 'status-secondary';
        $hargaTampil = (float) ($rumah['harga_beli'] ?? $rumah['harga'] ?? 0);
        $hargaLabel = 'Rp ' . number_format($hargaTampil, 0, ',', '.');
        $metode = trim((string) ($rumah['metode_pembayaran'] ?? ''));
    ?>
    <div class="rumah-panel">
        <div class="rumah-panel-head">
            <div class="rumah-panel-title">
                <span class="material-icons">home_work</span>
                <h2>Detail Rumah Pesanan Anda</h2>
            </div>
            <div class="rumah-panel-pills">
                <span class="code-pill"><?= esc($rumah['kode_rumah'] ?? '-') ?></span>
                <span class="status-pill <?= $statusPembelianClass ?>"><?= esc($statusPembelian) ?></span>
            </div>
        </div>
        <div class="rumah-split">
            <div class="galeri-main" data-slider data-images='<?= esc(json_encode($gambarList, JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_AMP), 'attr') ?>'>
                <img data-slider-image src="<?= esc($gambarUrl) ?>" alt="<?= esc($rumah['kode_rumah'] ?? 'Rumah') ?>">
                <?php if ($gambarCount > 1): ?>
                    <button type="button" class="img-slider-btn prev" data-slider-prev aria-label="Sebelumnya">&lsaquo;</button>
                    <button type="button" class="img-slider-btn next" data-slider-next aria-label="Berikutnya">&rsaquo;</button>
                    <span class="img-slider-count" data-slider-count>1 / <?= (int) $gambarCount ?></span>
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
                    <span>Metode Pembayaran</span>
                    <strong><?= esc($metode !== '' ? $metode : '-') ?></strong>
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
                        <strong><a class="spec-link" href="/<?= esc($dokumen) ?>" target="_blank" rel="noopener">Lihat dokumen</a></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="dash-panel">
        <div class="dash-head">
            <div class="dash-head-title">
                <span class="material-icons">folder_open</span>
                <h2>Pemberkasan</h2>
            </div>
            <div class="rumah-panel-pills">
                <span class="status-pill status-warning">
                    Sebelum <?= esc($infoBerkas['deadline_long'] ?? '-') ?>
                    <?php if (empty($infoBerkas['kedaluwarsa'])): ?>
                        · <?= esc($infoBerkas['sisa_display'] ?? '-') ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <p class="berkas-note">Silakan unggah dokumen yang diperlukan berikut.</p>
        <div class="berkas-grid">
            <?php foreach ($berkasItems as $item): ?>
                <?php
                    $status = (string) ($item['status'] ?? '');
                    $bisaUnggah = empty($item['file']) || $status === 'ditolak';
                    if (!empty($infoBerkas['kedaluwarsa']) && $status !== 'ditolak' && empty($item['file'])) {
                        $bisaUnggah = false;
                    }
                    $statusClass = 'status-secondary';
                    $statusLabel = 'Belum diunggah';
                    if ($status === 'disetujui') { $statusClass = 'status-success'; $statusLabel = 'Disetujui'; }
                    elseif ($status === 'ditolak') { $statusClass = 'status-danger'; $statusLabel = 'Ditolak'; }
                    elseif ($status === 'pending') { $statusClass = 'status-warning'; $statusLabel = 'Menunggu'; }
                    $namaBerkas = match ((string) ($item['key'] ?? '')) {
                        'ktp' => 'KTP',
                        'kk' => 'KK',
                        'npwp' => 'NPWP',
                        default => ucwords((string) ($item['short'] ?? $item['key'] ?? '-')),
                    };
                ?>
                <div class="berkas-card">
                    <div>
                        <?php if ($status === 'disetujui'): ?>
                            <span class="status-icon ok material-icons">check</span>
                        <?php elseif ($status === 'ditolak'): ?>
                            <span class="status-icon no material-icons">close</span>
                        <?php elseif ($status === 'pending'): ?>
                            <span class="status-icon wait material-icons">schedule</span>
                        <?php else: ?>
                            <span class="status-icon material-icons" style="background:#94a3b8;">upload_file</span>
                        <?php endif; ?>
                        <div class="berkas-name"><?= esc($namaBerkas) ?></div>
                        <span class="status-pill <?= $statusClass ?>" style="margin-top:8px;"><?= esc($statusLabel) ?></span>
                    </div>
                    <?php if (!empty($item['file']) || $bisaUnggah): ?>
                        <div class="berkas-actions">
                            <?php if (!empty($item['file'])): ?>
                                <button
                                    type="button"
                                    class="lihat-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalLihatBerkas"
                                    data-title="<?= esc($namaBerkas) ?>"
                                    data-label="<?= esc($item['label'] ?? $namaBerkas) ?>"
                                    data-status="<?= esc($statusLabel) ?>"
                                    data-file="/<?= esc(ltrim((string) $item['file'], '/')) ?>"
                                >Lihat</button>
                            <?php endif; ?>
                            <?php if ($bisaUnggah): ?>
                                <form method="post" action="/perumahan/rumah-booking/<?= (int) $pembelian['id'] ?>/berkas" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <input type="file" name="<?= esc($item['key']) ?>" accept=".jpg,.jpeg,.png,.pdf" hidden onchange="this.form.submit()">
                                    <button type="button" class="upload-btn" onclick="this.previousElementSibling.click()">
                                        <?= $status === 'ditolak' ? 'Perbarui' : 'Upload' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="dash-panel">
        <div class="dash-head">
            <div class="dash-head-title">
                <span class="material-icons">payments</span>
                <h2>Progress Cicilan</h2>
            </div>
        </div>
        <div class="spec-grid cicilan-summary">
            <div class="spec-box">
                <span>Rumah</span>
                <strong><?= esc($rumah['kode_rumah'] ?? '-') ?></strong>
            </div>
            <div class="spec-box">
                <span>Total</span>
                <strong class="spec-price">Rp <?= number_format((float) ($ringkasan['harga_beli'] ?? 0), 0, ',', '.') ?></strong>
            </div>
            <div class="spec-box">
                <span>Durasi</span>
                <strong><?= esc($ringkasan['durasi_text'] ?? '-') ?></strong>
            </div>
            <div class="spec-box">
                <span>Cicilan / bulan</span>
                <strong>Rp <?= number_format((float) ($ringkasan['jumlah_cicilan'] ?? 0), 0, ',', '.') ?></strong>
            </div>
            <div class="spec-box full">
                <span>Periode</span>
                <strong><?= esc($ringkasan['tanggal_mulai_display'] ?? '-') ?> - <?= esc($ringkasan['tanggal_selesai_display'] ?? '-') ?></strong>
            </div>
        </div>

        <?php if (!empty($progressNodes)): ?>
            <div class="timeline-wrap">
                <div class="timeline">
                    <?php foreach ($progressNodes as $node): ?>
                        <?php
                            $status = (string) ($node['status'] ?? '');
                            $iconClass = 'q';
                            $icon = 'help';
                            if ($status === 'disetujui') { $iconClass = 'ok'; $icon = 'check'; }
                            elseif ($status === 'pending') { $iconClass = 'wait'; $icon = 'schedule'; }
                            $payment = is_array($node['payment'] ?? null) ? $node['payment'] : [];
                            $uploadAt = $payment['created_at'] ?? $payment['tanggal_bayar'] ?? null;
                            $uploadLabel = $uploadAt ? date('j M Y', strtotime((string) $uploadAt)) : '';
                            if (!empty($node['is_dp'])) {
                                if ($status === 'disetujui') $tip = 'DP: di upload ' . $uploadLabel . ' (terverifikasi)';
                                elseif ($status === 'pending') $tip = 'DP: diupload ' . $uploadLabel . ' (Menunggu)';
                                else $tip = 'DP: belum upload bukti';
                            } else {
                                $ke = (int) $node['cicilan_ke'];
                                if ($status === 'disetujui') $tip = 'Cicilan ke-' . $ke . ': di upload ' . $uploadLabel . ' (terverifikasi)';
                                elseif ($status === 'pending') $tip = 'Cicilan ke-' . $ke . ': diupload ' . $uploadLabel . ' (Menunggu)';
                                else $tip = 'Cicilan ke-' . $ke . ': belum upload bukti';
                            }
                            $tip .= "\nBulan " . ($node['bulan_label'] ?? '-');
                        ?>
                        <div class="tl-node<?= !empty($node['is_current']) ? ' current' : '' ?>"
                             data-status="<?= esc($status) ?>"
                             data-payment-id="<?= (int) ($payment['id'] ?? 0) ?>"
                             onclick="pilihNode(this)">
                            <span class="tl-icon <?= $iconClass ?> material-icons"><?= $icon ?></span>
                            <span class="tl-pill<?= !empty($node['is_dp']) ? ' dp' : '' ?>"><?= esc($node['label']) ?></span>
                            <div class="tl-tip"><?= nl2br(esc($tip)) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="cicilan-actions">
            <?php
                $activeStatus = (string) ($activeNode['status'] ?? '');
                $activePaymentId = (int) (($activeNode['payment']['id'] ?? 0));
                $bisaUploadBaru = !empty($ringkasan['bisa_upload']);
                $bisaPerbarui = in_array($activeStatus, ['pending', 'ditolak'], true) && $activePaymentId > 0;
            ?>
            <?php if ((int) ($ringkasan['sisa_bayar'] ?? 0) <= 0): ?>
                <button type="button" class="cicilan-btn primary" disabled>Pembayaran sudah lunas</button>
            <?php elseif ($bisaPerbarui): ?>
                <button type="button" class="cicilan-btn primary" data-bs-toggle="modal" data-bs-target="#modalUploadCicilan" data-mode="update" data-payment-id="<?= $activePaymentId ?>">Perbarui Bukti</button>
            <?php elseif ($bisaUploadBaru): ?>
                <button type="button" class="cicilan-btn primary" data-bs-toggle="modal" data-bs-target="#modalUploadCicilan" data-mode="create" data-payment-id="0">Upload Bukti</button>
            <?php else: ?>
                <button type="button" class="cicilan-btn primary" disabled>Bukti bulan ini sudah diunggah</button>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
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
<?= $this->endSection() ?>

<?php if ($pembelian): ?>
<?= $this->section('modals') ?>
<div class="modal fade" id="modalLihatBerkas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lihatBerkasTitle">Detail berkas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="spec-grid" style="margin-bottom:14px;">
                    <div class="spec-box">
                        <span>Dokumen</span>
                        <strong id="lihatBerkasLabel">-</strong>
                    </div>
                    <div class="spec-box">
                        <span>Status</span>
                        <strong id="lihatBerkasStatus">-</strong>
                    </div>
                </div>
                <div id="lihatBerkasPreview"></div>
            </div>
            <div class="modal-footer">
                <a id="lihatBerkasLink" href="#" target="_blank" rel="noopener" class="btn btn-primary">Buka file</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalUploadCicilan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formUploadCicilan" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Upload bukti cicilan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="pembelian_rumah_id" value="<?= (int) ($pembelian['id'] ?? 0) ?>">
                    <input type="hidden" name="payment_id" id="cicilanPaymentId" value="0">
                    <div class="mb-3 create-only">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="number" class="form-control" name="jumlah_bayar" value="<?= (int) ($ringkasan['jumlah_cicilan'] ?? 0) ?>" min="1">
                    </div>
                    <div class="mb-3 create-only">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">Format: JPG, PNG, atau PDF. Maksimal 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function pilihNode(el) {
        document.querySelectorAll('.tl-node').forEach((node) => node.classList.remove('current'));
        el.classList.add('current');
        const btn = document.querySelector('.cicilan-actions .cicilan-btn.primary:not(:disabled)');
        if (!btn) return;
        const status = el.getAttribute('data-status') || '';
        const paymentId = el.getAttribute('data-payment-id') || '0';
        if (['pending', 'ditolak'].includes(status) && paymentId !== '0') {
            btn.textContent = 'Perbarui Bukti';
            btn.dataset.mode = 'update';
            btn.dataset.paymentId = paymentId;
        } else if (!status) {
            btn.textContent = 'Upload Bukti';
            btn.dataset.mode = 'create';
            btn.dataset.paymentId = '0';
        }
    }

    const modalBerkas = document.getElementById('modalLihatBerkas');
    if (modalBerkas) {
        modalBerkas.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const title = trigger?.dataset?.title || 'Detail berkas';
            const label = trigger?.dataset?.label || '-';
            const status = trigger?.dataset?.status || '-';
            const file = trigger?.dataset?.file || '#';
            document.getElementById('lihatBerkasTitle').textContent = title;
            document.getElementById('lihatBerkasLabel').textContent = label;
            document.getElementById('lihatBerkasStatus').textContent = status;
            document.getElementById('lihatBerkasLink').href = file;
            const preview = document.getElementById('lihatBerkasPreview');
            preview.innerHTML = '';
            const ext = (file.split('.').pop() || '').toLowerCase().split('?')[0];
            if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
                const img = document.createElement('img');
                img.src = file;
                img.alt = title;
                img.style.cssText = 'width:100%;max-height:420px;object-fit:contain;border:1px solid #e4e8ef;border-radius:8px;background:#f8fafc;';
                preview.appendChild(img);
            } else if (ext === 'pdf') {
                const frame = document.createElement('iframe');
                frame.src = file;
                frame.style.cssText = 'width:100%;height:420px;border:1px solid #e4e8ef;border-radius:8px;';
                preview.appendChild(frame);
            } else {
                preview.textContent = 'Pratinjau tidak tersedia. Gunakan tombol Buka file.';
            }
        });
    }

    const modal = document.getElementById('modalUploadCicilan');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const mode = trigger?.dataset?.mode || 'create';
            const paymentId = trigger?.dataset?.paymentId || '0';
            document.getElementById('cicilanPaymentId').value = paymentId;
            modal.querySelectorAll('.create-only').forEach((el) => {
                el.style.display = mode === 'update' ? 'none' : '';
            });
            modal.querySelector('.modal-title').textContent = mode === 'update' ? 'Perbarui bukti cicilan' : 'Upload bukti cicilan';
        });
    }

    $('#formUploadCicilan').on('submit', function (event) {
        event.preventDefault();
        const form = this;
        const bukti = form.querySelector('input[name=bukti_bayar]');
        if (!bukti.files.length) {
            alert('Bukti pembayaran wajib diunggah');
            return;
        }
        const file = bukti.files[0];
        const ext = (file.name.split('.').pop() || '').toLowerCase();
        if (!['jpg', 'jpeg', 'png', 'pdf'].includes(ext)) {
            alert('Bukti pembayaran harus berupa JPG, PNG, atau PDF');
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran bukti pembayaran maksimal 2 MB');
            return;
        }

        const paymentId = form.querySelector('#cicilanPaymentId').value;
        const url = paymentId && paymentId !== '0'
            ? `/pembayaran-rumah/unggah-ulang/${paymentId}`
            : '/pembayaran-rumah/store';
        const submitBtn = form.querySelector('button[type=submit]');
        submitBtn.disabled = true;

        $.ajax({
            url: url,
            method: 'POST',
            data: new FormData(form),
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status === 'success') {
                    window.location.reload();
                    return;
                }
                submitBtn.disabled = false;
                alert(res.message || 'Gagal mengunggah bukti cicilan');
            },
            error: function (xhr) {
                submitBtn.disabled = false;
                alert((xhr.responseJSON && xhr.responseJSON.message) || 'Gagal mengunggah bukti cicilan');
            }
        });
    });
</script>
<?= $this->endSection() ?>
<?php endif; ?>
