<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Dompdf\Dompdf;

class LaporanController extends BaseController
{
    public function index()
    {
        $data = [
            'userRole' => session()->get('role'),
            'penjualan' => $this->getPenjualanData(),
            'cicilan' => $this->getCicilanData(),
        ];

        return view('page/laporan/laporan', $data);
    }

    public function pdfPenjualan()
    {
        $penjualan = $this->getPenjualanData();
        $html = view('page/laporan/cetak_penjualan', [
            'penjualan' => $penjualan,
            'tanggalCetak' => date('d-m-Y H:i'),
        ]);

        return $this->streamPdf($html, 'Laporan_Penjualan_Rumah.pdf');
    }

    public function pdfCicilan()
    {
        $cicilan = $this->getCicilanData();
        $html = view('page/laporan/cetak_cicilan', [
            'cicilan' => $cicilan,
            'tanggalCetak' => date('d-m-Y H:i'),
        ]);

        return $this->streamPdf($html, 'Laporan_Cicilan_Rumah.pdf');
    }

    private function getPenjualanData(): array
    {
        $db = \Config\Database::connect();

        return $db->table('pembelian_rumah pr')
            ->select('
                pr.*,
                customer.nama as nama_customer,
                customer.telepon as telepon_customer,
                perumahan.kode_rumah,
                perumahan.tipe as tipe_rumah,
                perumahan.lokasi as lokasi_rumah
            ')
            ->join('customer', 'customer.id = pr.customer_id')
            ->join('perumahan', 'perumahan.id = pr.perumahan_id')
            ->where("LOWER(pr.status_pembelian) != 'batal'", null, false)
            ->orderBy('pr.tanggal_pembelian', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function getCicilanData(): array
    {
        $db = \Config\Database::connect();
        $hasStatusPengajuan = $db->fieldExists('status_pengajuan', 'pembayaran_rumah');

        $totalBayarSql = $hasStatusPengajuan
            ? "(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah WHERE status_pengajuan = 'disetujui' GROUP BY pembelian_rumah_id)"
            : '(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah GROUP BY pembelian_rumah_id)';

        return $db->table('pembelian_rumah pr')
            ->select('
                pr.*,
                customer.nama as nama_customer,
                customer.telepon as telepon_customer,
                perumahan.kode_rumah,
                perumahan.tipe as tipe_rumah,
                COALESCE(total_bayar.total_bayar, 0) AS total_bayar,
                (pr.harga_beli - COALESCE(total_bayar.total_bayar, 0)) AS sisa_bayar
            ')
            ->join('customer', 'customer.id = pr.customer_id')
            ->join('perumahan', 'perumahan.id = pr.perumahan_id')
            ->join($totalBayarSql . ' total_bayar', 'total_bayar.pembelian_rumah_id = pr.id', 'left')
            ->where("LOWER(pr.status_pembelian) IN ('cicil', 'dp')", null, false)
            ->orderBy('pr.tanggal_pembelian', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function streamPdf(string $html, string $filename)
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }
}
