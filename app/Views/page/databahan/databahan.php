<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    #dataBahanTable thead th {
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
<table id="dataBahanTable" class="display table table-bordered">
    <thead>
        <tr>
            <th>Nama Bahan</th>
            <th>Stok</th>
            <th>Satuan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Modal Tambah dan Edit -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form onsubmit="event.preventDefault(); simpanForm();">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFormLabel">Tambah Data Bahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control" name="nama_bahan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" class="form-control" name="satuan" required>
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pesan Berhasil -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-success text-black">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="successModalLabel">✔️ Berhasil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="successMessage">Data Berhasil Dihapus</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/databahan.js') ?>"></script>
<?= $this->endSection() ?>
