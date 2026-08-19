<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    #pembelianTable thead th {
        background-color: #eef6f8;
        color: #203246;
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    /** @var list<array<string, mixed>> $pembelianbahan */
    $pembelianbahan = is_array($pembelianbahan ?? null) ? $pembelianbahan : [];
?>
<table id="pembelianTable" class="table table-bordered">
    <thead>
        <tr>
            <th>Nomor Nota</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Total Harga</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pembelianbahan)): ?>
            <tr>
                <td colspan="5" class="text-center text-muted">Belum ada data pembelian bahan.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($pembelianbahan as $row): ?>
                <tr>
                    <td><?= esc($row['nomor_nota'] ?? '-') ?></td>
                    <td><?= !empty($row['tanggal']) ? esc(date('d-m-Y', strtotime($row['tanggal']))) : '-' ?></td>
                    <td><?= esc($row['supplier'] ?? '-') ?></td>
                    <td>Rp <?= number_format((float) ($row['total_harga'] ?? 0), 0, ',', '.') ?></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-primary btn-sm" onclick="editData(<?= (int) ($row['id'] ?? 0) ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapusData(<?= (int) ($row['id'] ?? 0) ?>)">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Modal Form Tambah/Edit Pembelian -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Tambah Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); simpanForm();">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label for="nomor_nota" class="form-label">Nomor Nota</label>
                        <input type="text" class="form-control" name="nomor_nota" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier" class="form-label">Supplier</label>
                        <input type="text" class="form-control" name="supplier" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_harga" class="form-label">Total Harga</label>
                        <input type="number" class="form-control" name="total_harga" required>
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

<!-- Modal konfirmasi dihapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="close"></button>
            </div>
            <div class="modal-body">
                Apakah Kamu Yakin Ingin Menghapus Data Ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pesan berhasil -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-success text-black">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="successModalLabel">✔️ Berhasil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="successMessage">Data Berhasil dihapus</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/datapembelian.js') ?>"></script>
<?= $this->endSection() ?>
