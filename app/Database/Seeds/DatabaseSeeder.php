<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('PerumahanSeeder');
        $this->call('CustomerSeeder');
        $this->call('UserSeeder');
        $this->call('PembelianRumahSeeder');
        $this->call('PembayaranRumahSeeder');
        $this->call('BahanBangunanSeeder');
        $this->call('PembelianBahanSeeder');
        $this->call('PekerjaanInsidentilSeeder');
    }
}
