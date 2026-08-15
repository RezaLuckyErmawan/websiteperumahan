<?php

namespace App\Controllers;

use App\Models\CustomerModel;
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
        $transaksiModel = new TransaksiRumahModel();
        $rumah = $perumahanModel->find($id);

        if (!$rumah) {
            return redirect()->to('/perumahan/data-rumah')->with('error', 'Data rumah tidak ditemukan.');
        }

        if (!$this->rumahDapatCheckout($rumah) || $transaksiModel->where('perumahan_id', $id)->first()) {
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

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $transaksiModel->insert([
                'perumahan_id' => (int) $id,
                'user_id' => session()->get('user_id'),
                'nama' => $data['nama'],
                'telepon' => $data['telepon'],
                'email' => $data['email'],
                'alamat' => $data['alamat'],
                'status' => 'booked',
                'status_berkas' => 'pending',
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

        return redirect()->to('/perumahan/rumah-booking')->with('success', 'Checkout berhasil. Rumah berstatus Booked.');
    }

    public function rumahBooking()
    {
        $transaksiModel = new TransaksiRumahModel();
        $userId = session()->get('user_id');

        $booking = $transaksiModel
            ->select('transaksi_rumah.*, perumahan.kode_rumah, perumahan.tipe, perumahan.lokasi, perumahan.harga, perumahan.gambar, perumahan.status AS status_rumah')
            ->join('perumahan', 'perumahan.id = transaksi_rumah.perumahan_id')
            ->where('transaksi_rumah.user_id', $userId)
            ->orderBy('transaksi_rumah.created_at', 'DESC')
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
        if ($info['kedaluwarsa'] && !$info['wajib_terisi']) {
            return redirect()->to('/perumahan/rumah-booking/' . $id . '/berkas')
                ->with('error', 'Batas waktu 7 hari untuk melengkapi berkas sudah habis.');
        }

        $uploaded = $info['uploaded'];
        $adaFile = false;

        foreach (array_keys(TransaksiRumahModel::JENIS_BERKAS) as $key) {
            $path = $this->uploadBerkasFile($key);
            if (is_array($path) && ($path['status'] ?? '') === 'error') {
                return redirect()->to('/perumahan/rumah-booking/' . $id . '/berkas')
                    ->with('error', $path['message'])
                    ->withInput();
            }
            if (is_string($path) && $path !== '') {
                $adaFile = true;
                $uploaded[$key] = $path;
            }
        }

        if (!$adaFile) {
            return redirect()->to('/perumahan/rumah-booking/' . $id . '/berkas')
                ->with('error', 'Pilih minimal satu berkas untuk diunggah.');
        }

        $statusBerkas = 'pending';
        $wajibTerisi = true;
        foreach (TransaksiRumahModel::JENIS_BERKAS as $key => $meta) {
            if ($meta['wajib'] && empty($uploaded[$key])) {
                $wajibTerisi = false;
                break;
            }
        }
        if ($wajibTerisi) {
            $statusBerkas = 'lengkap';
        }

        $model = new TransaksiRumahModel();
        $model->update($id, [
            'berkas' => json_encode($uploaded),
            'status_berkas' => $statusBerkas,
        ]);

        $pesan = $statusBerkas === 'lengkap'
            ? 'Semua berkas wajib berhasil dilengkapi.'
            : 'Berkas berhasil diunggah. Lengkapi sisa dokumen sebelum batas 7 hari.';

        return redirect()->to('/perumahan/rumah-booking/' . $id . '/berkas')->with('success', $pesan);
    }

    private function bookingMilikUser($id): ?array
    {
        $model = new TransaksiRumahModel();
        $transaksi = $model
            ->select('transaksi_rumah.*, perumahan.kode_rumah, perumahan.tipe, perumahan.lokasi')
            ->join('perumahan', 'perumahan.id = transaksi_rumah.perumahan_id')
            ->where('transaksi_rumah.id', $id)
            ->where('transaksi_rumah.user_id', session()->get('user_id'))
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
