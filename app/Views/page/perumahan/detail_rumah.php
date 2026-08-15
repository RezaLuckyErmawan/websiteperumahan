<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .detail-toolbar {
        margin-bottom: 16px;
    }

    .detail-section {
        margin-bottom: 18px;
        padding: 18px;
        border: 1px solid #e4e8ef;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .detail-section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .detail-section-header .material-icons {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 20px;
    }

    .detail-section-header h5 {
        margin: 0;
        color: #172033;
        font-size: 16px;
        font-weight: 800;
    }

    .payment-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .payment-detail-item {
        min-height: 66px;
        padding: 13px 14px;
        border: 1px solid #e4e8ef;
        border-radius: 8px;
        background: #ffffff;
    }

    .payment-detail-item.full {
        grid-column: 1 / -1;
    }

    .payment-detail-label {
        margin-bottom: 5px;
        color: #647084;
        font-size: 12px;
        font-weight: 800;
    }

    .payment-detail-value {
        color: #172033;
        font-size: 14px;
        font-weight: 700;
        overflow-wrap: anywhere;
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

    .rumah-hero {
        width: 100%;
        max-height: 360px;
        overflow: hidden;
        border-radius: 8px;
        background: #eef2f7;
        margin-bottom: 16px;
    }

    .rumah-hero img {
        width: 100%;
        max-height: 360px;
        object-fit: cover;
        display: block;
    }

    .rumah-hero .no-image {
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 56px;
    }

    .checkout-actions {
        margin-top: 16px;
        display: flex;
        gap: 8px;
    }

    .alert-flash {
        margin-bottom: 16px;
    }

    @media (max-width: 768px) {
        .payment-detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $rumah = is_array($rumah ?? null) ? $rumah : [];
    $status = strtolower((string) ($rumah['status'] ?? ''));
    $statusClass = 'status-secondary';
    if (in_array($status, ['dijual', 'tersedia'], true)) $statusClass = 'status-primary';
    elseif (in_array($status, ['terjual', 'lunas'], true)) $statusClass = 'status-success';
    elseif (in_array($status, ['proses pembangunan', 'booking', 'booked'], true)) $statusClass = 'status-warning';
    $gambar = $rumah['gambar'] ?? '';
    $dokumen = $rumah['dokumen'] ?? '';
    $dapatCheckout = (bool) ($dapatCheckout ?? false);
    $customer = is_array($customer ?? null) ? $customer : [];
    $error = session()->getFlashdata('error');
    $success = session()->getFlashdata('success');
?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-flash"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success alert-flash"><?= esc($success) ?></div>
<?php endif; ?>

<div class="detail-toolbar">
    <a href="/perumahan/data-rumah" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="detail-section">
    <div class="detail-section-header">
        <span class="material-icons">home_work</span>
        <h5>Informasi Rumah</h5>
    </div>

    <div class="rumah-hero">
        <?php if ($gambar): ?>
            <img src="/<?= esc($gambar) ?>" alt="<?= esc($rumah['kode_rumah'] ?? 'Rumah') ?>">
        <?php else: ?>
            <div class="no-image"><span class="material-icons">home_work</span></div>
        <?php endif; ?>
    </div>

    <div class="payment-detail-grid">
        <div class="payment-detail-item">
            <div class="payment-detail-label">Kode Rumah</div>
            <div class="payment-detail-value"><span class="code-pill"><?= esc($rumah['kode_rumah'] ?? '-') ?></span></div>
        </div>
        <div class="payment-detail-item">
            <div class="payment-detail-label">Status</div>
            <div class="payment-detail-value">
                <span class="status-pill <?= $statusClass ?>"><?= esc($rumah['status'] ?? '-') ?></span>
            </div>
        </div>
        <div class="payment-detail-item">
            <div class="payment-detail-label">Tipe</div>
            <div class="payment-detail-value"><?= esc($rumah['tipe'] ?? '-') ?></div>
        </div>
        <div class="payment-detail-item">
            <div class="payment-detail-label">Harga</div>
            <div class="payment-detail-value">Rp <?= number_format((float) ($rumah['harga'] ?? 0), 0, ',', '.') ?></div>
        </div>
        <div class="payment-detail-item">
            <div class="payment-detail-label">Luas Tanah</div>
            <div class="payment-detail-value"><?= esc($rumah['luas_tanah'] ?? '-') ?> m²</div>
        </div>
        <div class="payment-detail-item">
            <div class="payment-detail-label">Luas Bangunan</div>
            <div class="payment-detail-value"><?= esc($rumah['luas_bangunan'] ?? '-') ?> m²</div>
        </div>
        <div class="payment-detail-item full">
            <div class="payment-detail-label">Lokasi</div>
            <div class="payment-detail-value"><?= esc($rumah['lokasi'] ?? '-') ?></div>
        </div>
        <div class="payment-detail-item full">
            <div class="payment-detail-label">Deskripsi</div>
            <div class="payment-detail-value">
                <?= !empty($rumah['deskripsi']) ? nl2br(esc($rumah['deskripsi'])) : '-' ?>
            </div>
        </div>
        <?php if ($dokumen): ?>
            <div class="payment-detail-item full">
                <div class="payment-detail-label">Dokumen</div>
                <div class="payment-detail-value">
                    <a href="/<?= esc($dokumen) ?>" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="checkout-actions">
        <?php if ($dapatCheckout): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCheckout">
                Checkout
            </button>
        <?php else: ?>
            <button type="button" class="btn btn-secondary" disabled>Sudah Booked</button>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<div class="modal fade" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="/perumahan/data-rumah/<?= (int) ($rumah['id'] ?? 0) ?>/checkout">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCheckoutLabel">Input Data Customer</h5>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Checkout</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
