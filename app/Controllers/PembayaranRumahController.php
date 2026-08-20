<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PembayaranRumahModel;
use App\Models\PembelianRumahModel;
use App\Models\UserModel;

class PembayaranRumahController extends BaseController
{
    public function pembayaranrumah()
    {
        $db = \Config\Database::connect();

        $pembelian = $db->table('pembelian_rumah')
            ->select('pembelian_rumah.id, pembelian_rumah.harga_beli, customer.nama AS nama_customer, perumahan.kode_rumah')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->where("LOWER(pembelian_rumah.status_pembelian) != 'batal'", null, false)
            ->orderBy('pembelian_rumah.created_at', 'DESC');

        if ($this->isCustomer()) {
            $this->applyCustomerScope($pembelian);
        }

        return view('page/pembayaranrumah/pembayaran_rumah', [
            'pembelian' => $pembelian->get()->getResultArray(),
            'selectedPembelianId' => $this->request->getGet('pembelian_id'),
            'canCreatePayments' => true,
            'canModifyPayments' => !$this->isCustomer(),
            'userRole' => session()->get('role'),
            'useDataTables' => true,
            'pageTitle' => 'Pembayaran Cicilan Rumah',
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
                pembelian_rumah.tanggal_pembelian,
                pembelian_rumah.tanggal_cicilan,
                customer.nama AS nama_customer,
                perumahan.kode_rumah,
                COALESCE(total_bayar.total_bayar, 0) AS total_bayar,
                (pembelian_rumah.harga_beli - COALESCE(total_bayar.total_bayar, 0)) AS sisa_bayar,
                CASE
                    WHEN pr.jenis_pembayaran = \'cicilan\' AND pr.status_pengajuan != \'ditolak\' THEN (
                        SELECT COUNT(*)
                        FROM pembayaran_rumah p2
                        WHERE p2.pembelian_rumah_id = pr.pembelian_rumah_id
                          AND p2.jenis_pembayaran = \'cicilan\'
                          AND p2.status_pengajuan != \'ditolak\'
                          AND (
                            p2.created_at < pr.created_at
                            OR (p2.created_at = pr.created_at AND p2.id <= pr.id)
                          )
                    )
                    ELSE NULL
                END AS cicilan_ke
            ')
            ->join('pembelian_rumah', 'pembelian_rumah.id = pr.pembelian_rumah_id')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->join(
                "(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah WHERE status_pengajuan = 'disetujui' GROUP BY pembelian_rumah_id) total_bayar",
                'total_bayar.pembelian_rumah_id = pr.pembelian_rumah_id',
                'left'
            );

        if ($this->isCustomer()) {
            $this->applyCustomerScope($builder);
        }

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

        $totalBuilder = $db->table('pembayaran_rumah pr')
            ->join('pembelian_rumah', 'pembelian_rumah.id = pr.pembelian_rumah_id')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id');
        if ($this->isCustomer()) {
            $this->applyCustomerScope($totalBuilder, 'customer');
        }

        $total = $totalBuilder->countAllResults();
        $filtered = $builder->countAllResults(false);

        $start = (int) ($request->getGet('start') ?? 0);
        $length = (int) ($request->getGet('length') ?? 10);
        $data = $builder
            ->orderBy('pr.created_at', 'DESC')
            ->get($length, $start)
            ->getResultArray();

        foreach ($data as &$row) {
            $info = PembelianRumahModel::infoTampilanPembayaran(
                $row,
                $row['tanggal_cicilan'] ?? null,
                $row['tanggal_pembelian'] ?? null
            );
            $row = array_merge($row, $info);
        }
        unset($row);

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
        if ($this->isCustomer()) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Customer hanya dapat melihat data pembayaran.']);
        }

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
        if ($this->isCustomer()) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Customer tidak dapat mengubah pembayaran.']);
        }

