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

$gambar = trim((string) ($rumah['gambar'] ?? ''));
$dokumen = trim((string) ($rumah['dokumen'] ?? ''));
$dapatCheckout = (bool) ($dapatCheckout ?? false);
$customer = is_array($customer ?? null) ? $customer : [];
$error = session()->getFlashdata('error');
$success = session()->getFlashdata('success');
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .marketplace-shell {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.78fr);
        gap: 20px;
        align-items: start;
    }

    .product-hero,
    .summary-card,
    .detail-card,
    .description-card {
        background: #ffffff;
        border: 1px solid #e4e8ef;
        border-radius: 24px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .product-hero {
        overflow: hidden;
    }

    .hero-image {
        position: relative;
        min-height: 420px;
        background: linear-gradient(135deg, #dbeafe, #d1fae5);
        overflow: hidden;
    }

    .hero-image img {
        width: 100%;
        height: 100%;
        min-height: 420px;
        object-fit: cover;
        display: block;
    }

    .hero-image .no-image {
        min-height: 420px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(19, 34, 56, 0.42);
    }

    .hero-overlay {
        position: absolute;
        inset: auto 0 0 0;
        padding: 24px;
        background: linear-gradient(180deg, transparent 0%, rgba(5, 15, 30, 0.82) 100%);
        color: #ffffff;
    }

    .hero-overlay h1 {
        margin: 0 0 8px;
        font-size: clamp(28px, 4vw, 42px);
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .hero-overlay p {
        margin: 0;
        max-width: 720px;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.6;
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

    .pill.code {
        background: #e2e8f0;
        color: #334155;
    }

    .status-success { background: #d1fae5; color: #047857; }
    .status-primary { background: #dbeafe; color: #1d4ed8; }
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-secondary { background: #e2e8f0; color: #334155; }

    .section-block {
        padding: 22px;
    }

    .section-title {
        margin: 0 0 14px;
        color: #172033;
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
        border: 1px solid #e4e8ef;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .feature-label {
        margin-bottom: 6px;
        color: #66748a;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .feature-value {
        color: #172033;
        font-size: 15px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .description-copy {
        color: #435166;
        line-height: 1.75;
        font-size: 14px;
    }

    .summary-card {
        position: sticky;
        top: 94px;
        overflow: hidden;
    }

    .summary-header {
        padding: 22px 22px 18px;
        background: linear-gradient(135deg, #0f172a, #174e8a 60%, #0f766e);
        color: #ffffff;
    }

    .summary-header .muted {
        color: rgba(255, 255, 255, 0.8);
        font-size: 13px;
        font-weight: 700;
    }

    .price-box {
        padding: 22px;
        border-bottom: 1px solid #e4e8ef;
    }

    .price-label {
        margin-bottom: 4px;
        color: #66748a;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .price-value {
        color: #059669;
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
        border-bottom: 1px solid #edf1f6;
    }

    .summary-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .summary-key {
        color: #66748a;
        font-size: 13px;
        font-weight: 800;
    }

    .summary-value {
        color: #172033;
        font-size: 13px;
        font-weight: 800;
        text-align: right;
    }

    .cta-panel {
        padding: 0 22px 22px;
        display: grid;
        gap: 10px;
    }

    .cta-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 48px;
        padding: 0 18px;
        border: 0;
        border-radius: 14px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff;
        font-weight: 900;
        text-decoration: none;
        box-shadow: 0 14px 28px rgba(37, 99, 235, 0.18);
    }

    .cta-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 46px;
        padding: 0 18px;
        border-radius: 14px;
        border: 1px solid #d6dfeb;
        background: #f8fbff;
        color: #172033;
        font-weight: 800;
        text-decoration: none;
    }

    .action-note {
        color: #66748a;
        font-size: 12px;
        line-height: 1.6;
    }

    .gallery-mini {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .gallery-mini .mini-card {
        min-height: 86px;
        border-radius: 16px;
        background: linear-gradient(135deg, #eff6ff, #ecfdf5);
        border: 1px solid #e4e8ef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 12px;
        font-weight: 800;
        text-align: center;
        padding: 10px;
    }

    .alert-flash {
        margin-bottom: 16px;
    }

    .detail-toolbar {
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .breadcrumb-like {
        color: #66748a;
        font-size: 13px;
        font-weight: 700;
    }

    @media (max-width: 1024px) {
        .marketplace-shell {
            grid-template-columns: 1fr;
        }

        .summary-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .feature-grid {
            grid-template-columns: 1fr;
        }

        .hero-image,
        .hero-image img {
            min-height: 300px;
        }

        .summary-header,
        .price-box,
        .summary-list,
        .cta-panel,
        .section-block {
            padding-left: 18px;
            padding-right: 18px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $imageSrc = $gambar !== '' ? base_url(ltrim($gambar, '/')) : '';
?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-flash"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success alert-flash"><?= esc($success) ?></div>
<?php endif; ?>

<div class="detail-toolbar">
    <a href="/perumahan/data-rumah" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali ke katalog
    </a>
    <div class="breadcrumb-like">Beranda / Katalog Rumah / Detail Rumah</div>
</div>

<div class="marketplace-shell">
    <div class="product-column">
        <div class="product-hero">
            <div class="hero-image">
                <?php if ($imageSrc !== ''): ?>
                    <img src="<?= esc($imageSrc, 'attr') ?>" alt="<?= esc($rumah['kode_rumah'] ?? 'Rumah') ?>">
                <?php else: ?>
                    <div class="no-image">
                        <span class="material-icons" style="font-size:72px;">home_work</span>
                    </div>
                <?php endif; ?>
                <div class="hero-overlay">
                    <div class="chip-row">
                        <span class="pill code"><?= esc($rumah['kode_rumah'] ?? '-') ?></span>
                        <span class="pill <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
                        <?php if (in_array($status, ['dijual', 'tersedia'], true)): ?>
                            <span class="pill status-success">Ready stock</span>
                        <?php endif; ?>
                    </div>
                    <h1>Tipe <?= esc($rumah['tipe'] ?? '-') ?></h1>
                    <p><?= esc($rumah['lokasi'] ?? '-') ?></p>
                </div>
            </div>
        </div>

        <div class="detail-card section-block" style="margin-top:20px;">
            <div class="section-title">Spesifikasi Rumah</div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-label">Kode Rumah</div>
                    <div class="feature-value"><?= esc($rumah['kode_rumah'] ?? '-') ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-label">Lokasi</div>
                    <div class="feature-value"><?= esc($rumah['lokasi'] ?? '-') ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-label">Tipe</div>
                    <div class="feature-value"><?= esc($rumah['tipe'] ?? '-') ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-label">Status</div>
                    <div class="feature-value"><?= esc($statusLabel) ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-label">Luas Tanah</div>
                    <div class="feature-value"><?= esc($rumah['luas_tanah'] ?? '-') ?> m²</div>
                </div>
                <div class="feature-card">
                    <div class="feature-label">Luas Bangunan</div>
                    <div class="feature-value"><?= esc($rumah['luas_bangunan'] ?? '-') ?> m²</div>
                </div>
                <div class="feature-card">
                    <div class="feature-label">Harga</div>
                    <div class="feature-value">Rp <?= number_format((float) ($rumah['harga'] ?? 0), 0, ',', '.') ?></div>
                </div>
                <div class="feature-card">
                    <div class="feature-label">Tanah</div>
                    <div class="feature-value"><?= esc($rumah['luas_tanah'] ?? '-') ?> m² siap dikembangkan</div>
                </div>
            </div>
        </div>

        <div class="description-card section-block" style="margin-top:20px;">
            <div class="section-title">Deskripsi Detail Perumahan</div>
            <div class="description-copy">
                <?= !empty($rumah['deskripsi']) ? nl2br(esc($rumah['deskripsi'])) : 'Tambahkan deskripsi detail perumahan...' ?>
            </div>
            <div class="gallery-mini">
                <div class="mini-card">Fasilitas lingkungan<br>nyaman</div>
                <div class="mini-card">Akses lokasi<br>strategis</div>
                <div class="mini-card">Cocok untuk<br>keluarga</div>
            </div>
        </div>

        <?php if ($dokumen !== ''): ?>
            <div class="detail-card section-block" style="margin-top:20px;">
                <div class="section-title">Dokumen Perumahan</div>
                <a href="<?= esc(base_url(ltrim($dokumen, '/')), 'attr') ?>" target="_blank" class="cta-secondary" style="max-width:220px;">
                    <i class="fas fa-download"></i> Download Dokumen
                </a>
            </div>
        <?php endif; ?>
    </div>

    <aside class="summary-card">
        <div class="summary-header">
            <div class="muted">Kartu rumah pilihan</div>
            <h2 style="margin:10px 0 0; font-size:26px; letter-spacing:-0.03em;">Booking Sekarang</h2>
            <div style="margin-top:10px; font-size:14px; line-height:1.6; color:rgba(255,255,255,0.88);">
                Dapatkan detail rumah secara cepat, lalu lanjutkan booking jika stok masih tersedia.
            </div>
        </div>

        <div class="price-box">
            <div class="price-label">Harga Rumah</div>
            <div class="price-value">Rp <?= number_format((float) ($rumah['harga'] ?? 0), 0, ',', '.') ?></div>
        </div>

        <div class="summary-list">
            <div class="summary-item">
                <div class="summary-key">Kode Rumah</div>
                <div class="summary-value"><?= esc($rumah['kode_rumah'] ?? '-') ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-key">Lokasi</div>
                <div class="summary-value"><?= esc($rumah['lokasi'] ?? '-') ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-key">Status</div>
                <div class="summary-value">
                    <span class="pill <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-key">Luas Tanah</div>
                <div class="summary-value"><?= esc($rumah['luas_tanah'] ?? '-') ?> m²</div>
            </div>
            <div class="summary-item">
                <div class="summary-key">Luas Bangunan</div>
                <div class="summary-value"><?= esc($rumah['luas_bangunan'] ?? '-') ?> m²</div>
            </div>
        </div>

        <div class="cta-panel">
            <?php if ($dapatCheckout): ?>
                <button type="button" class="cta-primary" data-bs-toggle="modal" data-bs-target="#modalCheckout">
                    <i class="fas fa-bolt"></i> Booking Sekarang
                </button>
                <div class="action-note">
                    Rumah ini masih tersedia. Klik tombol di atas untuk mengisi data customer dan melanjutkan booking.
                </div>
            <?php else: ?>
                <button type="button" class="cta-primary" disabled style="opacity:.7; cursor:not-allowed;">
                    <i class="fas fa-lock"></i> Sudah Booked
                </button>
                <div class="action-note">
                    Rumah ini sudah tidak tersedia untuk booking. Anda masih bisa melihat detail dan spesifikasinya.
                </div>
            <?php endif; ?>
            <a class="cta-secondary" href="/perumahan/data-rumah">
                <i class="fas fa-th-large"></i> Lihat Katalog
            </a>
        </div>
    </aside>
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
