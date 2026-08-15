<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class TransaksiRumahModel extends Model
{
    public const BATAS_HARI_BERKAS = 7;

    public const JENIS_BERKAS = [
        'ktp' => [
            'label' => 'Fotokopi Kartu Tanda Penduduk (e-KTP)',
            'wajib' => true,
            'keterangan' => 'Wajib. Jika alamat tinggal tidak sesuai KTP, sertakan juga surat domisili.',
        ],
        'surat_domisili' => [
            'label' => 'Surat domisili',
            'wajib' => false,
            'keterangan' => 'Wajib jika alamat tempat tinggal tidak sesuai alamat KTP.',
        ],
        'kk' => [
            'label' => 'Fotokopi Kartu Keluarga (KK)',
            'wajib' => true,
            'keterangan' => 'Wajib.',
        ],
        'surat_nikah' => [
            'label' => 'Fotokopi surat nikah/cerai',
            'wajib' => true,
            'keterangan' => 'Wajib.',
        ],
        'npwp' => [
            'label' => 'Fotokopi NPWP/SPT PPh 21 (pajak)',
            'wajib' => true,
            'keterangan' => 'Wajib.',
        ],
        'surat_pernyataan' => [
            'label' => 'Surat pernyataan tidak memiliki rumah',
            'wajib' => true,
            'keterangan' => 'Diketahui instansi tempat bekerja atau lurah tempat KTP diterbitkan.',
        ],
        'slip_gaji' => [
            'label' => 'Slip gaji terakhir / surat keterangan penghasilan',
            'wajib' => true,
            'keterangan' => 'Ditandatangani pemohon di atas materai dan diketahui pimpinan instansi atau kepala desa/lurah untuk penghasilan tidak tetap.',
        ],
        'surat_kerja' => [
            'label' => 'Fotokopi surat keterangan pengangkatan pegawai tetap / surat keterangan kerja',
            'wajib' => false,
            'keterangan' => 'Wajib apabila pemohon bekerja di instansi.',
        ],
    ];

    protected $table = 'transaksi_rumah';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'perumahan_id',
        'user_id',
        'nama',
        'telepon',
        'email',
        'alamat',
        'berkas',
        'status_berkas',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_at',
        'verified_by',
        'pembelian_rumah_id',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;

    public static function decodeBerkas(?string $json): array
    {
        $data = json_decode((string) $json, true);
        return is_array($data) ? $data : [];
    }

    public static function infoBerkas(array $transaksi): array
    {
        $uploaded = self::decodeBerkas($transaksi['berkas'] ?? null);
        $created = $transaksi['created_at'] ?? date('Y-m-d H:i:s');
        $deadline = (new DateTime($created))->modify('+' . self::BATAS_HARI_BERKAS . ' days');
        $now = new DateTime();
        $sisaDetik = $deadline->getTimestamp() - $now->getTimestamp();
        $sisaHari = (int) max(0, ceil($sisaDetik / 86400));
        $kedaluwarsa = $sisaDetik <= 0;

        $wajibTerisi = true;
        foreach (self::JENIS_BERKAS as $key => $meta) {
            if ($meta['wajib'] && empty($uploaded[$key])) {
                $wajibTerisi = false;
                break;
            }
        }

        $status = strtolower((string) ($transaksi['status_berkas'] ?? 'pending'));
        if ($wajibTerisi) {
            $status = 'lengkap';
        } elseif ($kedaluwarsa) {
            $status = 'kedaluwarsa';
        }

        return [
            'uploaded' => $uploaded,
            'deadline' => $deadline->format('Y-m-d H:i:s'),
            'deadline_display' => $deadline->format('d-m-Y'),
            'sisa_hari' => $sisaHari,
            'kedaluwarsa' => $kedaluwarsa,
            'dapat_unggah' => !$kedaluwarsa && $status !== 'lengkap',
            'wajib_terisi' => $wajibTerisi,
            'status' => $status,
        ];
    }
}
