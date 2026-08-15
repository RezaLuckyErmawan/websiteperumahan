<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<style>
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
?>

<table id="pembelianListTable" class="display table table-striped table-bordered w-100">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Rumah</th>
            <th>Nama Customer</th>
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
                <td><span class="code-pill"><?= esc($p['kode_rumah']) ?></span></td>
                <td>
                    <div class="customer-cell">
                        <strong><?= esc($p['nama_customer']) ?></strong>
                        <small><?= esc($p['telepon_customer']) ?></small>
                    </div>
                </td>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#pembelianListTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            language: {
                emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                lengthMenu: "Tampilkan _MENU_ entri",
                loadingRecords: "Sedang memuat...",
                processing: "Sedang memproses...",
                search: "Cari:",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