        return $this->savePembayaran($id);
    }

    public function approve($id)
    {
        if ($this->isCustomer()) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Customer tidak dapat menyetujui pembayaran.']);
        }

        $db = \Config\Database::connect();
        $model = new PembayaranRumahModel();
        $data = $model->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan']);
        }

        $db->transStart();
        $model->update($id, [
            'tanggal_bayar'     => $data['tanggal_bayar'] ?: date('Y-m-d'),
            'jenis_pembayaran'  => $data['jenis_pembayaran'] ?: 'cicilan',
            'status_pengajuan'  => 'disetujui',
            'approved_at'       => date('Y-m-d H:i:s'),
            'approved_by'       => session()->get('user_id'),
        ]);
        $this->updateStatusPembelian((int) $data['pembelian_rumah_id']);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Gagal menyetujui pembayaran']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Pembayaran berhasil disetujui']);
    }

    public function reject($id)
    {
        if ($this->isCustomer()) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Customer tidak dapat menolak pembayaran.']);
        }

        $model = new PembayaranRumahModel();
        $data = $model->find($id);

        if (!$data) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan']);
        }

        if (strtolower((string) ($data['status_pengajuan'] ?? '')) !== 'pending') {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Hanya pengajuan pending yang dapat ditolak.']);
        }

        $model->update($id, [
            'status_pengajuan' => 'ditolak',
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => session()->get('user_id'),
        ]);
        $this->updateStatusPembelian((int) $data['pembelian_rumah_id']);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Bukti cicilan ditolak']);
    }

    public function unggahUlangBukti($id)
    {
        if (!$this->isCustomer()) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Hanya customer yang dapat memperbarui bukti cicilan.']);
        }

        $model = new PembayaranRumahModel();
        $data = $model->find($id);
        if (!$data) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan']);
        }

        $summary = $this->getRingkasanPembayaran((int) $data['pembelian_rumah_id']);
        if (!$summary) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Pembayaran ini bukan milik Anda.']);
        }

        $status = strtolower((string) ($data['status_pengajuan'] ?? ''));
        if (!in_array($status, ['pending', 'ditolak'], true)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Bukti yang sudah disetujui tidak dapat diubah.']);
        }

        $uploadedBukti = $this->uploadBuktiBayar();
        if (is_array($uploadedBukti) && ($uploadedBukti['status'] ?? '') === 'error') {
            return $this->response->setStatusCode(400)->setJSON($uploadedBukti);
        }
        if (!$uploadedBukti) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Bukti pembayaran wajib diunggah']);
        }

        $oldBukti = $data['bukti_bayar'] ?? null;
        $model->update($id, [
            'bukti_bayar' => $uploadedBukti,
            'status_pengajuan' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
        ]);
        $this->deleteBuktiBayar($oldBukti);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function delete($id)
    {
        if ($this->isCustomer()) {
            return $this->response->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Customer tidak dapat menghapus pembayaran.']);
        }

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

    private function isCustomer(): bool
    {
        return session()->get('role') === 'customer';
    }

    private function resolveCustomerId(): ?int
    {
        $userId = session()->get('user_id');
        if ($userId) {
            $user = (new UserModel())->find($userId);
            if (!empty($user['customer_id'])) {
                return (int) $user['customer_id'];
            }
        }

        $sessionCustomerId = session()->get('customer_id');
        return $sessionCustomerId ? (int) $sessionCustomerId : null;
    }

    private function applyCustomerScope($builder, string $customerAlias = 'customer')
    {
        $customerId = $this->resolveCustomerId();
        $name = trim((string) session()->get('nama'));
        $username = trim((string) session()->get('username'));

        $builder->groupStart();

        $hasCondition = false;
        if ($customerId) {
            $builder->where('pembelian_rumah.customer_id', $customerId);
            $hasCondition = true;
        }

        if ($name !== '') {
            if ($hasCondition) {
                $builder->orWhere($customerAlias . '.nama', $name);
            } else {
                $builder->where($customerAlias . '.nama', $name);
                $hasCondition = true;
            }
        }

        if ($username !== '' && strcasecmp($username, $name) !== 0) {
            if ($hasCondition) {
                $builder->orWhere($customerAlias . '.nama', $username);
            } else {
                $builder->where($customerAlias . '.nama', $username);
                $hasCondition = true;
            }
        }

        if (!$hasCondition) {
            $builder->where('pembelian_rumah.id', 0);
        }

        return $builder->groupEnd();
    }

    private function savePembayaran(?int $id = null)
    {
        $db = \Config\Database::connect();
        $model = new PembayaranRumahModel();

        $pembelianId = (int) $this->request->getPost('pembelian_rumah_id');
        $jumlahBayar = (int) $this->request->getPost('jumlah_bayar');
        $tanggalBayar = $this->isCustomer() ? null : trim((string) $this->request->getPost('tanggal_bayar'));
        if (!$this->isCustomer() && $tanggalBayar === '') {
            $tanggalBayar = date('Y-m-d');
        }

        if ($pembelianId <= 0 || $jumlahBayar <= 0 || (!$this->isCustomer() && empty($tanggalBayar))) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Data pembayaran belum lengkap']);
        }

        if (!$this->isCustomer() && !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $tanggalBayar)) {
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

        $jenis = (string) ($summary['jenis_pembayaran'] ?? '');
        $metode = (string) ($summary['metode_pembayaran'] ?? '');
        $allowedJenis = ['booking_fee', 'dp', 'cicilan', 'pelunasan'];
        $allowedMetode = ['Cash', 'Transfer Bank', 'Cicilan Internal'];

        if (!in_array($jenis, $allowedJenis, true) || !in_array($metode, $allowedMetode, true)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Metode pembayaran transaksi rumah tidak valid']);
        }

        if (strtolower((string) $summary['status_pembelian']) === 'batal') {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Pembelian yang sudah batal tidak bisa menerima pembayaran']);
        }

        $approvalStatus = $this->resolveApprovalStatus($id, $old);

        if (strtolower((string) $jenis) === 'cicilan') {
            $bulanRef = $tanggalBayar ?: date('Y-m-d');
            if ($this->sudahAdaCicilanBulanIni($pembelianId, $bulanRef, $id)) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['status' => 'error', 'message' => 'Cicilan hanya dapat dibayar 1 kali setiap bulan.']);
            }
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
            'status_pengajuan' => $approvalStatus,
            'approved_at' => $approvalStatus === 'disetujui' ? date('Y-m-d H:i:s') : null,
            'approved_by' => $approvalStatus === 'disetujui' ? session()->get('user_id') : null,
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

        if (!$this->isCustomer()) {
            $this->updateStatusPembelian($pembelianId);
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            $this->deleteBuktiBayar($newUploadedPath);

            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan pembayaran']);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    private function resolveApprovalStatus(?int $id, ?array $old): string
    {
        if ($this->isCustomer()) {
            return 'pending';
        }

        if ($id && $old) {
            return $old['status_pengajuan'] ?? 'disetujui';
        }

        return 'disetujui';
    }

    private function getRingkasanPembayaran(int $pembelianId, ?int $excludePaymentId = null): ?array
    {
        $pembelianModel = new PembelianRumahModel();
        $pembayaranModel = new PembayaranRumahModel();

        $pembelian = $pembelianModel
            ->select('pembelian_rumah.*, customer.nama AS nama_customer, perumahan.kode_rumah')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->where('pembelian_rumah.id', $pembelianId);

        if ($this->isCustomer()) {
            $this->applyCustomerScope($pembelian);
        }

        $pembelian = $pembelian->first();

        if (!$pembelian) {
            return null;
        }

        $query = $pembayaranModel
            ->where('pembelian_rumah_id', $pembelianId)
            ->where('status_pengajuan', 'disetujui');
        if ($excludePaymentId) {
            $query->where('id !=', $excludePaymentId);
        }

        $sum = $query->selectSum('jumlah_bayar')->first();
        $totalBayar = (int) ($sum['jumlah_bayar'] ?? 0);
        $hargaBeli = (int) $pembelian['harga_beli'];
        $sisaBayar = max($hargaBeli - $totalBayar, 0);
        $cicilanKe = $this->hitungCicilanKe($pembelianId, $excludePaymentId);
        $resolved = $this->resolveJenisDanMetode($pembelian, $totalBayar);
        $cicilanInfo = $this->hitungInfoCicilan($pembelian, $sisaBayar, $cicilanKe, $pembelianId, $excludePaymentId);

        return [
            'pembelian_id' => (int) $pembelian['id'],
            'nama_customer' => $pembelian['nama_customer'],
            'kode_rumah' => $pembelian['kode_rumah'],
            'harga_beli' => $hargaBeli,
            'total_bayar' => $totalBayar,
            'sisa_bayar' => $sisaBayar,
            'status_pembelian' => $pembelian['status_pembelian'],
            'metode_pembayaran' => $resolved['metode'],
            'jenis_pembayaran' => $resolved['jenis'],
            'lama_cicilan_tahun' => (int) ($pembelian['lama_cicilan_tahun'] ?? 0),
            'cicilan_ke' => $cicilanKe,
            'total_cicilan' => $cicilanInfo['total_cicilan'],
            'jumlah_cicilan' => $cicilanInfo['jumlah_cicilan'],
            'jatuh_tempo' => $cicilanInfo['jatuh_tempo'],
            'sudah_cicilan_bulan_ini' => $cicilanInfo['sudah_cicilan_bulan_ini'],
        ];
    }

    private function resolveJenisDanMetode(array $pembelian, int $totalBayar): array
    {
        $raw = trim((string) ($pembelian['metode_pembayaran'] ?? ''));
        $status = strtolower(trim((string) ($pembelian['status_pembelian'] ?? '')));
        $lama = (int) ($pembelian['lama_cicilan_tahun'] ?? 0);
        $aliases = [
            'cash' => 'Cash',
            'transfer bank' => 'Transfer Bank',
            'cicilan internal' => 'Cicilan Internal',
        ];
        $metode = $aliases[strtolower($raw)] ?? null;
        $isCicilan = $metode === 'Cicilan Internal' || ($lama > 0 && $metode !== 'Cash');
        $metodeCicilan = $isCicilan ? 'Cicilan Internal' : ($metode ?: 'Transfer Bank');
        $sudahDp = $this->sudahAdaDpAtauBookingFee((int) ($pembelian['id'] ?? 0));

        if (!$sudahDp && $totalBayar <= 0) {
            $jenisAwal = in_array($status, ['dp', 'proses'], true) ? 'dp' : 'booking_fee';

            return ['jenis' => $jenisAwal, 'metode' => $metodeCicilan];
        }

        if ($isCicilan) {
            return ['jenis' => 'cicilan', 'metode' => 'Cicilan Internal'];
        }

        $metode = $metode ?: 'Transfer Bank';

        return ['jenis' => 'pelunasan', 'metode' => $metode];
    }

    private function sudahAdaDpAtauBookingFee(int $pembelianId): bool
    {
        if ($pembelianId <= 0) {
            return false;
        }

        $row = (new PembayaranRumahModel())
            ->where('pembelian_rumah_id', $pembelianId)
            ->whereIn('jenis_pembayaran', ['dp', 'booking_fee'])
            ->whereIn('status_pengajuan', ['pending', 'disetujui'])
            ->first();

        return (bool) $row;
    }

    private function hitungCicilanKe(int $pembelianId, ?int $excludePaymentId = null): int
    {
        $query = (new PembayaranRumahModel())
            ->where('pembelian_rumah_id', $pembelianId)
            ->where('jenis_pembayaran', 'cicilan')
            ->where('status_pengajuan', 'disetujui');
        if ($excludePaymentId) {
            $query->where('id !=', $excludePaymentId);
        }

        return (int) $query->countAllResults();
    }

    private function hitungInfoCicilan(array $pembelian, int $sisaBayar, int $cicilanKe, int $pembelianId, ?int $excludePaymentId = null): array
    {
        $tahun = (int) ($pembelian['lama_cicilan_tahun'] ?? 0);
        $totalCicilan = $tahun > 0 ? $tahun * 12 : 0;
        $metode = strtolower((string) ($pembelian['metode_pembayaran'] ?? ''));
        $hargaBeli = (int) ($pembelian['harga_beli'] ?? 0);
        $jumlahCicilan = 0;
        $jatuhTempo = null;

        if ($metode === 'cicilan internal' && $totalCicilan > 0 && $sisaBayar > 0) {
            $nominalTetap = (int) ceil($hargaBeli / $totalCicilan);
            $jumlahCicilan = ($cicilanKe + 1 >= $totalCicilan)
                ? $sisaBayar
                : min($nominalTetap, $sisaBayar);

            $jatuhTempo = PembelianRumahModel::jatuhTempoCicilan(
                $pembelian['tanggal_cicilan'] ?? null,
                $pembelian['tanggal_pembelian'] ?? null,
                $cicilanKe
            );
        }

        return [
            'total_cicilan' => $totalCicilan,
            'jumlah_cicilan' => $jumlahCicilan,
            'jatuh_tempo' => $jatuhTempo,
            'sudah_cicilan_bulan_ini' => $this->sudahAdaCicilanBulanIni($pembelianId, date('Y-m-d'), $excludePaymentId),
        ];
    }

    private function sudahAdaCicilanBulanIni(int $pembelianId, string $tanggal, ?int $excludeId = null): bool
    {
        $bulan = substr($tanggal, 0, 7);
        if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            return false;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('pembayaran_rumah')
            ->where('pembelian_rumah_id', $pembelianId)
            ->where('jenis_pembayaran', 'cicilan')
            ->whereIn('status_pengajuan', ['pending', 'disetujui'])
            ->where("DATE_FORMAT(COALESCE(tanggal_bayar, created_at), '%Y-%m') = " . $db->escape($bulan), null, false);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
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
            if ($this->isCustomer()) {
                return ['status' => 'error', 'message' => 'Bukti pembayaran wajib diunggah'];
            }

            return null;
        }

        $error = $file->getError();
        if (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return ['status' => 'error', 'message' => 'Ukuran bukti pembayaran terlalu besar. Maksimal 2 MB.'];
        }

        if ($error !== UPLOAD_ERR_OK) {
            return ['status' => 'error', 'message' => $this->uploadErrorMessage($error)];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $extension = strtolower((string) ($file->getClientExtension() ?: pathinfo((string) $file->getName(), PATHINFO_EXTENSION)));

        if (!in_array($extension, $allowedExtensions, true)) {
            return ['status' => 'error', 'message' => 'Bukti pembayaran harus berupa JPG, PNG, atau PDF'];
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return ['status' => 'error', 'message' => 'Ukuran bukti pembayaran maksimal 2 MB'];
        }

        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'bukti_pembayaran';
        if (!is_dir($uploadPath) && !@mkdir($uploadPath, 0777, true) && !is_dir($uploadPath)) {
            return ['status' => 'error', 'message' => 'Folder upload bukti pembayaran tidak dapat dibuat'];
        }

        if (!is_writable($uploadPath)) {
            @chmod($uploadPath, 0777);
            if (!is_writable($uploadPath)) {
                return ['status' => 'error', 'message' => 'Folder upload bukti pembayaran tidak dapat ditulisi'];
            }
        }

        $newName = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $destination = $uploadPath . DIRECTORY_SEPARATOR . $newName;

        try {
            if ($file->isValid() && !$file->hasMoved()) {
                $file->move($uploadPath, $newName);
            } else {
                $tmpName = $file->getTempName();
                if (!$tmpName || !is_file($tmpName) || !@copy($tmpName, $destination)) {
                    return ['status' => 'error', 'message' => 'Upload bukti pembayaran gagal. Gunakan file JPG, PNG, atau PDF di bawah 2 MB.'];
                }
            }
        } catch (\Throwable $e) {
            $tmpName = $file->getTempName();
            if (!$tmpName || !is_file($tmpName) || !@copy($tmpName, $destination)) {
                return ['status' => 'error', 'message' => 'Gagal menyimpan bukti pembayaran'];
            }
        }

        return 'uploads/bukti_pembayaran/' . $newName;
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_PARTIAL => 'File bukti pembayaran hanya terunggah sebagian. Coba lagi.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara upload tidak ditemukan.',
            UPLOAD_ERR_CANT_WRITE => 'File bukti pembayaran gagal ditulis ke server.',
            UPLOAD_ERR_EXTENSION => 'Upload bukti pembayaran diblokir ekstensi PHP.',
            default => 'Upload bukti pembayaran gagal.',
        };
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
