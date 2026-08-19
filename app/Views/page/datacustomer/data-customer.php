<?php
$pageTitle = 'Data Customer';
$useDataTables = true;
/** @var list<array<string, mixed>> $customers */
$customers = is_array($customers ?? null) ? $customers : [];
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    #customerTable thead th {
        background-color: #eef6f8;
        color: #203246;
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="table-responsive">
    <table id="customerTable" class="display table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Alamat</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data customer.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $item): ?>
                    <tr>
                        <td><?= esc($item['nama'] ?? '-') ?></td>
                        <td><?= esc($item['email'] ?? '-') ?></td>
                        <td><?= esc($item['telepon'] ?? '-') ?></td>
                        <td><?= esc($item['alamat'] ?? '-') ?></td>
                        <td><?= !empty($item['tanggal_pembelian']) ? esc(date('d-m-Y', strtotime($item['tanggal_pembelian']))) : '-' ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" onclick="editData(<?= (int) ($item['id'] ?? 0) ?>)"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="hapusData(<?= (int) ($item['id'] ?? 0) ?>)"><i class="fas fa-trash"></i> Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Modal Tambah/Edit Customer -->
<div class="modal fade" id="modalCustomerForm" tabindex="-1" aria-labelledby="modalCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formCustomer">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCustomerLabel">Tambah Data Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden ID -->
                    <input type="hidden" name="id" id="customer_id">

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" name="telepon" id="telepon" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_pembelian" class="form-label">Tanggal Daftar</label>
                        <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal konfirmasi Hapus -->
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-labelledby="modalConfirmDelete" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="close"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin Menghapus data ini ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnDeleteConfirm">Hapus</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                <p id="successMessage">Data Berhasil diproses.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('modalCustomerForm').addEventListener('shown.bs.modal', function () {
    let today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_pembelian').value = today;
});
</script>
<script src="<?= base_url('assets/js/datacustomer.js') ?>"></script>
<?= $this->endSection() ?>
