<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\PembelianRumahModel;
use App\Models\PerumahanModel;
use App\Models\TransaksiRumahModel;

class PerumahanCustomerController extends BaseController
{
    public function dataRumah()
    {
        $model = new PerumahanModel();

        return view('page/perumahan/data_rumah', [
            'pageTitle' => 'Data Rumah',
            'rumah' => $model->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function detailRumah($id)
    {
        $model = new PerumahanModel();
        $rumah = $model->find($id);

        if (!$rumah) {
            return redirect()->to('/perumahan/data-rumah')->with('error', 'Data rumah tidak ditemukan.');
        }

        $customer = $this->currentCustomer();

        return view('page/perumahan/detail_rumah', [
            'pageTitle' => 'Detail Rumah',
            'rumah' => $rumah,
            'dapatCheckout' => $this->rumahDapatCheckout($rumah),
            'customer' => $customer,
        ]);
    }

    public function checkout($id)
    {
        $perumahanModel = new PerumahanModel();
        $rumah = $perumahanModel->find($id);

        if (!$rumah) {
            return redirect()->to('/perumahan/data-rumah')->with('error', 'Data rumah tidak ditemukan.');
        }

        if (!$this->rumahDapatCheckout($rumah)) {
            return redirect()->to('/perumahan/data-rumah/' . $id)
                ->with('error', 'Rumah ini sudah di-booking dan tidak dapat di-checkout user lain.');
        }

        $pembelianModel = new PembelianRumahModel();
        $bookingAktif = $pembelianModel
            ->where('perumahan_id', $id)
            ->whereIn('status_verifikasi', ['pending', 'disetujui', 'tidak_perlu'])
            ->where('status_pembelian !=', 'Batal')
            ->first();
        if ($bookingAktif) {
            return redirect()->to('/perumahan/data-rumah/' . $id)
                ->with('error', 'Rumah ini sudah di-booking dan tidak dapat di-checkout user lain.');
        }

        $data = [
            'nama' => trim((string) $this->request->getPost('nama')),
            'telepon' => trim((string) $this->request->getPost('telepon')),
            'email' => trim((string) $this->request->getPost('email')),
            'alamat' => trim((string) $this->request->getPost('alamat')),
        ];

        if ($data['nama'] === '' || $data['telepon'] === '' || $data['email'] === '' || $data['alamat'] === '') {
            return redirect()->to('/perumahan/data-rumah/' . $id)
                ->with('error', 'Nama, No. Telp, Email, dan Alamat wajib diisi.')
                ->withInput();
        }

        $lamaCicilan = (int) $this->request->getPost('lama_cicilan_tahun');
        if ($lamaCicilan < 1 || $lamaCicilan > 30) {
            $lamaCicilan = 5;
        }
        $tanggalCicilan = trim((string) $this->request->getPost('tanggal_cicilan'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalCicilan)) {
            $tanggalCicilan = date('Y-m-d', strtotime('+1 month'));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $customerModel = new CustomerModel();
            $customer = $customerModel->where('email', $data['email'])->first();
            if (!$customer) {
                $customerModel->insert([
                    'nama' => $data['nama'],
                    'telepon' => $data['telepon'],
                    'email' => $data['email'],
                    'alamat' => $data['alamat'],
                    'perumahan_id' => (int) $id,
                    'tanggal_pembelian' => date('Y-m-d'),
                ]);
                $customerId = (int) $customerModel->getInsertID();
            } else {
                $customerId = (int) $customer['id'];
                $customerModel->update($customerId, [
                    'nama' => $data['nama'],
                    'telepon' => $data['telepon'],
                    'alamat' => $data['alamat'],
                    'perumahan_id' => (int) $id,
                ]);
            }

            $pembelianModel->insert([
                'customer_id' => $customerId,
                'perumahan_id' => (int) $id,
                'tanggal_pembelian' => date('Y-m-d'),
                'harga_beli' => $rumah['harga'] ?? 0,
                'status_pembelian' => 'Booking',
                'metode_pembayaran' => 'Cicilan Internal',
                'lama_cicilan_tahun' => $lamaCicilan,
                'tanggal_cicilan' => $tanggalCicilan,
                'status_dokumen' => 'Pending',
                'sumber' => 'customer',
                'user_id' => session()->get('user_id'),
                'status_berkas' => 'pending',
                'status_verifikasi' => 'pending',
            ]);
            $perumahanModel->update($id, ['status' => 'Booked']);
            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/perumahan/data-rumah/' . $id)
                ->with('error', 'Checkout gagal. Rumah mungkin sudah di-booking user lain.');
        }

        if ($db->transStatus() === false) {
            return redirect()->to('/perumahan/data-rumah/' . $id)
                ->with('error', 'Checkout gagal. Rumah mungkin sudah di-booking user lain.');
        }

        return redirect()->to('/dashboard')->with('success', 'Checkout berhasil. Rumah berstatus Booked.');
    }

    public function rumahBooking()
    {
        $pembelianModel = new PembelianRumahModel();
        $userId = session()->get('user_id');

        $booking = $pembelianModel
            ->select('pembelian_rumah.*, customer.nama, customer.telepon, perumahan.kode_rumah, perumahan.tipe, perumahan.lokasi, perumahan.harga, perumahan.gambar, perumahan.status AS status_rumah')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->where('pembelian_rumah.user_id', $userId)
            ->where('pembelian_rumah.sumber', 'customer')
            ->orderBy('pembelian_rumah.created_at', 'DESC')
            ->findAll();

        foreach ($booking as &$item) {
            $item['info_berkas'] = TransaksiRumahModel::infoBerkas($item);
        }
        unset($item);

        return view('page/perumahan/rumah_booking', [
            'pageTitle' => 'Rumah yang booking',
            'booking' => $booking,
        ]);
    }

    public function formBerkas($id)
    {
        $transaksi = $this->bookingMilikUser($id);
        if (!$transaksi) {
            return redirect()->to('/perumahan/rumah-booking')->with('error', 'Data booking tidak ditemukan.');
        }

        $info = TransaksiRumahModel::infoBerkas($transaksi);

        return view('page/perumahan/lengkapi_berkas', [
            'pageTitle' => 'Lengkapi Berkas',
            'transaksi' => $transaksi,
            'info' => $info,
            'jenisBerkas' => TransaksiRumahModel::JENIS_BERKAS,
        ]);
    }

    public function unggahBerkas($id)
    {
        $transaksi = $this->bookingMilikUser($id);
        if (!$transaksi) {
            return redirect()->to('/perumahan/rumah-booking')->with('error', 'Data booking tidak ditemukan.');
        }

        $info = TransaksiRumahModel::infoBerkas($transaksi);
        $split = TransaksiRumahModel::splitBerkas($transaksi['berkas'] ?? null);
        $files = $split['files'];
        $verifikasi = $split['verifikasi'];

        if ($info['kedaluwarsa']) {
            $bolehPerbarui = false;
            foreach (array_keys(TransaksiRumahModel::JENIS_BERKAS) as $key) {
                $file = $this->request->getFile($key);
                if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE && (($verifikasi[$key] ?? '') === 'ditolak')) {
                    $bolehPerbarui = true;
                    break;
                }
            }
            if (!$bolehPerbarui && !$info['wajib_terisi']) {
                return redirect()->to('/dashboard')
                    ->with('error', 'Batas waktu 7 hari untuk melengkapi berkas sudah habis.');
            }
        }

        $adaFile = false;

        foreach (array_keys(TransaksiRumahModel::JENIS_BERKAS) as $key) {
            $path = $this->uploadBerkasFile($key);
            if (is_array($path) && ($path['status'] ?? '') === 'error') {
                return redirect()->to('/dashboard')
                    ->with('error', $path['message'])
                    ->withInput();
            }
            if (is_string($path) && $path !== '') {
                $adaFile = true;
                $files[$key] = $path;
                $verifikasi[$key] = 'pending';
            }
        }

        if (!$adaFile) {
            return redirect()->to('/dashboard')
                ->with('error', 'Pilih minimal satu berkas untuk diunggah.');
        }

        $statusBerkas = 'pending';
        $wajibTerisi = true;
        foreach (TransaksiRumahModel::JENIS_BERKAS as $key => $meta) {
            if ($meta['wajib'] && empty($files[$key])) {
                $wajibTerisi = false;
                break;
            }
        }
        if ($wajibTerisi) {
            $statusBerkas = 'lengkap';
        }

        $model = new PembelianRumahModel();
        $model->update($id, [
            'berkas' => TransaksiRumahModel::encodeBerkas($files, $verifikasi),
            'status_berkas' => $statusBerkas,
        ]);

        $pesan = $statusBerkas === 'lengkap'
            ? 'Semua berkas wajib berhasil dilengkapi.'
            : 'Berkas berhasil diunggah. Lengkapi sisa dokumen sebelum batas 7 hari.';

        return redirect()->to('/dashboard')->with('success', $pesan);
    }

    private function bookingMilikUser($id): ?array
    {
        $model = new PembelianRumahModel();
        $transaksi = $model
            ->select('pembelian_rumah.*, perumahan.kode_rumah, perumahan.tipe, perumahan.lokasi')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->where('pembelian_rumah.id', $id)
            ->groupStart()
                ->where('pembelian_rumah.user_id', session()->get('user_id'))
                ->orWhere('pembelian_rumah.customer_id', session()->get('customer_id') ?: 0)
            ->groupEnd()
            ->first();

        return $transaksi ?: null;
    }

    private function uploadBerkasFile(string $field)
    {
        $file = $this->request->getFile($field);
        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (!$file->isValid()) {
            return ['status' => 'error', 'message' => 'Upload ' . $field . ' gagal.'];
        }

        $validated = $this->validate([
            $field => [
                "uploaded[{$field}]",
                "mime_in[{$field},image/jpg,image/jpeg,image/png,application/pdf]",
                "max_size[{$field},5120]",
            ],
        ]);

        if (!$validated) {
            return ['status' => 'error', 'message' => implode(', ', $this->validator->getErrors())];
        }

        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'berkas_booking';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        return 'uploads/berkas_booking/' . $newName;
    }

    private function rumahDapatCheckout(array $rumah): bool
    {
        $status = strtolower((string) ($rumah['status'] ?? ''));

        return !in_array($status, ['booked', 'terjual'], true);
    }

    private function currentCustomer(): array
    {
        $customerId = session()->get('customer_id');
        if ($customerId) {
            $customer = (new CustomerModel())->find($customerId);
            if ($customer) {
                return $customer;
            }
        }

        return [
            'nama' => session()->get('nama') ?? '',
            'telepon' => '',
            'email' => '',
            'alamat' => '',
        ];
    }
}
