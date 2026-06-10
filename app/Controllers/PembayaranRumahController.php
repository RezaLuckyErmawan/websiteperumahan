<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\RawSql;
use App\Models\PembayaranRumahModel;
use App\Models\PembelianRumahModel;

class PembayaranRumahController extends BaseController
{
    public function pembayaranrumah()
    {
        $pembelianModel = new PembelianRumahModel();

        $pembelian = $pembelianModel
            ->select('pembelian_rumah.*, customer.nama AS nama_customer, perumahan.kode_rumah')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->where(new RawSql("LOWER(pembelian_rumah.status_pembelian) != 'batal'"))
            ->orderBy('pembelian_rumah.created_at', 'DESC')
            ->findAll();

        return view('page/pembayaranrumah/pembayaran_rumah', [
            'pembelian' => $pembelian,
            'selectedPembelianId' => $this->request->getGet('pembelian_id'),
        ]);
    }

    public function json()
    {
        $request = service('request');
        $db = \Config\Database::connect();

        $builder = $db->table('pembayaran_rumah pr')
            ->select('
                pr.*,
                pembelian_rumah.harga_beli,
                pembelian_rumah.status_pembelian,
                customer.nama AS nama_customer,
                perumahan.kode_rumah,
                COALESCE(total_bayar.total_bayar, 0) AS total_bayar,
                (pembelian_rumah.harga_beli - COALESCE(total_bayar.total_bayar, 0)) AS sisa_bayar
            ')
            ->join('pembelian_rumah', 'pembelian_rumah.id = pr.pembelian_rumah_id')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->join(
                '(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah GROUP BY pembelian_rumah_id) total_bayar',
                'total_bayar.pembelian_rumah_id = pr.pembelian_rumah_id',
                'left'
            );

        $searchValue = $request->getGet('search')['value'] ?? '';
        if ($searchValue) {
            $builder->groupStart()
                ->like('customer.nama', $searchValue)
                ->orLike('perumahan.kode_rumah', $searchValue)
                ->orLike('pr.jenis_pembayaran', $searchValue)
                ->orLike('pr.metode_bayar', $searchValue)
                ->orLike('pr.tanggal_bayar', $searchValue)
                ->groupEnd();
        }

        $total = $db->table('pembayaran_rumah')->countAll();
        $filtered = $builder->countAllResults(false);

        $start = (int) ($request->getGet('start') ?? 0);
        $length = (int) ($request->getGet('length') ?? 10);
        $data = $builder
            ->orderBy('pr.created_at', 'DESC')
            ->get($length, $start)
            ->getResultArray();

        return $this->response->setJSON([
            'draw' => (int) $request->getGet('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function ringkasan($pembelianId)
    {
        $summary = $this->getRingkasanPembayaran($pembelianId);
        if (!$summary) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Data pembelian tidak ditemukan']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $summary]);
    }

    public function edit($id)
    {
        $model = new PembayaranRumahModel();
        $data = $model->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    public function store()
    {
        return $this->savePembayaran();
    }

    public function update($id)
    {
        return $this->savePembayaran($id);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $model = new PembayaranRumahModel();
        $data = $model->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan']);
        }

        $db->transStart();
        $model->delete($id);
        $this->updateStatusPembelian($data['pembelian_rumah_id']);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Gagal menghapus pembayaran']);
        }

        $this->deleteBuktiBayar($data['bukti_bayar'] ?? null);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Pembayaran berhasil dihapus']);
    }

    private function savePembayaran(?int $id = null)
    {
        $db = \Config\Database::connect();
        $model = new PembayaranRumahModel();

        $pembelianId = (int) $this->request->getPost('pembelian_rumah_id');
        $jumlahBayar = (int) $this->request->getPost('jumlah_bayar');
        $tanggalBayar = $this->request->getPost('tanggal_bayar');
        $jenis = $this->request->getPost('jenis_pembayaran');
        $metode = $this->request->getPost('metode_bayar');
        $allowedJenis = ['booking_fee', 'dp', 'cicilan', 'pelunasan'];
        $allowedMetode = ['Cash', 'Transfer Bank', 'KPR', 'Cicilan Internal'];

        if ($pembelianId <= 0 || $jumlahBayar <= 0 || empty($tanggalBayar) || empty($jenis) || empty($metode)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Data pembayaran belum lengkap']);
        }

        if (!in_array($jenis, $allowedJenis, true)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Jenis pembayaran tidak valid']);
        }

        if (!in_array($metode, $allowedMetode, true)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Metode bayar tidak valid']);
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $tanggalBayar)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Format tanggal bayar tidak valid']);
        }

        $old = null;
        if ($id) {
            $old = $model->find($id);
            if (!$old) {
                return $this->response->setStatusCode(404)
                    ->setJSON(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan']);
            }
        }

        $summary = $this->getRingkasanPembayaran($pembelianId, $id);
        if (!$summary) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Data pembelian tidak ditemukan']);
        }

        if (strtolower((string) $summary['status_pembelian']) === 'batal') {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Pembelian yang sudah batal tidak bisa menerima pembayaran']);
        }

        if ($jumlahBayar > $summary['sisa_bayar']) {
            return $this->response->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Jumlah bayar melebihi sisa tagihan Rp ' . number_format($summary['sisa_bayar'], 0, ',', '.'),
                ]);
        }

        $data = [
            'pembelian_rumah_id' => $pembelianId,
            'tanggal_bayar' => $tanggalBayar,
            'jumlah_bayar' => $jumlahBayar,
            'jenis_pembayaran' => $jenis,
            'metode_bayar' => $metode,
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        $uploadedBukti = $this->uploadBuktiBayar();
        if (is_array($uploadedBukti) && ($uploadedBukti['status'] ?? '') === 'error') {
            return $this->response->setStatusCode(400)->setJSON($uploadedBukti);
        }

        $newUploadedPath = null;

        $db->transStart();

        if ($id) {
            if ($uploadedBukti) {
                $data['bukti_bayar'] = $uploadedBukti;
                $newUploadedPath = $uploadedBukti;
            }

            $model->update($id, $data);
            $this->updateStatusPembelian($old['pembelian_rumah_id']);

            if ($uploadedBukti) {
                $this->deleteBuktiBayar($old['bukti_bayar'] ?? null);
            }
        } else {
            if ($uploadedBukti) {
                $data['bukti_bayar'] = $uploadedBukti;
                $newUploadedPath = $uploadedBukti;
            }

            $model->insert($data);
        }

        $this->updateStatusPembelian($pembelianId);
        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->deleteBuktiBayar($newUploadedPath);

            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan pembayaran']);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    private function getRingkasanPembayaran(int $pembelianId, ?int $excludePaymentId = null): ?array
    {
        $pembelianModel = new PembelianRumahModel();
        $pembayaranModel = new PembayaranRumahModel();

        $pembelian = $pembelianModel
            ->select('pembelian_rumah.*, customer.nama AS nama_customer, perumahan.kode_rumah')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->find($pembelianId);

        if (!$pembelian) {
            return null;
        }

        $query = $pembayaranModel->where('pembelian_rumah_id', $pembelianId);
        if ($excludePaymentId) {
            $query->where('id !=', $excludePaymentId);
        }

        $sum = $query->selectSum('jumlah_bayar')->first();
        $totalBayar = (int) ($sum['jumlah_bayar'] ?? 0);
        $hargaBeli = (int) $pembelian['harga_beli'];

        return [
            'pembelian_id' => (int) $pembelian['id'],
            'nama_customer' => $pembelian['nama_customer'],
            'kode_rumah' => $pembelian['kode_rumah'],
            'harga_beli' => $hargaBeli,
            'total_bayar' => $totalBayar,
            'sisa_bayar' => max($hargaBeli - $totalBayar, 0),
            'status_pembelian' => $pembelian['status_pembelian'],
        ];
    }

    private function updateStatusPembelian(int $pembelianId): void
    {
        $pembelianModel = new PembelianRumahModel();
        $summary = $this->getRingkasanPembayaran($pembelianId);

        if (!$summary || strtolower($summary['status_pembelian']) === 'batal') {
            return;
        }

        if ($summary['total_bayar'] >= $summary['harga_beli']) {
            $status = 'Lunas';
        } elseif ($summary['total_bayar'] > 0) {
            $status = 'Cicil';
        } else {
            $status = 'DP';
        }

        $pembelianModel->update($pembelianId, ['status_pembelian' => $status]);
    }

    private function uploadBuktiBayar()
    {
        $file = $this->request->getFile('bukti_bayar');

        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (!$file->isValid()) {
            return ['status' => 'error', 'message' => 'Upload bukti pembayaran gagal'];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $extension = strtolower($file->getClientExtension());

        if (!in_array($extension, $allowedExtensions, true)) {
            return ['status' => 'error', 'message' => 'Bukti pembayaran harus berupa JPG, PNG, atau PDF'];
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'Ukuran bukti pembayaran maksimal 2 MB'];
        }

        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'bukti_pembayaran';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        return 'uploads/bukti_pembayaran/' . $newName;
    }

    private function deleteBuktiBayar(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
