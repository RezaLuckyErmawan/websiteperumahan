<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .dash-title {
        margin: 0 0 16px;
        color: #111827;
        font-size: 28px;
        font-weight: 800;
    }

    .rumah-panel {
        margin-bottom: 22px;
        padding: 22px 24px 26px;
        border-radius: 18px;
        background: #d8efc4;
    }

    .rumah-panel h2 {
        margin: 0 0 16px;
        font-size: 22px;
        font-weight: 800;
        color: #111827;
    }

    .rumah-split {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .galeri-stack {
        display: flex;
        align-items: flex-start;
        gap: 18px;
        min-height: 160px;
    }

    .polaroid {
        width: 120px;
        height: 148px;
        padding: 8px 8px 22px;
        background: #fff;
        border: 1px solid #dbe3ee;
        box-shadow: 6px 8px 0 rgba(15, 23, 42, 0.08);
        transform: rotate(-6deg);
    }

    .polaroid + .polaroid { transform: rotate(5deg); }

    .polaroid img,
    .polaroid .no-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        background: #e8f1fb;
    }

    .polaroid .no-image {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 36px;
    }

    .spec-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 8px;
    }

    .spec-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #1f2937;
        font-size: 15px;
        font-weight: 700;
    }

    .spec-item input { width: 16px; height: 16px; }

    .berkas-head {
        margin: 8px 0 6px;
        font-size: 20px;
        font-weight: 800;
        color: #111827;
    }

    .berkas-head .deadline { color: #ca8a04; }
    .berkas-head .sisa { color: #dc2626; }

    .berkas-note {
        margin: 0 0 14px;
        color: #334155;
        font-weight: 600;
    }

    .berkas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
    }

    .berkas-card {
        min-height: 168px;
        padding: 16px 12px 14px;
        border-radius: 18px 18px 10px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        text-align: center;
        color: #0f172a;
        font-weight: 800;
        position: relative;
    }

    .berkas-card .status-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 22px;
        color: #fff;
        margin-bottom: 8px;
    }

    .berkas-card .status-icon.ok { background: #22c55e; }
    .berkas-card .status-icon.no { background: #ef4444; }
    .berkas-card .status-icon.wait { background: #f59e0b; color: #111827; }

    .berkas-card .upload-btn,
    .cicilan-btn {
        min-height: 34px;
        padding: 6px 16px;
        border: 0;
        border-radius: 999px;
        background: #ffffff;
        color: #111827;
        font-weight: 800;
        cursor: pointer;
    }

    .cicilan-card {
        margin-top: 8px;
        padding: 18px 20px 22px;
        border-radius: 16px;
        background: #9fe3ea;
        color: #111827;
        font-weight: 800;
        line-height: 1.55;
    }

    .timeline-wrap {
        margin-top: 18px;
        overflow-x: auto;
        padding: 28px 8px 70px;
    }

    .timeline {
        position: relative;
        display: flex;
        align-items: flex-start;
        min-width: max-content;
        gap: 28px;
        padding: 8px 12px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        right: 8px;
        top: 46px;
        border-top: 2px dashed #334155;
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
        background: #111827;
        color: #fff;
        font-size: 16px;
    }

    .tl-icon.ok { background: #22c55e; }
    .tl-icon.wait { background: #facc15; color: #111827; }
    .tl-icon.q { background: #111827; }

    .tl-pill {
        min-width: 42px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #7dd3e8;
        color: #0f172a;
        font-size: 13px;
        font-weight: 800;
        text-align: center;
    }

    .tl-pill.dp { background: #c4b5fd; }
    .tl-node.current .tl-pill { background: #2dd4bf; transform: scale(1.12); }

    .tl-tip {
        position: absolute;
        top: 86px;
        left: 50%;
        transform: translateX(-50%);
        width: 210px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #9fe3ea;
        color: #0f172a;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        display: none;
    }

    .tl-node.current .tl-tip { display: block; }

    .tl-tip::before {
        content: '';
        position: absolute;
        top: -7px;
        left: 50%;
        transform: translateX(-50%);
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 8px solid #9fe3ea;
    }

    .cicilan-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .cicilan-btn.primary {
        min-height: 42px;
        padding: 8px 22px;
        background: #5ec8d4;
        color: #0f172a;
    }

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

    .rumah-card-image { height: 160px; background: #eef2f7; }
    .rumah-card-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
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
    $berkasColors = ['#b9d7f5', '#9fd6ea', '#8eddd4', '#8fe0c4', '#b4e6a6', '#c8e89a', '#d7e88c', '#e4e89a'];
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
                        <a href="/perumahan/data-rumah/<?= (int) ($item['id'] ?? 0) ?>" class="btn btn-primary btn-sm w-100">Lihat Detail</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <?php $gambar = $rumah['gambar'] ?? ''; ?>
    <div class="rumah-panel">
        <h2>Detail Rumah Pesanan Anda (Rumah <?= esc($rumah['kode_rumah'] ?? '-') ?>)</h2>
        <div class="rumah-split">
            <div>
                <strong>Galeri</strong>
                <div class="galeri-stack">
                    <?php for ($i = 0; $i < 2; $i++): ?>
                        <div class="polaroid">
                            <?php if ($gambar): ?>
                                <img src="/<?= esc($gambar) ?>" alt="<?= esc($rumah['kode_rumah'] ?? 'Rumah') ?>">
                            <?php else: ?>
                                <div class="no-image"><span class="material-icons">photo</span></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div>
                <strong>Spesifikasi</strong>
                <div class="spec-list">
                    <label class="spec-item"><input type="checkbox" checked disabled> Tipe <?= esc($rumah['tipe'] ?? '-') ?></label>
                    <label class="spec-item"><input type="checkbox" checked disabled> LT <?= esc($rumah['luas_tanah'] ?? '-') ?> m² · LB <?= esc($rumah['luas_bangunan'] ?? '-') ?> m²</label>
                    <label class="spec-item"><input type="checkbox" checked disabled> <?= esc($rumah['lokasi'] ?? '-') ?></label>
                </div>
            </div>
        </div>
    </div>

    <div class="berkas-head">
        Pemberkasan!
        (Mohon dilengkapi sebelum
        <span class="deadline"><?= esc($infoBerkas['deadline_long'] ?? '-') ?></span>
        <?php if (empty($infoBerkas['kedaluwarsa'])): ?>
            (<span class="sisa"><?= esc($infoBerkas['sisa_display'] ?? '-') ?></span>)
        <?php endif; ?>
        )
    </div>
    <p class="berkas-note">Silahkan unggah dokumen yang diperlukan berikut:</p>
    <div class="berkas-grid">
        <?php foreach ($berkasItems as $index => $item): ?>
            <?php
                $status = (string) ($item['status'] ?? '');
                $bisaUnggah = empty($item['file']) || $status === 'ditolak';
                if (!empty($infoBerkas['kedaluwarsa']) && $status !== 'ditolak' && empty($item['file'])) {
                    $bisaUnggah = false;
                }
            ?>
            <div class="berkas-card" style="background: <?= esc($berkasColors[$index % count($berkasColors)]) ?>;">
                <div>
                    <?php if ($status === 'disetujui'): ?>
                        <span class="status-icon ok material-icons">check</span>
                    <?php elseif ($status === 'ditolak'): ?>
                        <span class="status-icon no material-icons">close</span>
                    <?php elseif ($status === 'pending'): ?>
                        <span class="status-icon wait material-icons">schedule</span>
                    <?php endif; ?>
                    <div><?= esc($item['short']) ?></div>
                </div>
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
        <?php endforeach; ?>
    </div>

    <h2 class="dash-title" style="margin-top:28px;">Progress Cicilan</h2>
    <div class="cicilan-card">
        Rumah <?= esc($rumah['kode_rumah'] ?? '-') ?><br>
        Total: Rp <?= number_format((float) ($ringkasan['harga_beli'] ?? 0), 0, ',', '.') ?><br>
        Durasi : <?= esc($ringkasan['durasi_text'] ?? '-') ?>
        (<?= esc($ringkasan['tanggal_mulai_display'] ?? '-') ?> - <?= esc($ringkasan['tanggal_selesai_display'] ?? '-') ?>)<br>
        Cicilan: Rp <?= number_format((float) ($ringkasan['jumlah_cicilan'] ?? 0), 0, ',', '.') ?> / bulan.
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
<?php endif; ?>
<?= $this->endSection() ?>

<?php if ($pembelian): ?>
<?= $this->section('modals') ?>
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
