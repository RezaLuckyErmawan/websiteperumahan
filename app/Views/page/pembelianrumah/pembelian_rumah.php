<?php
/** @var list<array<string, mixed>> $perumahan */
/** @var list<array<string, mixed>> $customer */
/** @var list<int> $terjual_ids */
$rumah = is_array($perumahan ?? null) ? $perumahan : [];
$customer = is_array($customer ?? null) ? $customer : [];
$terjual_ids = is_array($terjual_ids ?? null) ? $terjual_ids : [];
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .main,
    .content1 {
        min-width: 0;
        max-width: 100%;
    }

    .content1 {
        overflow-x: hidden;
    }

    #pembelianRumahTable_wrapper {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow: hidden;
    }

    #pembelianRumahTable_wrapper .dataTables_scroll {
        grid-column: 1 / -1;
        width: 100% !important;
        max-width: 100%;
        min-width: 0;
        overflow-x: auto;
    }

    #pembelianRumahTable_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: visible !important;
    }

    #pembelianRumahTable_wrapper table.dataTable {
        min-width: 1400px;
        overflow: visible;
    }

    #pembelianRumahTable thead th {
        background-color: #eef6f8;
        color: #203246;
        text-align: center;
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<table id="pembelianRumahTable" class="display table table-striped table-bordered w-100">
    <thead>
        <tr>
            <th>Customer</th>
            <th>Kode Rumah</th>
            <th>Tanggal Pembelian</th>
            <th>Harga Beli</th>
            <th>Total Dibayar</th>
            <th>Sisa Tagihan</th>
            <th>Status Pembelian</th>
            <th>Sumber</th>
            <th>Verifikasi</th>
            <th>Metode Pembayaran</th>
            <th>Lama Cicilan</th>
            <th>Cicilan Ke</th>
            <th>Cicilan Berikutnya</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Tambah Transaksi Pembelian Rumah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); simpanForm();">
                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <select class="form-control" name="customer_id" id="customerSelect" required>
                            <option value="">Pilih Customer</option>
                            <?php foreach ($customer as $c): ?>
                                <option value="<?= esc($c['id']) ?>"><?= esc($c['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rumah <span class="text-danger">*</span></label>
                        <select class="form-control" name="perumahan_id" id="rumahSelect" required>
                            <option value="">Pilih Rumah</option>
                            <?php foreach ($rumah as $r): ?>
                                <option value="<?= esc($r['id']) ?>"
                                        data-harga="<?= esc($r['harga']) ?>"
                                        data-status="<?= esc($r['status']) ?>">
                                    <?= esc($r['kode_rumah']) ?> - Rp <?= number_format($r['harga'], 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="harga_beli" id="hargaBeli" required>
                        <small class="text-muted">Harga akan terisi otomatis dari harga rumah</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_pembelian" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-control" name="metode_pembayaran" required>
                            <option value="Cash">Cash</option>
                            <option value="Cicilan Internal">Cicilan Internal</option>
                        </select>
                    </div>

                    <div class="cicilan-tahun-field" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Lama Cicilan (Tahun) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="lama_cicilan_tahun" min="1" max="30" placeholder="Contoh: 5">
                            <small class="text-muted">Cicilan dihitung per bulan. Contoh 5 tahun = 60 kali cicilan.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Cicilan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_cicilan">
                            <small class="text-muted cicilan-jatuh-tempo-hint">Pilih tanggal, misalnya 15. Cicilan jatuh tempo setiap tanggal 15 tiap bulan.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Cicilan / Bulan</label>
                            <input type="text" class="form-control" name="info_jumlah_cicilan" readonly placeholder="Otomatis dari harga beli">
                            <small class="text-muted">Jumlah otomatis dari harga beli dibagi lama cicilan.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Dokumen <span class="text-danger">*</span></label>
                        <select class="form-control" name="status_dokumen" required>
                            <option value="Pending">Pending</option>
                            <option value="Verifikasi">Verifikasi</option>
                            <option value="Lengkap">Lengkap</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Pembelian <span class="text-danger">*</span></label>
                        <select class="form-control" name="status_pembelian" required>
                            <option value="DP">DP</option>
                            <option value="Cicil">Cicil</option>
                            <option value="Lunas">Lunas</option>
                            <option value="Batal">Batal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Request Khusus</label>
                        <textarea class="form-control" name="request_khusus" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Marketing</label>
                        <textarea class="form-control" name="catatan_marketing" rows="2"></textarea>
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

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit Transaksi Pembelian Rumah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); updateForm();">
                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                        <select class="form-control" name="customer_id" required>
                            <option value="">Pilih Customer</option>
                            <?php foreach ($customer as $c): ?>
                                <option value="<?= esc($c['id']) ?>"><?= esc($c['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rumah <span class="text-danger">*</span></label>
                        <select class="form-control" name="perumahan_id" required>
                            <option value="">Pilih Rumah</option>
                            <?php foreach ($rumah as $r): ?>
                                <option value="<?= esc($r['id']) ?>"
                                        data-harga="<?= esc($r['harga']) ?>"
                                        data-status="<?= esc($r['status']) ?>">
                                    <?= esc($r['kode_rumah']) ?> - Rp <?= number_format($r['harga'], 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="harga_beli" id="hargaBeliEdit" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_pembelian" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-control" name="metode_pembayaran" required>
                            <option value="Cash">Cash</option>
                            <option value="Cicilan Internal">Cicilan Internal</option>
                        </select>
                    </div>

                    <div class="cicilan-tahun-field" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Lama Cicilan (Tahun) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="lama_cicilan_tahun" min="1" max="30" placeholder="Contoh: 5">
                            <small class="text-muted">Cicilan dihitung per bulan. Contoh 5 tahun = 60 kali cicilan.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Cicilan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_cicilan">
                            <small class="text-muted cicilan-jatuh-tempo-hint">Pilih tanggal, misalnya 15. Cicilan jatuh tempo setiap tanggal 15 tiap bulan.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Cicilan / Bulan</label>
                            <input type="text" class="form-control" name="info_jumlah_cicilan" readonly placeholder="Otomatis dari harga beli">
                            <small class="text-muted">Jumlah otomatis dari harga beli dibagi lama cicilan.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Dokumen <span class="text-danger">*</span></label>
                        <select class="form-control" name="status_dokumen" required>
                            <option value="Pending">Pending</option>
                            <option value="Verifikasi">Verifikasi</option>
                            <option value="Lengkap">Lengkap</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Pembelian <span class="text-danger">*</span></label>
                        <select class="form-control" name="status_pembelian" required>
                            <option value="DP">DP</option>
                            <option value="Cicil">Cicil</option>
                            <option value="Lunas">Lunas</option>
                            <option value="Batal">Batal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Request Khusus</label>
                        <textarea class="form-control" name="request_khusus" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Marketing</label>
                        <textarea class="form-control" name="catatan_marketing" rows="2"></textarea>
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
                Apakah kamu yakin ingin menghapus data pembelian rumah ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerifikasiBooking" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verifikasi Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="bookingDetailInfo"></div>
                <form id="formVerifikasiPembayaran" onsubmit="event.preventDefault();">
                    <input type="hidden" id="bookingVerifikasiId">
                    <input type="hidden" name="harga_beli" id="verifikasiHargaBeli" value="0">

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-control" name="metode_pembayaran" id="verifikasiMetode">
                            <option value="Cash">Cash</option>
                            <option value="Cicilan Internal" selected>Cicilan Internal</option>
                        </select>
                    </div>

                    <div class="cicilan-tahun-field">
                        <div class="mb-3">
                            <label class="form-label">Lama Cicilan (Tahun) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="lama_cicilan_tahun" id="verifikasiLamaCicilan" min="1" max="30" value="5">
                            <small class="text-muted">Cicilan dihitung per bulan. Contoh 5 tahun = 60 kali cicilan.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Cicilan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_cicilan" id="verifikasiTanggalCicilan">
                            <small class="text-muted cicilan-jatuh-tempo-hint">Pilih tanggal, misalnya 15. Cicilan jatuh tempo setiap tanggal 15 tiap bulan.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Cicilan / Bulan</label>
                            <input type="text" class="form-control" name="info_jumlah_cicilan" id="verifikasiJumlahCicilan" readonly>
                            <small class="text-muted">Jumlah otomatis dari harga beli dibagi lama cicilan.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Pembelian <span class="text-danger">*</span></label>
                        <select class="form-control" name="status_pembelian" id="verifikasiStatusPembelian">
                            <option value="DP" selected>DP</option>
                            <option value="Cicil">Cicil</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan verifikasi</label>
                        <textarea class="form-control" id="catatanVerifikasi" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="verifikasiBooking('tolak')">Tolak</button>
                <button type="button" class="btn btn-success" onclick="verifikasiBooking('setujui')">Setujui</button>
            </div>
        </div>
    </div>
</div>
<?= csrf_field() ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/pembelianrumah.js') ?>"></script>
<?= $this->endSection() ?>
