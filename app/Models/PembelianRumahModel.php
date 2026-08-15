<?php 

namespace App\Models;
use CodeIgniter\Model;

class PembelianRumahModel extends Model {
    protected $table = 'pembelian_rumah';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'customer_id',
        'perumahan_id',
        'tanggal_pembelian',
        'harga_beli',
        'status_pembelian',
        'metode_pembayaran',
        'lama_cicilan_tahun',
        'tanggal_cicilan',
        'catatan_marketing',
        'status_dokumen',
        'request_khusus',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;

    public static function jatuhTempoCicilan(?string $tanggalCicilan, ?string $tanggalPembelian, int $offsetBulan): ?string
    {
        $base = $tanggalCicilan ?: $tanggalPembelian;
        if (!$base) {
            return null;
        }

        $dt = date_create($base);
        if (!$dt) {
            return null;
        }

        $day = (int) $dt->format('j');
        $monthIndex = ((int) $dt->format('n') - 1) + max($offsetBulan, 0);
        $year = (int) $dt->format('Y') + intdiv($monthIndex, 12);
        $month = ($monthIndex % 12) + 1;
        $lastDay = (int) date('t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
        $day = min($day, $lastDay);

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    public static function annotatePembayaranCicilan(array $pembayaran, array $pembelian): array
    {
        $ordered = $pembayaran;
        usort($ordered, static function ($a, $b) {
            $cmp = strcmp((string) ($a['created_at'] ?? ''), (string) ($b['created_at'] ?? ''));
            if ($cmp !== 0) {
                return $cmp;
            }

            return ((int) ($a['id'] ?? 0)) <=> ((int) ($b['id'] ?? 0));
        });

        $nomor = 0;
        $map = [];
        foreach ($ordered as $row) {
            $jenis = strtolower((string) ($row['jenis_pembayaran'] ?? ''));
            $status = strtolower((string) ($row['status_pengajuan'] ?? ''));
            $cicilanKe = null;
            if ($jenis === 'cicilan' && $status !== 'ditolak') {
                $nomor++;
                $cicilanKe = $nomor;
            }
            $map[(int) ($row['id'] ?? 0)] = $cicilanKe;
        }

        foreach ($pembayaran as &$row) {
            $row['cicilan_ke'] = $map[(int) ($row['id'] ?? 0)] ?? null;
            $info = self::infoTampilanPembayaran(
                $row,
                $pembelian['tanggal_cicilan'] ?? null,
                $pembelian['tanggal_pembelian'] ?? null
            );
            $row = array_merge($row, $info);
        }
        unset($row);

        return $pembayaran;
    }

    public static function infoTampilanPembayaran(array $row, ?string $tanggalCicilan, ?string $tanggalPembelian): array
    {
        $jenis = strtolower((string) ($row['jenis_pembayaran'] ?? ''));
        $status = strtolower((string) ($row['status_pengajuan'] ?? ''));
        $cicilanKe = (int) ($row['cicilan_ke'] ?? 0);
        $labels = [
            'booking_fee' => 'Booking Fee',
            'dp' => 'DP',
            'cicilan' => 'Cicilan',
            'pelunasan' => 'Pelunasan',
        ];

        $infoJenis = $labels[$jenis] ?? ucfirst($jenis ?: '-');
        $jatuhTempo = null;
        if ($jenis === 'cicilan' && $status !== 'ditolak' && $cicilanKe > 0) {
            $infoJenis = 'Cicilan ke-' . $cicilanKe;
            $jatuhTempo = self::jatuhTempoCicilan($tanggalCicilan, $tanggalPembelian, $cicilanKe - 1);
        }

        $tanggalBayar = $row['tanggal_bayar'] ?? null;
        $pakaiJatuhTempo = empty($tanggalBayar) && !empty($jatuhTempo);
        $tanggalIso = $tanggalBayar ?: $jatuhTempo;

        return [
            'info_jenis' => $infoJenis,
            'jatuh_tempo' => $jatuhTempo,
            'info_tanggal' => $tanggalIso,
            'info_tanggal_display' => $tanggalIso ? date('d/m/Y', strtotime((string) $tanggalIso)) : '-',
            'is_jatuh_tempo' => $pakaiJatuhTempo,
        ];
    }
}