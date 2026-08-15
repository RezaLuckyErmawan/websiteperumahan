<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .detail-toolbar { margin-bottom: 16px; }

    .detail-section {
        margin-bottom: 18px;
        padding: 18px;
        border: 1px solid #e4e8ef;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .berkas-item {
        margin-bottom: 16px;
        padding: 14px;
        border: 1px solid #e4e8ef;
        border-radius: 8px;
        background: #f8fafc;
    }

    .berkas-item label {
        display: block;
        margin-bottom: 4px;
        color: #172033;
        font-size: 14px;
        font-weight: 800;
    }

    .berkas-item small {
        display: block;
        margin-bottom: 10px;
        color: #647084;
    }

    .badge-wajib {
        display: inline-flex;
        margin-left: 6px;
        padding: 2px 8px;
        border-radius: 999px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 11px;
        font-weight: 800;
    }

    .badge-opsional {
        display: inline-flex;
        margin-left: 6px;
        padding: 2px 8px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #334155;
        font-size: 11px;
        font-weight: 800;
    }

    .file-ok {
        margin-top: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #047857;
    }

    .deadline-box {
        margin-bottom: 16px;
        padding: 12px 14px;
        border-radius: 8px;
        background: #fff7ed;
        color: #9a3412;
        font-weight: 700;
    }

    .deadline-box.ok { background: #ecfdf5; color: #047857; }
    .deadline-box.late { background: #fef2f2; color: #b91c1c; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $transaksi = is_array($transaksi ?? null) ? $transaksi : [];
    $info = is_array($info ?? null) ? $info : [];
    $jenisBerkas = is_array($jenisBerkas ?? null) ? $jenisBerkas : [];
    $uploaded = $info['uploaded'] ?? [];
    $error = session()->getFlashdata('error');
    $success = session()->getFlashdata('success');
    $status = $info['status'] ?? 'pending';
    $deadlineClass = 'deadline-box';
    if ($status === 'lengkap') $deadlineClass .= ' ok';
    if ($status === 'kedaluwarsa') $deadlineClass .= ' late';
?>

<div class="detail-toolbar">
    <a href="/perumahan/rumah-booking" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>

<div class="detail-section">
    <h5 style="font-weight:800;color:#172033;">Berkas <?= esc($transaksi['kode_rumah'] ?? '') ?></h5>
    <p style="color:#647084;margin-bottom:12px;">
        <?= esc($transaksi['nama'] ?? '-') ?> · Tipe <?= esc($transaksi['tipe'] ?? '-') ?>
    </p>

    <?php if ($status === 'lengkap'): ?>
        <div class="<?= $deadlineClass ?>">Semua berkas wajib sudah dilengkapi.</div>
    <?php elseif ($status === 'kedaluwarsa'): ?>
        <div class="<?= $deadlineClass ?>">Batas waktu 7 hari sudah habis. Unggah berkas tidak lagi tersedia.</div>
    <?php else: ?>
        <div class="<?= $deadlineClass ?>">
            Lengkapi berkas maksimal 7 hari sejak booking.
            Sisa <?= (int) ($info['sisa_hari'] ?? 0) ?> hari (batas <?= esc($info['deadline_display'] ?? '-') ?>).
        </div>
    <?php endif; ?>

    <form method="post" action="/perumahan/rumah-booking/<?= (int) ($transaksi['id'] ?? 0) ?>/berkas" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <?php foreach ($jenisBerkas as $key => $meta): ?>
            <div class="berkas-item">
                <label>
                    <?= esc($meta['label']) ?>
                    <?php if (!empty($meta['wajib'])): ?>
                        <span class="badge-wajib">Wajib</span>
                    <?php else: ?>
                        <span class="badge-opsional">Opsional</span>
                    <?php endif; ?>
                </label>
                <small><?= esc($meta['keterangan']) ?></small>
                <?php if (!empty($info['dapat_unggah'])): ?>
                    <input type="file" class="form-control" name="<?= esc($key) ?>" accept=".jpg,.jpeg,.png,.pdf">
                <?php endif; ?>
                <?php if (!empty($uploaded[$key])): ?>
                    <div class="file-ok">
                        Sudah diunggah:
                        <a href="/<?= esc($uploaded[$key]) ?>" target="_blank">Lihat berkas</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($info['dapat_unggah'])): ?>
            <button type="submit" class="btn btn-primary">Simpan Berkas</button>
        <?php endif; ?>
    </form>
</div>
<?= $this->endSection() ?>
