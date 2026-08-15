<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    #pembayaranRumahTable thead th {
        background-color: #eef6f8;
        color: #203246;
        text-align: center;
    }

    #pembayaranRumahTable td:last-child {
        text-align: center;
    }

    .admin-payment-field {
        <?php if (session()->get('role') === 'customer'): ?>
        display: none;
        <?php endif; ?>
    }

    .customer-only-field {
        <?php if (session()->get('role') !== 'customer'): ?>
        display: none;
        <?php endif; ?>
    }

    .user-popover {
        min-width: 180px;
        padding: 2px 0;
    }

    .user-popover-row {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-bottom: 10px;
    }

    .user-popover-row span {
        color: #647084;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .user-popover-row strong {
        color: #172033;
        font-size: 13px;
        font-weight: 700;
    }

    .user-popover-logout {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
        padding-top: 10px;
        border-top: 1px solid #e4e8ef;
        color: #dc2626;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .user-popover-logout:hover {
        color: #b91c1c;
    }

    .payment-actions {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .payment-actions .btn {
        width: 32px;
        height: 32px;
        padding: 0;
    }

    .payment-detail-header {
        background: #f8fafc;
        border-bottom: 1px solid #e4e8ef;
    }

    .payment-detail-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .payment-detail-title .material-icons {
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

    .payment-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .payment-summary-item {
        padding: 14px;
        border: 1px solid #e4e8ef;
        border-radius: 8px;
        background: #f8fafc;
    }

    .payment-summary-item span {
        display: block;
        margin-bottom: 6px;
        color: #647084;
        font-size: 12px;
        font-weight: 800;
    }

    .payment-summary-item strong {
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

    @media (max-width: 768px) {
        .payment-summary,
        .payment-detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<table id="pembayaranRumahTable" class="display table table-striped table-bordered w-100">
    <thead>
        <tr>
            <th>Customer</th>
            <th>Kode Rumah</th>
            <th>Tanggal Bayar</th>
            <th>Jenis</th>
            <th>Jumlah Bayar</th>
            <th>Total Dibayar</th>
            <th>Sisa Tagihan</th>
            <th>Status</th>
            <th>Verifikasi</th>
            <th>Bukti</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<?php
    /** @var list<array<string, mixed>> $pembelian */
    $pembelian = is_array($pembelian ?? null) ? $pembelian : [];
?>
<!-- Modal Tambah Pembayaran -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Tambah Pembayaran Rumah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); simpanForm();" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Transaksi Rumah <span class="text-danger">*</span></label>
                        <select class="form-control" name="pembelian_rumah_id" onchange="updateRingkasan()" required>
                            <option value="">Pilih customer dan rumah yang akan dibayar</option>
                            <?php foreach ($pembelian as $item): ?>
                                <option value="<?= esc($item['id']) ?>">
                                    <?= esc($item['nama_customer']) ?> - <?= esc($item['kode_rumah']) ?> - Rp <?= number_format($item['harga_beli'], 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="alert alert-info" id="ringkasanPembayaran">
                        <span class="text-muted">Pilih transaksi rumah untuk melihat sisa tagihan.</span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 admin-payment-field">
                            <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_bayar" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah_bayar" min="1" placeholder="Otomatis sesuai cicilan bulan ini" required>
                        <small class="text-muted jumlah-bayar-hint">Jumlah cicilan terisi otomatis. Cicilan 1 kali tiap bulan.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>

                    <div class="mb-3 customer-only-field">
                        <label class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">Format: JPG, JPEG, PNG, PDF. Maksimal 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pembayaran -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit Pembayaran Rumah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); updateForm();" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Transaksi Rumah <span class="text-danger">*</span></label>
                        <select class="form-control" name="pembelian_rumah_id" onchange="updateRingkasanEdit()" required>
                            <option value="">Pilih customer dan rumah yang akan dibayar</option>
                            <?php foreach ($pembelian as $item): ?>
                                <option value="<?= esc($item['id']) ?>">
                                    <?= esc($item['nama_customer']) ?> - <?= esc($item['kode_rumah']) ?> - Rp <?= number_format($item['harga_beli'], 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="alert alert-info" id="ringkasanPembayaranEdit">
                        <span class="text-muted">Pilih transaksi rumah untuk melihat sisa tagihan.</span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_bayar" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah_bayar" min="1" placeholder="Otomatis sesuai cicilan bulan ini" required>
                        <small class="text-muted jumlah-bayar-hint">Jumlah cicilan terisi otomatis. Cicilan 1 kali tiap bulan.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bukti Pembayaran</label>
                        <input type="file" class="form-control" name="bukti_bayar" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Format: JPG, JPEG, PNG, PDF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah bukti.</small>
                        <input type="hidden" name="existing_bukti_bayar" id="existingBuktiBayar">
                        <div id="buktiPreviewContainer" class="mt-2" style="display: none;">
                            <a id="buktiPreview" href="#" target="_blank" style="color: #007bff; text-decoration: none;">
                                <i class="fas fa-file-alt"></i> Lihat bukti yang ada
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Pembayaran -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header payment-detail-header">
                <h5 class="modal-title payment-detail-title" id="detailModalLabel">
                    <span class="material-icons">receipt_long</span>
                    Detail Pembayaran Cicilan Rumah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="payment-summary">
                    <div class="payment-summary-item">
                        <span>Jumlah Bayar</span>
                        <strong id="detailJumlahBayar"></strong>
                    </div>
                    <div class="payment-summary-item">
                        <span>Total Dibayar</span>
                        <strong id="detailTotalBayar"></strong>
                    </div>
                    <div class="payment-summary-item">
                        <span>Sisa Tagihan</span>
                        <strong id="detailSisaBayar"></strong>
                    </div>
                </div>

                <div class="payment-detail-grid">
                    <div class="payment-detail-item">
                        <div class="payment-detail-label">Customer</div>
                        <div class="payment-detail-value" id="detailCustomer"></div>
                    </div>
                    <div class="payment-detail-item">
                        <div class="payment-detail-label">Kode Rumah</div>
                        <div class="payment-detail-value" id="detailKodeRumah"></div>
                    </div>
                    <div class="payment-detail-item">
                        <div class="payment-detail-label">Tanggal Bayar</div>
                        <div class="payment-detail-value" id="detailTanggalBayar"></div>
                    </div>
                    <div class="payment-detail-item">
                        <div class="payment-detail-label">Status Pembelian</div>
                        <div class="payment-detail-value" id="detailStatusPembelian"></div>
                    </div>
                    <div class="payment-detail-item">
                        <div class="payment-detail-label">Status Verifikasi</div>
                        <div class="payment-detail-value" id="detailStatusPengajuan"></div>
                    </div>
                    <div class="payment-detail-item">
                        <div class="payment-detail-label">Jenis Pembayaran</div>
                        <div class="payment-detail-value" id="detailJenisPembayaran"></div>
                    </div>
                    <div class="payment-detail-item">
                        <div class="payment-detail-label">Metode Bayar</div>
                        <div class="payment-detail-value" id="detailMetodeBayar"></div>
                    </div>
                    <div class="payment-detail-item full">
                        <div class="payment-detail-label">Keterangan</div>
                        <div class="payment-detail-value" id="detailKeterangan"></div>
                    </div>
                    <div class="payment-detail-item full">
                        <div class="payment-detail-label">Bukti Pembayaran</div>
                        <div class="payment-detail-value" id="detailBuktiBayar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-success text-black">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="successModalLabel">✔️ Berhasil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="successMessage">Data berhasil disimpan!</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah kamu yakin ingin menghapus data pembayaran ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Approve -->
<div class="modal fade" id="confirmApproveModal" tabindex="-1" aria-labelledby="confirmApproveLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmApproveLabel">Konfirmasi Setuju</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah kamu yakin ingin menyetujui pembayaran ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm" id="confirmApproveBtn">Setuju</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  window.selectedPembelianId = "<?= esc($selectedPembelianId ?? '') ?>";
  window.canCreatePayments = <?= ($canCreatePayments ?? true) ? 'true' : 'false' ?>;
  window.canModifyPayments = <?= ($canModifyPayments ?? true) ? 'true' : 'false' ?>;
</script>
<script src="<?= base_url('assets/js/pembayaranrumah.js') ?>"></script>
<?= $this->endSection() ?>
