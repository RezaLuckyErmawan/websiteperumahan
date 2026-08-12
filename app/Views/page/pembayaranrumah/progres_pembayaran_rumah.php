<?php $this->extend('layouts/main') ?>

<?php $this->section('styles') ?>
<style>
  #progresPembayaranTable thead th {
    background-color: #eef6f8;
    color: #203246;
    text-align: center;
  }

  .progress-payment {
    min-width: 150px;
  }

  .progress-payment-track {
    height: 8px;
    overflow: hidden;
    border-radius: 999px;
    background: #e2e8f0;
  }

  .progress-payment-fill {
    height: 100%;
    border-radius: 999px;
    background: #2563eb;
  }

  .progress-payment-text {
    display: block;
    margin-top: 5px;
    color: #647084;
    font-size: 12px;
    font-weight: 800;
  }
</style>
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<table id="progresPembayaranTable" class="display table table-striped table-bordered w-100">
  <thead>
    <tr>
      <th>Customer</th>
      <th>Kode Rumah</th>
      <th>Harga</th>
      <th>Total Dibayar</th>
      <th>Sisa Tagihan</th>
      <th>Progress</th>
      <th>Status</th>
      <th>Metode</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>
<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url('assets/js/progrespembayaranrumah.js') ?>"></script>
<?php $this->endSection() ?>
