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
        'dokumen',
        'deskripsi',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;

    public static function parseGambar($raw): array
    {
        if (is_array($raw)) {
            $items = $raw;
        } else {
            $value = trim((string) $raw);
            if ($value === '') {
                return [];
            }

            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = [$value];
            }
        }

        $paths = [];
        foreach ($items as $item) {
            $path = trim((string) $item);
            if ($path !== '') {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    public static function encodeGambar(array $paths): ?string
    {
        $paths = self::parseGambar($paths);
        if ($paths === []) {
            return null;
        }

        if (count($paths) === 1) {
            return $paths[0];
        }

        return json_encode($paths, JSON_UNESCAPED_SLASHES);
    }

    public static function gambarUrls($raw): array
    {
        $urls = [];
        foreach (self::parseGambar($raw) as $path) {
            if (preg_match('~^https?://~i', $path)) {
                $urls[] = $path;
                continue;
            }

            $urls[] = '/' . ltrim($path, '/');
        }

        return $urls;
    }
}
