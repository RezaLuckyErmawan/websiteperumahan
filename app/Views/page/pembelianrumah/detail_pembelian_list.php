<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

<style>
    .purchase-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .purchase-summary-item {
        padding: 14px;
        border: 1px solid #e4e8ef;
        border-radius: 8px;
        background: #f8fafc;
    }

    .purchase-summary-item span {
        display: block;
        margin-bottom: 6px;
        color: #647084;
        font-size: 12px;
        font-weight: 800;
    }

    .purchase-summary-item strong {
        color: #172033;
        font-size: 16px;
        font-weight: 800;
    }

    #pembelianListTable thead th {
        background-color: #eef6f8;
        color: #203246;
        text-align: center;
    }

    #pembelianListTable td {
        vertical-align: middle;
    }

    #pembelianListTable td:last-child {
        text-align: center;
    }

    .customer-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .customer-cell strong {
        color: #172033;
        font-weight: 700;
    }

    .customer-cell small {
        color: #647084;
    }

    .code-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #334155;
        font-size: 12px;
        font-weight: 800;
    }

    .amount-paid {
        color: #059669;
        font-weight: 700;
    }

    .amount-remain {
        color: #dc2626;
        font-weight: 700;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 62px;
        min-height: 24px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
    }

    .status-success {
        background: #d1fae5;
        color: #047857;
    }

    .status-primary {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-secondary {
        background: #e2e8f0;
        color: #334155;
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

    @media (max-width: 992px) {
        .purchase-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .purchase-summary {
            grid-template-columns: 1fr;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
/**
 * Data dari PembelianRumahController::detailPembelianList()
 *
 * @var array<int, array<string, mixed>> $pembelian
 */
if (!isset($pembelian) || !is_array($pembelian)) {
    $pembelian = [];
}

$totalPembelian = count($pembelian);
$totalLunas = count(array_filter($pembelian, static fn($p) => strtolower((string) ($p['status_pembelian'] ?? '')) === 'lunas'));
$totalCicilan = count(array_filter($pembelian, static fn($p) => in_array(strtolower((string) ($p['status_pembelian'] ?? '')), ['cicil', 'dp'], true)));
$totalOmzet = array_sum(array_column($pembelian, 'harga_beli'));
?>

<div class="purchase-summary">
    <div class="purchase-summary-item">
        <span>Total Pembelian</span>
        <strong><?= $totalPembelian ?></strong>
    </div>
    <div class="purchase-summary-item">
        <span>Total Lunas</span>
        <strong><?= $totalLunas ?></strong>
    </div>
    <div class="purchase-summary-item">
        <span>Sedang Cicilan</span>
        <strong><?= $totalCicilan ?></strong>
    </div>
    <div class="purchase-summary-item">
        <span>Total Omzet</span>
        <strong>Rp <?= number_format($totalOmzet, 0, ',', '.') ?></strong>
    </div>
</div>

<table id="pembelianListTable" class="display table table-striped table-bordered w-100">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Customer</th>
            <th>Kode Rumah</th>
            <th>Tipe</th>
            <th>Tanggal Pembelian</th>
            <th>Harga Beli</th>
            <th>Total Dibayar</th>
            <th>Sisa Tagihan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($pembelian as $p): ?>
            <?php
                $status = strtolower($p['status_pembelian']);
                $statusClass = 'status-secondary';
                if ($status === 'lunas') $statusClass = 'status-success';
                elseif ($status === 'cicil') $statusClass = 'status-primary';
                elseif ($status === 'dp') $statusClass = 'status-warning';
                elseif ($status === 'batal') $statusClass = 'status-danger';
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <div class="customer-cell">
                        <strong><?= esc($p['nama_customer']) ?></strong>
                        <small><?= esc($p['telepon_customer']) ?></small>
                    </div>
                </td>
                <td><span class="code-pill"><?= esc($p['kode_rumah']) ?></span></td>
                <td><?= esc($p['tipe_rumah']) ?></td>
                <td><?= date('d-m-Y', strtotime($p['tanggal_pembelian'])) ?></td>
                <td>Rp <?= number_format($p['harga_beli'], 0, ',', '.') ?></td>
                <td class="amount-paid">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                <td class="amount-remain">Rp <?= number_format($p['sisa_bayar'], 0, ',', '.') ?></td>
                <td><span class="status-pill <?= $statusClass ?>"><?= ucfirst($p['status_pembelian']) ?></span></td>
                <td>
                    <div class="payment-actions">
                        <a href="/detail-pembelian-rumah/<?= $p['id'] ?>" class="btn btn-sm btn-primary" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#pembelianListTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
</script>
<?= $this->endSection() ?>
