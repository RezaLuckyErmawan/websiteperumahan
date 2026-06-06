<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PekerjaanInsidentilSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'perumahan_id' => 19,
            'mandor_id' => 3,
            'tanggal' =>  '2025-09-15',
            'nama_pekerjaan' => 'Pemasangan Kanopi depan teras',
            'keterangan' => 'permintaan dari pembeli untuk pemasangan kanopi depan rumah',
            'total_biaya' => 1000000,
            'status' => 'pending',
            'approved_by' => null,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
        $this->db->table('pekerjaan_insidentil')->insert($data);
    }
}
