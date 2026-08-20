<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    #dataRumahTable thead th {
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

    .gambar-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .gambar-thumb {
        position: relative;
        width: 84px;
        height: 84px;
        overflow: hidden;
        border: 1px solid #e4e8ef;
        border-radius: 8px;
        background: #f8fafc;
    }

    .gambar-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .gambar-thumb button {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 22px;
        height: 22px;
        border: 0;
        border-radius: 999px;
        background: #dc2626;
        color: #fff;
        font-size: 14px;
        line-height: 22px;
        cursor: pointer;
    }

    .gambar-thumb .thumb-label {
        position: absolute;
        left: 4px;
        bottom: 4px;
        padding: 1px 6px;
        border-radius: 999px;
        background: #2563eb;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
    }

    .lihat-gambar-stage {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 240px;
        padding: 0 56px;
    }

    .lihat-gambar-stage img {
        max-width: 100%;
        max-height: 500px;
        object-fit: contain;
        border-radius: 8px;
    }

    .lihat-gambar-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        padding: 0;
        margin: 0;
        border: 0;
        border-radius: 999px;
        background: #2563eb;
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        appearance: none;
    }

    .lihat-gambar-nav.prev { left: 8px; }
    .lihat-gambar-nav.next { right: 8px; }

    .lihat-gambar-nav .material-icons {
        font-size: 22px;
        line-height: 22px;
        width: 22px;
        height: 22px;
        display: block;
    }

    .tabel-gambar {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 50px;
        cursor: pointer;
        vertical-align: middle;
    }

    .tabel-gambar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: block;
    }

    .tabel-gambar-count {
        position: absolute;
        top: -7px;
        right: -7px;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        border-radius: 999px;
        background: #334155;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<table id="dataRumahTable" class="display table table-striped table-bordered w-100">
    <thead>
        <tr>
            <th>Kode Rumah</th>
            <th>Gambar</th>
            <th>Lokasi</th>
            <th>Tipe</th>
            <th>Luas Tanah</th>
            <th>Luas Bangunan</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Dokumen</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Modal Tambah dan Edit -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Edit Data Rumah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); simpanForm();">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label for="kode_rumah" class="form-label">Kode Rumah</label>
                        <input type="text" class="form-control" name="kode_rumah" required>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <textarea class="form-control" name="lokasi" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe</label>
                        <input type="text" class="form-control" name="tipe" required>
                    </div>
                    <div class="mb-3">
                        <label for="luas_tanah" class="form-label">Luas Tanah</label>
                        <input type="number" class="form-control" name="luas_tanah" required>
                    </div>
                    <div class="mb-3">
                        <label for="luas_bangunan" class="form-label">Luas Bangunan</label>
                        <input type="number" class="form-control" name="luas_bangunan" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Tanah">Tanah</option>
                            <option value="Dijual">Dijual</option>
                            <option value="Booked">Booked</option>
                            <option value="Terjual">Terjual</option>
                            <option value="Proses Pembangunan">Proses Pembangunan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="4" placeholder="Tambahkan deskripsi detail perumahan..."></textarea>
                        <small class="text-muted">Deskripsi detail tentang perumahan (fasilitas, lokasi, dll).</small>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar Perumahan</label>
                        <input type="file" class="form-control" name="gambar[]" id="gambar" accept="image/jpeg,image/jpg,image/png" multiple onchange="previewImage(event)">
                        <input type="hidden" name="existing_gambar" id="existingGambar" value="[]">
                        <div id="gambarList" class="gambar-list"></div>
                        <small class="text-muted">Bisa pilih lebih dari 1 foto. Format: JPG, JPEG, PNG. Maksimal 2MB per file, 8 foto.</small>
                    </div>
                    <div class="mb-3">
                        <label for="dokumen" class="form-label">Dokumen Perumahan</label>
                        <input type="file" class="form-control" name="dokumen" accept=".pdf,.doc,.docx">
                        <input type="hidden" name="existing_dokumen" id="existingDokumen">
                        <div id="dokumenInfo" class="mt-2" style="display: none;">
                            <div class="alert alert-info py-2">
                                <i class="fas fa-file-alt me-2"></i>
                                <span id="dokumenName"></span>
                                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeDokumen()">Hapus</button>
                                <a href="#" id="dokumenLink" target="_blank" class="btn btn-sm btn-primary ms-2">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                        <small class="text-muted">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
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

<!-- Modal Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah kamu yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
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
                <p id="successMessage">Data berhasil dihapus!</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Gambar -->
<div class="modal fade" id="lihatGambarModal" tabindex="-1" aria-labelledby="lihatGambarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="lihatGambarLabel">📷 Gambar Perumahan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="lihat-gambar-stage">
                    <button type="button" class="lihat-gambar-nav prev" id="gambarPrevBtn" onclick="geserGambar(-1)" aria-label="Sebelumnya">
                        <span class="material-icons">chevron_left</span>
                    </button>
                    <img id="gambarPreview" src="" alt="Gambar Perumahan">
                    <button type="button" class="lihat-gambar-nav next" id="gambarNextBtn" onclick="geserGambar(1)" aria-label="Berikutnya">
                        <span class="material-icons">chevron_right</span>
                    </button>
                </div>
                <div class="mt-2 text-muted" id="gambarCounter"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Bahan -->
<div class="modal fade" id="lihatBahanModal" tabindex="-1" aria-labelledby="lihatBahanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="lihatBahanLabel">Detail Bahan Pembangunan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="lihatBahanContent">
                <p>Loading data...</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/datarumah.js') ?>"></script>
<?= $this->endSection() ?>
