<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .content1 {
        padding: 20px;
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }

    .card {
        flex: 1 1 250px;
        background: #f5f5f5;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .card:hover {
        transform: scale(1.03);
    }

    .card h3 {
        margin-bottom: 10px;
        font-size: 20px;
        color: #333;
    }

    .card p {
        font-size: 16px;
        color: #666;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    /** @var int|float|string $jumlahrumah */
    /** @var int|float|string $jumlahcustomer */
    /** @var int|float|string $totalpembelian */
    /** @var int|float|string $stoktotal */
    $jumlahrumah = $jumlahrumah ?? 0;
    $jumlahcustomer = $jumlahcustomer ?? 0;
    $totalpembelian = $totalpembelian ?? 0;
    $stoktotal = $stoktotal ?? 0;
?>

<div class="card-container">
    <div class="card">
        <i class="fas fa-house fa-2x text-success mb-2"></i>
        <h3>Jumlah Rumah</h3>
        <p><?= esc($jumlahrumah) ?> unit</p>
    </div>
    <div class="card">
        <i class="fas fa-users fa-2x text-primary mb-2"></i>
        <h3>Customer Terdaftar</h3>
        <p><?= esc($jumlahcustomer) ?> Orang</p>
    </div>
    <div class="card">
        <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
        <h3>Hasil Penjualan Rumah</h3>
        <p>Rp <?= number_format($totalpembelian, 0, ',', '.') ?></p>
    </div>
    <div class="card">
        <i class="fas fa-boxes fa-2x text-danger mb-2"></i>
        <h3>Stok Bahan Bangunan</h3>
        <p><?= number_format($stoktotal, 0, ',', '.') ?> stok</p>
    </div>
</div>

<div class="mt-4">
    <h4 class="mt-5">Grafik Penjualan Rumah per Bulan</h4>
    <div style="max-width: 100%; height: 400px;">
        <canvas id="salesChart"></canvas>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.onload = function () {
        $.get('/chart-penjualan-rumah', function (data) {
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Unit Terjual',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Rumah'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            }
                        }
                    }
                }
            });
        });
    };
</script>
<?= $this->endSection() ?>