<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .rumah-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 18px;
    }

    .rumah-card {
        overflow: hidden;
        background: #fff;
        border: 1px solid #e4e8ef;
        border-radius: 12px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .rumah-card-image {
        width: 100%;
        height: 180px;
        background: #eef2f7;
        overflow: hidden;
    }

    .rumah-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
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
        background: #fff;
        border-radius: 12px;
        padding: 28px 24px;
        box-shadow: 0 4px 12px rgba(23, 32, 51, 0.06);
        color: #647084;
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
                $gambar = $item['gambar'] ?? '';
            ?>
            <div class="rumah-card">
                <div class="rumah-card-image">
                    <?php if ($gambar): ?>
                        <img src="/<?= esc($gambar) ?>" alt="<?= esc($item['kode_rumah'] ?? 'Rumah') ?>">
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
                    <a href="/perumahan/data-rumah/<?= (int) $item['id'] ?>" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
