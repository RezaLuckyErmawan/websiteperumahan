<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\PembatalanModel;
use App\Models\PembayaranRumahModel;
use App\Models\PembelianRumahModel;
use App\Models\PerumahanModel;
use App\Models\TransaksiRumahModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class PembelianRumahController extends BaseController
{
    public function pembelianrumah()
{
    $modal = new PembelianRumahModel();
    $perumahan = new PerumahanModel();
    $customerModel = new CustomerModel();
    $data['perumahan'] = $perumahan->orderBy('created_at', 'DESC')->findAll();
    $data['customer']  = $customerModel->orderBy('created_at', 'DESC')->findAll();

    // Ambil semua perumahan_id yang sudah terjual
    $data['terjual_ids'] = array_column(
        $modal->select('perumahan_id')->findAll(),
        'perumahan_id'
    );

    $data['useDataTables'] = true;
    $data['pageTitle'] = 'Penjualan Rumah';

    return view('page/pembelianrumah/pembelian_rumah', $data);
}

    public function detailPembelianList()
{
    $db = \Config\Database::connect();

    // Get pembelian data with customer and perumahan details
    $builder = $db->table('pembelian_rumah pr')
        ->select('
            pr.*,
            customer.nama as nama_customer,
            customer.telepon as telepon_customer,
            perumahan.kode_rumah,
            perumahan.tipe as tipe_rumah,
            perumahan.lokasi as lokasi_rumah,
            COALESCE(total_bayar.total_bayar, 0) AS total_bayar,
            (pr.harga_beli - COALESCE(total_bayar.total_bayar, 0)) AS sisa_bayar
        ')
        ->join('customer', 'customer.id = pr.customer_id')
        ->join('perumahan', 'perumahan.id = pr.perumahan_id')
        ->join(
            "(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah WHERE status_pengajuan = 'disetujui' GROUP BY pembelian_rumah_id) total_bayar",
            'total_bayar.pembelian_rumah_id = pr.id',
            'left'
        );

    // Apply customer scope if user is customer
    if ($this->isCustomer()) {
        $this->applyCustomerScope($builder, 'customer');
    }

    $pembelian = $builder
        ->orderBy('pr.tanggal_pembelian', 'DESC')
        ->get()
        ->getResultArray();

    $data = [
        'pageTitle' => 'Detail Pembelian Rumah',
        'pembelian' => $pembelian,
        'userRole' => session()->get('role'),
        'useDataTables' => true,
    ];

    return view('page/pembelianrumah/detail_pembelian_list', $data);
}

    public function chartPenjualanRumah() {
        $db = \config\Database::connect();
        $query = $db->query("
            SELECT 
            MONTH(tanggal_pembelian) AS bulan,
            COUNT(*) AS total
            FROM pembelian_rumah
            WHERE LOWER(status_pembelian) != 'batal'
            GROUP BY bulan
        ");
        $results = $query->getResultArray();
        $data = array_fill(1, 12, 0);

        foreach ($results as $row) {
            $bulan = (int) $row['bulan'];
            $data[$bulan] = (int) $row['total'];
        }

        return $this->response->setJSON(array_values($data));

    }

    public function json() {

        $request = service('request');
        $db = \Config\Database::connect();
        $builder = $db->table('pembelian_rumah');
        
        $builder->select('
            pembelian_rumah.*, 
            customer.nama AS customer_nama,
            customer.telepon AS customer_telepon,
            customer.email AS customer_email,
            perumahan.kode_rumah,
            COALESCE(total_bayar.total_bayar, 0) AS total_bayar,
            (pembelian_rumah.harga_beli - COALESCE(total_bayar.total_bayar, 0)) AS sisa_bayar,
            COALESCE(cicilan_count.cicilan_ke, 0) AS cicilan_ke
        ');
        $builder->join('customer', 'customer.id = pembelian_rumah.customer_id');
        $builder->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id');
        $builder->join(
            '(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah WHERE status_pengajuan = \'disetujui\' GROUP BY pembelian_rumah_id) total_bayar',
            'total_bayar.pembelian_rumah_id = pembelian_rumah.id',
            'left'
        );
        $builder->join(
            "(SELECT pembelian_rumah_id, COUNT(*) AS cicilan_ke FROM pembayaran_rumah WHERE jenis_pembayaran = 'cicilan' AND status_pengajuan = 'disetujui' GROUP BY pembelian_rumah_id) cicilan_count",
            'cicilan_count.pembelian_rumah_id = pembelian_rumah.id',
            'left'
        );

        $searchValue = $request->getGet('search')['value'] ?? '';
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $draw = (int) $request->getGet('draw');


        $totalRecords = $builder->countAllResults(false);

        if ($searchValue) {
            $builder->groupStart()
                ->like('customer.nama', $searchValue)
                ->orLike('perumahan.kode_rumah', $searchValue)
                ->orLike('pembelian_rumah.status_pembelian', $searchValue)
                ->orLike('pembelian_rumah.metode_pembayaran', $searchValue)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false); 

        
        $builder->limit($length, $start);
        $builder->orderBy('pembelian_rumah.created_at', 'DESC');

        $data = $builder->get()->getResultArray();
        $data = array_map(fn(array $row) => $this->appendCicilanInfo($row), $data);

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function store()
{
    $request = service('request');
    $db = \Config\Database::connect();
    $model = new PembelianRumahModel();
    $perumahanModel = new PerumahanModel();
    $allowedStatus = ['Lunas', 'Cicil', 'DP', 'Batal'];
    $allowedMetode = ['Cash', 'Cicilan Internal'];
    $allowedDokumen = ['Lengkap', 'Pending', 'Verifikasi'];

    $statusPembelian = $request->getPost('status_pembelian');
    $metodePembayaran = $request->getPost('metode_pembayaran');
    $statusDokumen = $request->getPost('status_dokumen');
    $lamaCicilan = $this->resolveLamaCicilan($metodePembayaran, $request->getPost('lama_cicilan_tahun'));
    $tanggalCicilan = $this->resolveTanggalCicilan($metodePembayaran, $request->getPost('tanggal_cicilan'));

    if ($lamaCicilan === false) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Lama cicilan wajib diisi 1-30 tahun untuk Cicilan Internal']);
    }

    if ($tanggalCicilan === false) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Tanggal cicilan wajib diisi untuk Cicilan Internal']);
    }

    if (
        !$request->getPost('customer_id') ||
        !$request->getPost('perumahan_id') ||
        !$request->getPost('tanggal_pembelian') ||
        !in_array($statusPembelian, $allowedStatus, true) ||
        !in_array($metodePembayaran, $allowedMetode, true) ||
        !in_array($statusDokumen, $allowedDokumen, true)
    ) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Data pembelian rumah belum lengkap atau tidak valid']);
    }

    // Ambil data rumah berdasarkan ID
    $perumahan = $perumahanModel->find($request->getPost('perumahan_id'));

    if (!$perumahan) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data rumah tidak ditemukan'
        ]);
    }

    if (strtolower((string) $perumahan['status']) === 'terjual') {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Rumah ini sudah terjual']);
    }

    $nominalDp = $this->resolveNominalDp($metodePembayaran, $request->getPost('nominal_dp'), $perumahan['harga']);
    if ($nominalDp === false) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Nominal DP tidak valid (0 s.d. harga beli)']);
    }

    $data = [
        'customer_id'       => $request->getPost('customer_id'),
        'perumahan_id'      => $request->getPost('perumahan_id'),
        'tanggal_pembelian' => $request->getPost('tanggal_pembelian'),
        'harga_beli'        => $perumahan['harga'], // AMAN
        'nominal_dp'        => $nominalDp,
        'status_pembelian'  => $statusPembelian,
        'metode_pembayaran' => $metodePembayaran,
        'lama_cicilan_tahun' => $lamaCicilan,
        'tanggal_cicilan'   => $tanggalCicilan,
        'status_dokumen'    => $statusDokumen,
        'request_khusus'    => $request->getPost('request_khusus'),
        'catatan_marketing' => $request->getPost('catatan_marketing'),
        'sumber'            => 'admin',
        'status_verifikasi' => 'tidak_perlu',
        'status_berkas'     => 'pending',
    ];

    $db->transStart();
    $model->insert($data);

    // Update status rumah
    if (strtolower($statusPembelian) === 'batal') {
        $perumahanModel->update($data['perumahan_id'], ['status' => 'Dijual']);
    } else {
        $perumahanModel->update($data['perumahan_id'], ['status' => 'Terjual']);
    }
    $db->transComplete();

    if ($db->transStatus() === false) {
        return $this->response->setStatusCode(500)
            ->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan pembelian rumah']);
    }

    return $this->response->setJSON(['status' => 'success']);
}


    public function edit($id)
{
    $model = new PembelianRumahModel();
    $data = $model->select('
            pembelian_rumah.*,
            customer.nama as nama_customer,
            perumahan.kode_rumah,
            COALESCE(total_bayar.total_bayar, 0) AS total_bayar,
            (pembelian_rumah.harga_beli - COALESCE(total_bayar.total_bayar, 0)) AS sisa_bayar,
            COALESCE(cicilan_count.cicilan_ke, 0) AS cicilan_ke
        ')
        ->join('customer', 'customer.id = pembelian_rumah.customer_id')
        ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
        ->join(
            '(SELECT pembelian_rumah_id, SUM(jumlah_bayar) AS total_bayar FROM pembayaran_rumah WHERE status_pengajuan = \'disetujui\' GROUP BY pembelian_rumah_id) total_bayar',
            'total_bayar.pembelian_rumah_id = pembelian_rumah.id',
            'left'
        )
        ->join(
            "(SELECT pembelian_rumah_id, COUNT(*) AS cicilan_ke FROM pembayaran_rumah WHERE jenis_pembayaran = 'cicilan' AND status_pengajuan = 'disetujui' GROUP BY pembelian_rumah_id) cicilan_count",
            'cicilan_count.pembelian_rumah_id = pembelian_rumah.id',
            'left'
        )
        ->find($id);

    if ($data) {
        $data = $this->appendCicilanInfo($data);

        return $this->response->setJSON([
            'status' => true,
            'data' => $data
        ]);
    } else {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Data tidak ditemukan'
        ]);
    }
}


    public function update($id) {
    $request = service('request');
    $db = \Config\Database::connect();
    $model = new PembelianRumahModel();
    $pembatalanModel = new PembatalanModel();
    $perumahanModel = new PerumahanModel();
    $allowedStatus = ['Lunas', 'Cicil', 'DP', 'Batal'];
    $allowedMetode = ['Cash', 'Cicilan Internal'];
    $allowedDokumen = ['Lengkap', 'Pending', 'Verifikasi'];

    $dataLama = $model->find($id);
    if (!$dataLama) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ]);
    }

    // Ambil input alasan pembatalan
    $alasanPembatalan = trim((string) $request->getPost('alasan_pembatalan'));
    $statusPembelian = $alasanPembatalan ? 'Batal' : $request->getPost('status_pembelian');
    $metodePembayaran = $request->getPost('metode_pembayaran');
    $statusDokumen = $request->getPost('status_dokumen');
    $perumahanId = $request->getPost('perumahan_id') ?: $dataLama['perumahan_id'];
    $lamaCicilan = $this->resolveLamaCicilan($metodePembayaran, $request->getPost('lama_cicilan_tahun'));
    $tanggalCicilan = $this->resolveTanggalCicilan($metodePembayaran, $request->getPost('tanggal_cicilan'));

    if ($lamaCicilan === false) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Lama cicilan wajib diisi 1-30 tahun untuk Cicilan Internal']);
    }

    if ($tanggalCicilan === false) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Tanggal cicilan wajib diisi untuk Cicilan Internal']);
    }

    if (
        !$request->getPost('customer_id') ||
        !$perumahanId ||
        !$request->getPost('tanggal_pembelian') ||
        !in_array($statusPembelian, $allowedStatus, true) ||
        !in_array($metodePembayaran, $allowedMetode, true) ||
        !in_array($statusDokumen, $allowedDokumen, true)
    ) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Data pembelian rumah belum lengkap atau tidak valid']);
    }

    $nominalDp = $this->resolveNominalDp($metodePembayaran, $request->getPost('nominal_dp'), $request->getPost('harga_beli'));
    if ($nominalDp === false) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Nominal DP tidak valid (0 s.d. harga beli)']);
    }

    // Data baru default
    $dataBaru = [
        'customer_id'       => $request->getPost('customer_id'),
        'perumahan_id'      => $perumahanId,
        'tanggal_pembelian' => $request->getPost('tanggal_pembelian'),
        'harga_beli'        => $request->getPost('harga_beli'),
        'nominal_dp'        => $nominalDp,
        'status_pembelian'  => $statusPembelian,
        'metode_pembayaran' => $metodePembayaran,
        'lama_cicilan_tahun' => $lamaCicilan,
        'tanggal_cicilan'   => $tanggalCicilan,
        'status_dokumen'    => $statusDokumen,
        'request_khusus'    => $request->getPost('request_khusus'),
        'catatan_marketing' => $request->getPost('catatan_marketing'),
    ];

    $rumahBaru = $perumahanModel->find($dataBaru['perumahan_id']);
    if (!$rumahBaru) {
        return $this->response->setStatusCode(404)
            ->setJSON(['status' => 'error', 'message' => 'Data rumah tidak ditemukan']);
    }

    if (
        (int) $dataBaru['perumahan_id'] !== (int) $dataLama['perumahan_id'] &&
        strtolower((string) $rumahBaru['status']) === 'terjual'
    ) {
        return $this->response->setStatusCode(400)
            ->setJSON(['status' => 'error', 'message' => 'Rumah pengganti sudah terjual']);
    }

    $db->transStart();

    if (!empty($alasanPembatalan)) {
        // Insert ke pembatalan_transaksi
        $pembatalanModel->insert([
            'pembelian_id'         => $id,
            'perumahan_id'         => $dataBaru['perumahan_id'],
            'customer_id'          => $dataBaru['customer_id'],
            'keterangan_pembatalan'=> $alasanPembatalan
        ]);

    }

    $model->update($id, $dataBaru);

    if ((int) $dataBaru['perumahan_id'] !== (int) $dataLama['perumahan_id']) {
        $perumahanModel->update($dataLama['perumahan_id'], ['status' => 'Dijual']);
    }

    if (strtolower($dataBaru['status_pembelian']) === 'batal') {
        $perumahanModel->update($dataBaru['perumahan_id'], ['status' => 'Dijual']);
    } else {
        $perumahanModel->update($dataBaru['perumahan_id'], ['status' => 'Terjual']);
    }

    $db->transComplete();

    if ($db->transStatus() === false) {
        return $this->response->setStatusCode(500)
            ->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui pembelian rumah']);
    }

    return $this->response->setJSON(['status' => 'success']);
}


    public function detailPembelian($id)
    {
        $model = new PembelianRumahModel();
        $pembayaranModel = new PembayaranRumahModel();
        $db = \Config\Database::connect();

        // Build query for pembelian data
        $builder = $db->table('pembelian_rumah pr')
            ->select('
                pr.*,
                customer.nama as nama_customer,
                customer.email as email_customer,
                customer.telepon as telepon_customer,
                customer.alamat as alamat_customer,
                perumahan.kode_rumah,
                perumahan.tipe as tipe_rumah,
                perumahan.luas_tanah,
                perumahan.luas_bangunan,
                perumahan.lokasi as lokasi_rumah,
                perumahan.gambar as gambar_rumah,
                perumahan.dokumen as dokumen_rumah,
                perumahan.deskripsi as deskripsi_rumah
            ')
            ->join('customer', 'customer.id = pr.customer_id')
            ->join('perumahan', 'perumahan.id = pr.perumahan_id')
            ->where('pr.id', $id);

        // Apply customer scope if user is customer
        if ($this->isCustomer()) {
            $this->applyCustomerScope($builder, 'customer');
        }

        $pembelian = $builder->get()->getRowArray();

        if (!$pembelian) {
            return redirect()->to('/detail-pembelian-list')->with('error', 'Data pembelian tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Get payment history for this purchase
        $pembayaran = $pembayaranModel
            ->where('pembelian_rumah_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        $pembayaran = PembelianRumahModel::annotatePembayaranCicilan($pembayaran, $pembelian);

        $totalDibayar = 0;
        foreach ($pembayaran as $p) {
            if ($p['status_pengajuan'] === 'disetujui') {
                $totalDibayar += $p['jumlah_bayar'];
            }
        }

        $sisaTagihan = $pembelian['harga_beli'] - $totalDibayar;

        $berkasCustomer = [];
        $infoBerkas = null;
        if (($pembelian['sumber'] ?? '') === 'customer') {
            $infoBerkas = TransaksiRumahModel::infoBerkas($pembelian);
            foreach (TransaksiRumahModel::JENIS_BERKAS as $key => $meta) {
                $berkasCustomer[] = [
                    'key' => $key,
                    'label' => $meta['label'],
                    'wajib' => $meta['wajib'],
                    'keterangan' => $meta['keterangan'],
                    'file' => $infoBerkas['uploaded'][$key] ?? null,
                    'status' => $infoBerkas['verifikasi'][$key] ?? ((!empty($infoBerkas['uploaded'][$key])) ? 'pending' : null),
                ];
            }
        }

        $data = [
            'pageTitle' => 'Detail Pembelian Rumah',
            'pembelian' => $pembelian,
            'pembayaran' => $pembayaran,
            'total_dibayar' => $totalDibayar,
            'sisa_tagihan' => $sisaTagihan,
            'userRole' => session()->get('role'),
            'isCustomer' => $this->isCustomer(),
            'berkasCustomer' => $berkasCustomer,
            'infoBerkas' => $infoBerkas,
        ];

        return view('page/pembelianrumah/detail_pembelian', $data);
    }

    public function delete($id)
{
    $model = new PembelianRumahModel();
    $perumahanModel = new PerumahanModel();
    $pembayaranModel = new PembayaranRumahModel();
    $db = \Config\Database::connect();

    try {
        $data = $model->find($id);
        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        $db->transStart();
        $buktiPembayaran = $pembayaranModel
            ->where('pembelian_rumah_id', $id)
            ->where('bukti_bayar IS NOT NULL', null, false)
            ->findAll();

        $deleted = $model->delete($id);
        if ($deleted) {
            $perumahanModel->update($data['perumahan_id'], ['status' => 'Dijual']);
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Data gagal dihapus.']);
        }

        if ($deleted) {
            foreach ($buktiPembayaran as $bukti) {
                $path = $bukti['bukti_bayar'] ?? null;
                if ($path) {
                    $fullPath = FCPATH . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
                    if (is_file($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data gagal dihapus.'
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
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

    private function applyCustomerScope($builder, string $customerAlias)
    {
        $customerId = $this->resolveCustomerId();
        $name = trim((string) session()->get('nama'));
        $username = trim((string) session()->get('username'));

        $builder->groupStart();

        $hasCondition = false;
        if ($customerId) {
            $builder->where($customerAlias . '.id', $customerId);
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
            $builder->where($customerAlias . '.id', 0);
        }

        return $builder->groupEnd();
    }

    /**
     * @return int|null|false
     */
    private function resolveLamaCicilan(?string $metodePembayaran, $lamaCicilan)
    {
        if (strtolower((string) $metodePembayaran) !== 'cicilan internal') {
            return null;
        }

        $tahun = (int) $lamaCicilan;
        if ($tahun < 1 || $tahun > 30) {
            return false;
        }

        return $tahun;
    }

    /**
     * @return string|null|false
     */
    private function resolveTanggalCicilan(?string $metodePembayaran, $tanggalCicilan)
    {
        if (strtolower((string) $metodePembayaran) !== 'cicilan internal') {
            return null;
        }

        $tanggal = trim((string) $tanggalCicilan);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return false;
        }

        return $tanggal;
    }

    /**
     * Nominal DP opsional untuk Cicilan Internal.
     *
     * @return int|null|false null = tidak diset, false = tidak valid
     */
    private function resolveNominalDp(?string $metodePembayaran, $nominalDp, $hargaBeli)
    {
        if (strtolower((string) $metodePembayaran) !== 'cicilan internal') {
            return null;
        }

        $nominal = trim((string) $nominalDp);
        if ($nominal === '') {
            return null;
        }

        $nominal = (int) $nominal;
        if ($nominal < 0 || $nominal > (int) $hargaBeli) {
            return false;
        }

        return $nominal;
    }

    private function appendCicilanInfo(array $row): array
    {
        $metode = strtolower((string) ($row['metode_pembayaran'] ?? ''));
        $tahun = (int) ($row['lama_cicilan_tahun'] ?? 0);
        $cicilanKe = (int) ($row['cicilan_ke'] ?? 0);
        $status = strtolower((string) ($row['status_pembelian'] ?? ''));
        $sisa = (int) ($row['sisa_bayar'] ?? 0);
        $harga = (int) ($row['harga_beli'] ?? 0);

        $row['info_cicilan_tahun'] = $tahun > 0 ? $tahun . ' tahun' : '-';
        $row['info_cicilan_ke'] = '-';
        $row['info_cicilan_berikutnya'] = '-';

        if ($metode !== 'cicilan internal') {
            return $row;
        }

        $totalCicilan = $tahun > 0 ? $tahun * 12 : 0;
        $row['info_cicilan_ke'] = $totalCicilan > 0
            ? $cicilanKe . ' dari ' . $totalCicilan
            : (string) $cicilanKe;

        if ($status === 'batal') {
            $row['info_cicilan_berikutnya'] = '-';
            return $row;
        }

        if ($status === 'lunas' || ($totalCicilan > 0 && $cicilanKe >= $totalCicilan) || ($sisa <= 0 && $cicilanKe > 0)) {
            $row['info_cicilan_berikutnya'] = 'Lunas';
            return $row;
        }

        $jatuhTempoIso = PembelianRumahModel::jatuhTempoCicilan(
            $row['tanggal_cicilan'] ?? null,
            $row['tanggal_pembelian'] ?? null,
            $cicilanKe
        );
        $jatuhTempo = '-';
        if ($jatuhTempoIso) {
            $due = date_create($jatuhTempoIso);
            $jatuhTempo = $due ? $due->format('d/m/Y') : $jatuhTempoIso;
        }

        $nominalText = '';
        if ($totalCicilan > 0 && $sisa > 0) {
            $dasarCicilan = max($harga - (int) ($row['nominal_dp'] ?? 0), 0);
            $nominalTetap = (int) ceil($dasarCicilan / $totalCicilan);
            $nominal = ($cicilanKe + 1 >= $totalCicilan)
                ? $sisa
                : min($nominalTetap, $sisa);
            $nominalText = ' • Rp ' . number_format($nominal, 0, ',', '.');
        }

        $row['info_cicilan_berikutnya'] = $jatuhTempo . $nominalText;

        return $row;
    }

    public function bookingJson()
    {
        return $this->json();
    }

    public function bookingDetail($id)
    {
        $model = new PembelianRumahModel();
        $row = $model
            ->select('pembelian_rumah.*, customer.nama, customer.telepon, customer.email, customer.alamat, perumahan.kode_rumah, perumahan.tipe, perumahan.lokasi, perumahan.harga')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->where('pembelian_rumah.id', $id)
            ->first();

        if (!$row || ($row['sumber'] ?? '') !== 'customer') {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Data booking tidak ditemukan',
            ]);
        }

        $info = TransaksiRumahModel::infoBerkas($row);
        $berkas = [];
        foreach (TransaksiRumahModel::JENIS_BERKAS as $key => $meta) {
            $berkas[] = [
                'key' => $key,
                'label' => $meta['label'],
                'wajib' => $meta['wajib'],
                'file' => $info['uploaded'][$key] ?? null,
                'status' => $info['verifikasi'][$key] ?? ((!empty($info['uploaded'][$key])) ? 'pending' : null),
            ];
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $row,
            'info_berkas' => $info,
            'berkas' => $berkas,
        ]);
    }

    public function verifikasiBerkas($id, $jenis)
    {
        if ($this->isCustomer()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 'error',
                'message' => 'Customer tidak dapat memverifikasi berkas.',
            ]);
        }

        $jenis = strtolower((string) $jenis);
        if (!isset(TransaksiRumahModel::JENIS_BERKAS[$jenis])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Jenis berkas tidak valid.',
            ]);
        }

        $aksi = strtolower((string) $this->request->getPost('aksi'));
        if (!in_array($aksi, ['setujui', 'tolak'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Aksi verifikasi tidak valid.',
            ]);
        }

        $model = new PembelianRumahModel();
        $pembelian = $model->find($id);
        if (!$pembelian) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Data pembelian tidak ditemukan.',
            ]);
        }

        $split = TransaksiRumahModel::splitBerkas($pembelian['berkas'] ?? null);
        if (empty($split['files'][$jenis])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Berkas belum diunggah, tidak dapat diverifikasi.',
            ]);
        }

        $split['verifikasi'][$jenis] = $aksi === 'setujui' ? 'disetujui' : 'ditolak';
        $model->update($id, [
            'berkas' => TransaksiRumahModel::encodeBerkas($split['files'], $split['verifikasi']),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $aksi === 'setujui' ? 'Berkas disetujui.' : 'Berkas ditolak.',
        ]);
    }

    public function verifikasiBooking($id)
    {
        $aksi = strtolower((string) $this->request->getPost('aksi'));
        $catatan = trim((string) $this->request->getPost('catatan_verifikasi'));
        if (!in_array($aksi, ['setujui', 'tolak'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Aksi verifikasi tidak valid.',
            ]);
        }

        $model = new PembelianRumahModel();
        $pembelian = $model->find($id);
        if (!$pembelian || ($pembelian['sumber'] ?? '') !== 'customer') {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Data booking tidak ditemukan.',
            ]);
        }

        if (strtolower((string) ($pembelian['status_verifikasi'] ?? '')) !== 'pending') {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Booking ini sudah diverifikasi.',
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $perumahanModel = new PerumahanModel();
        $update = [
            'status_verifikasi' => $aksi === 'setujui' ? 'disetujui' : 'ditolak',
            'catatan_verifikasi' => $catatan !== '' ? $catatan : null,
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => session()->get('user_id'),
        ];

        if ($aksi === 'tolak') {
            $update['status_pembelian'] = 'Batal';
            $perumahanModel->update($pembelian['perumahan_id'], ['status' => 'Dijual']);
        } else {
            $metodePembayaran = $this->request->getPost('metode_pembayaran');
            $statusPembelian = $this->request->getPost('status_pembelian');
            $allowedMetode = ['Cash', 'Cicilan Internal'];
            $allowedStatus = ['DP', 'Cicil', 'Lunas'];

            if (!in_array($metodePembayaran, $allowedMetode, true) || !in_array($statusPembelian, $allowedStatus, true)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Metode pembayaran dan status pembelian wajib diisi dengan benar.',
                ]);
            }

            $lamaCicilan = $this->resolveLamaCicilan($metodePembayaran, $this->request->getPost('lama_cicilan_tahun'));
            $tanggalCicilan = $this->resolveTanggalCicilan($metodePembayaran, $this->request->getPost('tanggal_cicilan'));
            if ($lamaCicilan === false) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Lama cicilan wajib diisi 1-30 tahun untuk Cicilan Internal.',
                ]);
            }
            if ($tanggalCicilan === false) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Tanggal cicilan wajib diisi untuk Cicilan Internal.',
                ]);
            }

            $nominalDp = $this->resolveNominalDp($metodePembayaran, $this->request->getPost('nominal_dp'), $pembelian['harga_beli']);
            if ($nominalDp === false) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Nominal DP tidak valid (0 s.d. harga beli).',
                ]);
            }

            $info = TransaksiRumahModel::infoBerkas($pembelian);
            $update['status_pembelian'] = $statusPembelian;
            $update['metode_pembayaran'] = $metodePembayaran;
            $update['lama_cicilan_tahun'] = $lamaCicilan;
            $update['tanggal_cicilan'] = $tanggalCicilan;
            $update['nominal_dp'] = $nominalDp;
            $update['status_dokumen'] = $info['wajib_terisi'] ? 'Lengkap' : 'Verifikasi';
            $perumahanModel->update($pembelian['perumahan_id'], ['status' => 'Terjual']);
        }

        $model->update($id, $update);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memverifikasi booking.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $aksi === 'setujui' ? 'Booking disetujui.' : 'Booking ditolak.',
        ]);
    }

}
