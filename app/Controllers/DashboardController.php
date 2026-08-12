<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BahanBangunanModel;
use App\Models\CustomerModel;
use App\Models\PembelianBahanModel;
use App\Models\PembelianRumahModel;
use App\Models\PerumahanModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
   public function index()
{   
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $perumahan = new PerumahanModel();
    $customer = new CustomerModel();
    $pembelianrumah = new PembelianRumahModel();
    $bahanbangunan  = new BahanBangunanModel();

    $jumlahcustomer = $customer->countAll();
    $jumlahrumah = $perumahan->countAll();

    // Hitung total pembelian tanpa status batal
    $totalharga = $pembelianrumah
        ->where('status_pembelian !=', 'batal')
        ->selectSum('harga_beli')
        ->first();
    $totalpembelian = $totalharga['harga_beli'] ?? 0;

    $jumlahbahan  = $bahanbangunan->selectSum('stok')->first();
    $stoktotal    = $jumlahbahan['stok'] ?? 0;

    return view('page/dashboard', [
        'pageTitle'       => 'Dashboard',
        'jumlahrumah'     => $jumlahrumah,
        'jumlahcustomer'  => $jumlahcustomer,
        'totalpembelian'  => $totalpembelian,
        'stoktotal'       => $stoktotal,
    ]);
}

    public function landing() {
        return view('page/landingpage');
    }


    public function datapembelianbahan() {
        return view('page/data_pembelian_bahan');
    }
    public function databahanpembangunan() {
        return view('page/data_bahan_pembangunan');
    }

    public function datacustomer() {
        return view('page/datacustomer');
    }
}

