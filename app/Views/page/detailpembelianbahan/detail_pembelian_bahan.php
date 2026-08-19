<?php
$pageTitle = 'Detail Pembelian Bahan';
$useDataTables = true;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
  #detailPembelianTable thead th {
    background-color: #eef6f8;
    color: #203246;
    text-align: center;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
  /** @var list<array<string, mixed>> $pembelian */
  /** @var list<array<string, mixed>> $bahan */
  /** @var list<array<string, mixed>> $detailpembelian */
  $pembelian = is_array($pembelian ?? null) ? $pembelian : [];
  $bahan = is_array($bahan ?? null) ? $bahan : [];
  $detailpembelian = is_array($detailpembelian ?? null) ? $detailpembelian : [];
?>

<table id="detailPembelianTable" class="display table table-bordered w-100">
  <thead>
    <tr>
      <th>Nomor Nota</th>
      <th>Nama Bahan</th>
      <th>Jumlah</th>
      <th>Harga Satuan</th>
      <th>Subtotal</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($detailpembelian)): ?>
      <tr>
        <td colspan="6" class="text-center text-muted">Belum ada data detail pembelian bahan.</td>
      </tr>
    <?php else: ?>
      <?php foreach ($detailpembelian as $row): ?>
        <tr>
          <td><?= esc($row['nomor_nota'] ?? '-') ?></td>
          <td><?= esc($row['nama_bahan'] ?? '-') ?></td>
          <td><?= esc($row['jumlah'] ?? '-') ?></td>
          <td>Rp <?= number_format((float) ($row['harga_satuan'] ?? 0), 0, ',', '.') ?></td>
          <td>Rp <?= number_format((float) ($row['subtotal'] ?? 0), 0, ',', '.') ?></td>
          <td class="text-nowrap">
            <button type="button" class="btn btn-sm btn-primary" onclick="editData(<?= (int) ($row['id'] ?? 0) ?>)">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusData(<?= (int) ($row['id'] ?? 0) ?>)">
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
<div class="modal fade" id="modalDetailForm" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formDetailPembelian">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDetailLabel">Tambah Detail Pembelian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="mb-2">
            <label for="pembelian_id">Nomor Nota</label>
            <select class="form-control" name="pembelian_id" id="pembelian_id">
              <?php foreach ($pembelian as $p): ?>
                <option value="<?= $p['id'] ?>"><?= esc($p['nomor_nota']) ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="mb-2">
            <label for="bahan_bangunan_id">Nama Bahan</label>
            <select class="form-control" name="bahan_bangunan_id" id="bahan_bangunan_id">
              <?php foreach ($bahan as $b): ?>
                <option value="<?= $b['id'] ?>"><?= esc($b['nama_bahan']) ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="mb-2">
            <label for="jumlah">Jumlah</label>
            <input type="number" class="form-control" name="jumlah" id="jumlah" required>
          </div>
          <div class="mb-2">
            <label for="harga_satuan">Harga Satuan</label>
            <input type="number" class="form-control" name="harga_satuan" id="harga_satuan" required>
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
<script src="<?= base_url('assets/js/detailpembelianbahan.js') ?>"></script>
<?= $this->endSection() ?>
