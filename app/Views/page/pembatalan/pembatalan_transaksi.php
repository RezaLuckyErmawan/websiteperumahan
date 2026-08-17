<?php
$pageTitle = 'Pembatalan Transaksi';
$useDataTables = true;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .pembatalan-page {
        display: grid;
        gap: 16px;
    }

    .pembatalan-card {
        background: #fff;
        border: 1px solid #e4e8ef;
        border-radius: 14px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        padding: 18px;
    }

    .pembatalan-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .pembatalan-title {
        margin: 0;
        color: #172033;
        font-size: 20px;
        font-weight: 800;
    }

    .pembatalan-subtitle {
        margin: 4px 0 0;
        color: #647084;
        font-size: 13px;
    }

    #dataPembatalanTable thead th {
        background-color: #eef6f8;
        color: #203246;
        text-align: center;
        vertical-align: middle;
    }

    #dataPembatalanTable {
        width: 100% !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="pembatalan-page">
    <div class="pembatalan-card">
        <div class="pembatalan-header">
            <div>
                <h1 class="pembatalan-title">Pembatalan Transaksi</h1>
                <p class="pembatalan-subtitle">Daftar transaksi pembelian rumah yang dibatalkan beserta keterangannya.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="dataPembatalanTable" class="display table table-striped table-bordered align-middle w-100">
                <thead>
                    <tr>
                        <th>Kode Rumah</th>
                        <th>Nama</th>
                        <th>Harga Beli</th>
                        <th>Keterangan</th>
                        <th>Tanggal Pembelian</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/pembatalantransaksi.js') ?>"></script>
<?= $this->endSection() ?>
