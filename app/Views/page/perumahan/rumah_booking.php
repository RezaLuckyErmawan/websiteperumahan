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

    .rumah-card-image img,
    .rumah-card-image .no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 48px;
    }

    .rumah-card-image img {
        object-fit: cover;
        display: block;
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
    .status-warning { background: #fef3c7; color: #92400e; }
    .status-success { background: #d1fae5; color: #047857; }
    .status-danger { background: #fee2e2; color: #b91c1c; }

    .rumah-card-title {
        margin: 0 0 6px;
        color: #172033;
        font-size: 16px;
        font-weight: 800;
    }

    .rumah-card-meta {
        margin: 0 0 8px;
        color: #647084;
        font-size: 13px;
    }

    .rumah-card-price {
        margin-bottom: 10px;
        color: #059669;
        font-size: 16px;
        font-weight: 800;
    }

    .berkas-note {
        margin-bottom: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        background: #fff7ed;
        color: #9a3412;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.45;
    }

    .berkas-note.ok {
        background: #ecfdf5;
        color: #047857;
    }

    .berkas-note.late {
        background: #fef2f2;
        color: #b91c1c;
    }

    .card-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .empty-page-card {
        background: #fff;
        border-radius: 12px;
        padding: 28px 24px;
        box-shadow: 0 4px 12px rgba(23, 32, 51, 0.06);
        color: #647084;
        text-align: center;
    }

    .alert-flash {
        margin-bottom: 16px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $booking = is_array($booking ?? null) ? $booking : [];
    $success = session()->getFlashdata('success');
    $error = session()->getFlashdata('error');
?>

<?php if ($success): ?>
    <div class="alert alert-success alert-flash"><?= esc($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-flash"><?= esc($error) ?></div>
<?php endif; ?>

<?php if (empty($booking)): ?>
    <div class="empty-page-card">
        <h3>Rumah yang booking</h3>
        <p>Belum ada rumah yang di-booking.</p>
    </div>
<?php else: ?>
    <div class="rumah-grid">
        <?php foreach ($booking as $item): ?>
            <?php
                $info = $item['info_berkas'] ?? [];
                $statusBerkas = $info['status'] ?? 'pending';
                $noteClass = 'berkas-note';
                if ($statusBerkas === 'lengkap') $noteClass .= ' ok';
                if ($statusBerkas === 'kedaluwarsa') $noteClass .= ' late';
            ?>
            <div class="rumah-card">
                <div class="rumah-card-image">
                    <?php if (!empty($item['gambar'])): ?>
                        <img src="/<?= esc($item['gambar']) ?>" alt="<?= esc($item['kode_rumah'] ?? 'Rumah') ?>">
                    <?php else: ?>
                        <div class="no-image"><span class="material-icons">home_work</span></div>
                    <?php endif; ?>
                </div>
                <div class="rumah-card-body">
                    <div class="rumah-card-top">
                        <span class="code-pill"><?= esc($item['kode_rumah'] ?? '-') ?></span>
                        <span class="status-pill status-warning"><?= esc($item['status_rumah'] ?? 'Booked') ?></span>
                    </div>
                    <h3 class="rumah-card-title">Tipe <?= esc($item['tipe'] ?? '-') ?></h3>
                    <p class="rumah-card-meta"><?= esc($item['lokasi'] ?? '-') ?></p>
                    <p class="rumah-card-meta"><?= esc($item['nama'] ?? '-') ?> · <?= esc($item['telepon'] ?? '-') ?></p>
                    <div class="rumah-card-price">Rp <?= number_format((float) ($item['harga'] ?? 0), 0, ',', '.') ?></div>

                    <?php if ($statusBerkas === 'lengkap'): ?>
                        <div class="<?= $noteClass ?>">Berkas wajib sudah lengkap.</div>
                    <?php elseif ($statusBerkas === 'kedaluwarsa'): ?>
                        <div class="<?= $noteClass ?>">Batas 7 hari untuk melengkapi berkas sudah habis.</div>
                    <?php else: ?>
                        <div class="<?= $noteClass ?>">
                            Lengkapi berkas maksimal 7 hari.
                            Sisa <?= (int) ($info['sisa_hari'] ?? 0) ?> hari (batas <?= esc($info['deadline_display'] ?? '-') ?>).
                        </div>
                    <?php endif; ?>

                    <div class="card-actions">
                        <?php if ($statusBerkas === 'kedaluwarsa'): ?>
                            <button type="button" class="btn btn-secondary btn-sm w-100" disabled>Berkas kedaluwarsa</button>
                        <?php else: ?>
                            <a href="/perumahan/rumah-booking/<?= (int) $item['id'] ?>/berkas" class="btn btn-primary btn-sm w-100">
                                <?= $statusBerkas === 'lengkap' ? 'Lihat Berkas' : 'Lengkapi Berkas' ?>
                            </a>
                        <?php endif; ?>
                        <a href="/perumahan/data-rumah/<?= (int) $item['perumahan_id'] ?>" class="btn btn-outline-secondary btn-sm w-100">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
