<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranRumahModel extends Model
{
    protected $table      = 'pembayaran_rumah';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'pembelian_rumah_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'jenis_pembayaran',
        'metode_bayar',
        'keterangan',
        'bukti_bayar',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
}
