<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BahanBangunanModel;
use App\Models\CustomerModel;
use App\Models\PembayaranRumahModel;
use App\Models\PembelianBahanModel;
use App\Models\PembelianRumahModel;
use App\Models\PerumahanModel;
use App\Models\TransaksiRumahModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
   public function index()
{   
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
    }

    if (session()->get('role') === 'customer') {
        return $this->dashboardCustomer();
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
        $model = new PerumahanModel();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $model->orderBy('created_at', 'DESC');
        if ($q !== '') {
            $builder->groupStart()
                ->like('lokasi', $q)
                ->orLike('tipe', $q)
                ->orLike('kode_rumah', $q)
                ->orLike('status', $q)
                ->orLike('deskripsi', $q)
                ->orLike('harga', $q)
                ->groupEnd();
        }

        return view('page/landingpage', [
            'rumah' => $builder->findAll(),
            'q' => $q,
        ]);
    }

    public function detailRumah($id)
    {
        $model = new PerumahanModel();
        $rumah = $model->find($id);

        if (!$rumah) {
            return redirect()->to('/')->with('error', 'Data rumah tidak ditemukan.');
        }

        return view('page/detail_rumah_publik', [
            'rumah' => $rumah,
        ]);
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

    private function dashboardCustomer()
    {
        $pembelian = $this->findCustomerPembelian();

        if (!$pembelian) {
            $rekomendasi = (new PerumahanModel())
                ->whereIn('status', ['Dijual', 'Proses Pembangunan', 'Tanah'])
                ->orderBy('created_at', 'DESC')
                ->findAll(6);

            if (!$rekomendasi) {
                $rekomendasi = (new PerumahanModel())
                    ->orderBy('created_at', 'DESC')
                    ->findAll(6);
            }

            return view('page/dashboard_customer', [
                'pageTitle' => 'Dashboard',
                'pembelian' => null,
                'rumah' => null,
                'rekomendasi' => $rekomendasi,
                'ringkasan' => null,
                'cicilanSlots' => [],
                'cicilanHariIni' => null,
                'progressNodes' => [],
                'infoBerkas' => null,
                'berkasItems' => [],
            ]);
        }

        $pembayaran = (new PembayaranRumahModel())
            ->where('pembelian_rumah_id', (int) $pembelian['id'])
            ->orderBy('created_at', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $ringkasan = $this->ringkasanCustomer($pembelian, $pembayaran);
        $cicilanSlots = $this->buildCicilanSlots($pembelian, $pembayaran);
        $cicilanHariIni = $this->cicilanHariIni($cicilanSlots);
        $progressNodes = $this->buildProgressNodes($pembelian, $pembayaran, $cicilanSlots, $cicilanHariIni);
        $infoBerkas = TransaksiRumahModel::infoBerkas($pembelian);
        $berkasItems = $this->berkasDashboardItems($infoBerkas);

        return view('page/dashboard_customer', [
            'pageTitle' => 'Dashboard',
            'pembelian' => $pembelian,
            'rumah' => $pembelian,
            'rekomendasi' => [],
            'ringkasan' => $ringkasan,
            'cicilanSlots' => $cicilanSlots,
            'cicilanHariIni' => $cicilanHariIni,
            'progressNodes' => $progressNodes,
            'infoBerkas' => $infoBerkas,
            'berkasItems' => $berkasItems,
        ]);
    }

    private function findCustomerPembelian(): ?array
    {
        $builder = (new PembelianRumahModel())
            ->select('pembelian_rumah.*, customer.nama AS nama_customer, perumahan.kode_rumah, perumahan.tipe, perumahan.lokasi, perumahan.harga, perumahan.gambar, perumahan.dokumen, perumahan.deskripsi, perumahan.luas_tanah, perumahan.luas_bangunan, perumahan.status AS status_rumah')
            ->join('customer', 'customer.id = pembelian_rumah.customer_id')
            ->join('perumahan', 'perumahan.id = pembelian_rumah.perumahan_id')
            ->where("LOWER(pembelian_rumah.status_pembelian) != 'batal'", null, false)
            ->orderBy('pembelian_rumah.created_at', 'DESC');

        $customerId = $this->resolveCustomerId();
        $userId = (int) (session()->get('user_id') ?? 0);
        $name = trim((string) session()->get('nama'));
        $username = trim((string) session()->get('username'));

        $builder->groupStart();
        $hasCondition = false;

        if ($customerId) {
            $builder->where('pembelian_rumah.customer_id', $customerId);
            $hasCondition = true;
        }

        if ($userId > 0) {
            if ($hasCondition) {
                $builder->orWhere('pembelian_rumah.user_id', $userId);
            } else {
                $builder->where('pembelian_rumah.user_id', $userId);
                $hasCondition = true;
            }
        }

        if ($name !== '') {
            if ($hasCondition) {
                $builder->orWhere('customer.nama', $name);
            } else {
                $builder->where('customer.nama', $name);
                $hasCondition = true;
            }
        }

        if ($username !== '' && strcasecmp($username, $name) !== 0) {
            if ($hasCondition) {
                $builder->orWhere('customer.nama', $username);
            } else {
                $builder->where('customer.nama', $username);
                $hasCondition = true;
            }
        }

        if (!$hasCondition) {
            $builder->where('pembelian_rumah.id', 0);
        }

        $builder->groupEnd();

        $row = $builder->first();

        return $row ?: null;
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

    private function ringkasanCustomer(array $pembelian, array $pembayaran): array
    {
        $hargaBeli = (int) ($pembelian['harga_beli'] ?? 0);
        $totalBayar = 0;
        $cicilanDisetujui = 0;
        $sudahBulanIni = false;
        $bulanIni = date('Y-m');

        foreach ($pembayaran as $row) {
            $status = strtolower((string) ($row['status_pengajuan'] ?? ''));
            $jenis = strtolower((string) ($row['jenis_pembayaran'] ?? ''));
            if ($status === 'disetujui') {
                $totalBayar += (int) ($row['jumlah_bayar'] ?? 0);
                if ($jenis === 'cicilan') {
                    $cicilanDisetujui++;
                }
            }

            if ($jenis === 'cicilan' && in_array($status, ['pending', 'disetujui'], true)) {
                $ref = (string) (($row['tanggal_bayar'] ?? '') ?: ($row['created_at'] ?? ''));
                if (substr($ref, 0, 7) === $bulanIni) {
                    $sudahBulanIni = true;
                }
            }
        }

        $sisaBayar = max($hargaBeli - $totalBayar, 0);
        $tahun = (int) ($pembelian['lama_cicilan_tahun'] ?? 0);
        $totalCicilan = $tahun > 0 ? $tahun * 12 : 0;
        $metode = strtolower((string) ($pembelian['metode_pembayaran'] ?? ''));
        $statusPembelian = strtolower((string) ($pembelian['status_pembelian'] ?? ''));
        $jumlahCicilan = $sisaBayar;

        if (($metode === 'cicilan internal' || $tahun > 0) && $totalCicilan > 0 && $sisaBayar > 0 && $metode !== 'cash') {
            $nominalTetap = (int) ceil($hargaBeli / $totalCicilan);
            $jumlahCicilan = ($cicilanDisetujui + 1 >= $totalCicilan)
                ? $sisaBayar
                : min($nominalTetap, $sisaBayar);
        } elseif (in_array($statusPembelian, ['booking', 'booked'], true)) {
            $jumlahCicilan = min(5000000, $sisaBayar > 0 ? $sisaBayar : 5000000);
        }

        $persen = $hargaBeli > 0 ? (int) round(($totalBayar / $hargaBeli) * 100) : 0;
        $mulai = (string) (($pembelian['tanggal_cicilan'] ?? '') ?: ($pembelian['tanggal_pembelian'] ?? ''));
        $selesai = ($mulai !== '' && $tahun > 0)
            ? date('Y-m-d', strtotime($mulai . ' +' . $tahun . ' years'))
            : '';

        return [
            'harga_beli' => $hargaBeli,
            'total_bayar' => $totalBayar,
            'sisa_bayar' => $sisaBayar,
            'status_pembelian' => $pembelian['status_pembelian'] ?? '-',
            'metode_pembayaran' => $pembelian['metode_pembayaran'] ?? '-',
            'total_cicilan' => $totalCicilan,
            'cicilan_disetujui' => $cicilanDisetujui,
            'jumlah_cicilan' => $jumlahCicilan,
            'sudah_cicilan_bulan_ini' => $sudahBulanIni,
            'persen' => min($persen, 100),
            'bisa_upload' => $sisaBayar > 0 && !$sudahBulanIni,
            'durasi_tahun' => $tahun,
            'durasi_text' => $tahun > 0 ? $tahun . ' tahun' : '-',
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'tanggal_mulai_display' => $this->formatTanggalId($mulai),
            'tanggal_selesai_display' => $this->formatTanggalId($selesai),
        ];
    }

    private function buildCicilanSlots(array $pembelian, array $pembayaran): array
    {
        $tahun = (int) ($pembelian['lama_cicilan_tahun'] ?? 0);
        $total = $tahun > 0 ? $tahun * 12 : 0;
        $today = date('Y-m-d');
        $currentYm = date('Y-m');

        $cicilan = [];
        foreach ($pembayaran as $row) {
            $jenis = strtolower((string) ($row['jenis_pembayaran'] ?? ''));
            $status = strtolower((string) ($row['status_pengajuan'] ?? ''));
            if ($jenis !== 'cicilan' || $status === 'ditolak') {
                continue;
            }
            $cicilan[] = $row;
        }

        if ($total <= 0) {
            $total = count($cicilan);
        }

        if ($total <= 0) {
            return [];
        }

        $byMonth = [];
        foreach ($cicilan as $row) {
            $ref = (string) (($row['tanggal_bayar'] ?? '') ?: ($row['created_at'] ?? ''));
            $ym = substr($ref, 0, 7);
            if (preg_match('/^\d{4}-\d{2}$/', $ym)) {
                $byMonth[$ym][] = $row;
            }
        }

        $usedIds = [];
        $slots = [];
        for ($i = 1; $i <= $total; $i++) {
            $due = PembelianRumahModel::jatuhTempoCicilan(
                $pembelian['tanggal_cicilan'] ?? null,
                $pembelian['tanggal_pembelian'] ?? null,
                $i - 1
            );
            $dueYm = $due ? substr($due, 0, 7) : null;
            $payment = null;
            if ($dueYm && !empty($byMonth[$dueYm])) {
                $payment = array_shift($byMonth[$dueYm]);
                $usedIds[(int) ($payment['id'] ?? 0)] = true;
            }

            $slots[] = [
                'cicilan_ke' => $i,
                'jatuh_tempo' => $due,
                'is_past' => $due ? ($due <= $today) : false,
                'is_current' => $dueYm === $currentYm,
                'payment' => $payment,
                'status' => $payment ? strtolower((string) ($payment['status_pengajuan'] ?? '')) : null,
            ];
        }

        $leftovers = [];
        foreach ($cicilan as $row) {
            if (empty($usedIds[(int) ($row['id'] ?? 0)])) {
                $leftovers[] = $row;
            }
        }

        foreach ($slots as &$slot) {
            if ($slot['payment'] || !$leftovers) {
                continue;
            }
            $slot['payment'] = array_shift($leftovers);
            $slot['status'] = strtolower((string) ($slot['payment']['status_pengajuan'] ?? ''));
        }
        unset($slot);

        return $slots;
    }

    private function cicilanHariIni(array $slots): ?int
    {
        foreach ($slots as $slot) {
            if (!empty($slot['is_current'])) {
                return (int) $slot['cicilan_ke'];
            }
        }

        $lastPast = null;
        foreach ($slots as $slot) {
            if (!empty($slot['is_past'])) {
                $lastPast = (int) $slot['cicilan_ke'];
                continue;
            }

            return (int) $slot['cicilan_ke'];
        }

        return $lastPast;
    }

    private function buildProgressNodes(array $pembelian, array $pembayaran, array $slots, ?int $cicilanHariIni): array
    {
        $dp = null;
        foreach ($pembayaran as $row) {
            $jenis = strtolower((string) ($row['jenis_pembayaran'] ?? ''));
            $status = strtolower((string) ($row['status_pengajuan'] ?? ''));
            if (in_array($jenis, ['dp', 'booking_fee'], true) && $status !== 'ditolak') {
                $dp = $row;
                break;
            }
        }

        $nodes = [];
        $nodes[] = [
            'key' => 'dp',
            'label' => 'DP',
            'is_dp' => true,
            'cicilan_ke' => 0,
            'status' => $dp ? strtolower((string) ($dp['status_pengajuan'] ?? '')) : null,
            'payment' => $dp,
            'is_current' => $cicilanHariIni === null && !$dp,
            'jatuh_tempo' => $pembelian['tanggal_pembelian'] ?? null,
            'bulan_label' => $this->formatBulanId($pembelian['tanggal_pembelian'] ?? null),
        ];

        foreach ($slots as $slot) {
            $due = $slot['jatuh_tempo'] ?? null;
            $nodes[] = [
                'key' => 'c' . (int) $slot['cicilan_ke'],
                'label' => (string) $slot['cicilan_ke'],
                'is_dp' => false,
                'cicilan_ke' => (int) $slot['cicilan_ke'],
                'status' => $slot['status'] ?? null,
                'payment' => $slot['payment'] ?? null,
                'is_current' => (int) $slot['cicilan_ke'] === (int) $cicilanHariIni,
                'jatuh_tempo' => $due,
                'bulan_label' => $this->formatBulanId($due),
            ];
        }

        $hasCurrent = false;
        foreach ($nodes as $node) {
            if (!empty($node['is_current'])) {
                $hasCurrent = true;
                break;
            }
        }
        if (!$hasCurrent) {
            foreach ($nodes as $i => $node) {
                if (($node['status'] ?? '') !== 'disetujui') {
                    $nodes[$i]['is_current'] = true;
                    break;
                }
            }
        }

        return $nodes;
    }

    private function berkasDashboardItems(array $infoBerkas): array
    {
        $short = [
            'ktp' => 'ktp',
            'surat_domisili' => 'domisili',
            'kk' => 'kk',
            'surat_nikah' => 'surat nikah',
            'npwp' => 'dokumen pajak',
            'surat_pernyataan' => 'surat pernyataan',
            'slip_gaji' => 'slip gaji',
            'surat_kerja' => 'surat kerja',
        ];

        $items = [];
        foreach (TransaksiRumahModel::JENIS_BERKAS as $key => $meta) {
            $file = $infoBerkas['uploaded'][$key] ?? null;
            $status = $infoBerkas['verifikasi'][$key] ?? ($file ? 'pending' : null);
            $items[] = [
                'key' => $key,
                'label' => $meta['label'],
                'short' => $short[$key] ?? $key,
                'wajib' => $meta['wajib'],
                'file' => $file,
                'status' => $status,
            ];
        }

        return $items;
    }

    private function formatTanggalId(?string $date): string
    {
        if (!$date) {
            return '-';
        }
        $ts = strtotime($date);
        if (!$ts) {
            return '-';
        }
        $bulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

        return date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }

    private function formatBulanId(?string $date): string
    {
        if (!$date) {
            return '-';
        }
        $ts = strtotime($date);
        if (!$ts) {
            return '-';
        }
        $bulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

        return $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }
}

