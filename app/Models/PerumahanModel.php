<?php

namespace App\Models;

use CodeIgniter\Model;

class PerumahanModel extends Model
{
    protected $table      = 'perumahan';         
    protected $primaryKey = 'id';                 

    protected $allowedFields = [
        'kode_rumah',
        'lokasi',
        'tipe',
        'luas_tanah',
        'luas_bangunan',
        'harga',
        'status',
        'gambar',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;               
}
