<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PembayaranRumahSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        if ($this->db->table('perumahan')->countAllResults() === 0) {
            $perumahanData = [
                [
                    'kode_rumah'     => 'RH001',
                    'lokasi'         => 'Jl. Melati No. 10',
                    'tipe'           => '36/60',
                    'luas_tanah'     => 60,
                    'luas_bangunan'  => 36,
                    'harga'          => 150000000,
                    'status'         => 'Terjual',
                    'created_at'     => $now,
                ],
                [
                    'kode_rumah'     => 'RH002',
                    'lokasi'         => 'Jl. Melati No. 12',
                    'tipe'           => '45/72',
                    'luas_tanah'     => 72,
                    'luas_bangunan'  => 45,
                    'harga'          => 200000000,
                    'status'         => 'Terjual',
                    'created_at'     => $now,
                ],
                [
                    'kode_rumah'     => 'RH003',
                    'lokasi'         => 'Jl. Mawar No. 5',
                    'tipe'           => '36/60',
                    'luas_tanah'     => 60,
                    'luas_bangunan'  => 36,
                    'harga'          => 155000000,
                    'status'         => 'Terjual',
                    'created_at'     => $now,
                ],
                [
                    'kode_rumah'     => 'RH004',
                    'lokasi'         => 'Jl. Mawar No. 7',
                    'tipe'           => '60/90',
                    'luas_tanah'     => 90,
                    'luas_bangunan'  => 60,
                    'harga'          => 280000000,
                    'status'         => 'Terjual',
                    'created_at'     => $now,
                ],
                [
                    'kode_rumah'     => 'RH005',
                    'lokasi'         => 'Jl. Anggrek No. 15',
                    'tipe'           => '36/60',
                    'luas_tanah'     => 60,
                    'luas_bangunan'  => 36,
                    'harga'          => 160000000,
                    'status'         => 'Dijual',
                    'created_at'     => $now,
                ],
            ];

            $this->db->table('perumahan')->insertBatch($perumahanData);
        }

        if ($this->db->table('customer')->countAllResults() === 0) {
            $customerData = [
                [
                    'perumahan_id'       => 1,
                    'nama'                => 'Budi Santoso',
                    'email'               => 'budi@gmail.com',
                    'telepon'             => '08123456789',
                    'alamat'              => 'Jl. Merdeka No. 50',
                    'tanggal_pembelian'   => '2025-01-15',
                    'created_at'          => $now,
                ],
                [
                    'perumahan_id'       => 2,
                    'nama'                => 'Siti Rahayu',
                    'email'               => 'siti@yahoo.com',
                    'telepon'             => '08198765432',
                    'alamat'              => 'Jl. Pahlawan No. 25',
                    'tanggal_pembelian'   => '2025-02-10',
                    'created_at'          => $now,
                ],
                [
                    'perumahan_id'       => 3,
                    'nama'                => 'Ahmad Wijaya',
                    'email'               => 'ahmad.w@gmail.com',
                    'telepon'             => '08234567890',
                    'alamat'              => 'Jl. Sudirman No. 100',
                    'tanggal_pembelian'   => '2025-03-05',
                    'created_at'          => $now,
                ],
                [
                    'perumahan_id'       => 4,
                    'nama'                => 'Dewi Lestari',
                    'email'               => 'dewi.l@hotmail.com',
                    'telepon'             => '08345678901',
                    'alamat'              => 'Jl. Gatot Subroto No. 75',
                    'tanggal_pembelian'   => '2025-04-20',
                    'created_at'          => $now,
                ],
            ];

            $this->db->table('customer')->insertBatch($customerData);
        }

        if ($this->db->table('pembelian_rumah')->countAllResults() === 0) {
            $dewiId = $this->customerIdByNama('Dewi Lestari');
            $budiId = $this->customerIdByNama('Budi Santoso');

            if ($dewiId === null || $budiId === null) {
                return;
            }

            $pembelianData = [
                [
                    'customer_id'         => $dewiId,
                    'perumahan_id'        => 3,
                    'tanggal_pembelian'   => '2025-03-20',
                    'harga_beli'          => 650000000,
                    'status_pembelian'    => 'DP',
                    'metode_pembayaran'   => 'Cash',
                    'lama_cicilan_tahun'  => null,
                    'status_dokumen'      => 'Pending',
                    'created_at'          => $now,
                ],
                [
                    'customer_id'         => $budiId,
                    'perumahan_id'        => 1,
                    'tanggal_pembelian'   => '2025-02-15',
                    'harga_beli'          => 350000000,
                    'status_pembelian'    => 'Cicil',
                    'metode_pembayaran'   => 'Cicilan Internal',
                    'lama_cicilan_tahun'  => 5,
                    'status_dokumen'      => 'Lengkap',
                    'created_at'          => $now,
                ],
            ];

            $this->db->table('pembelian_rumah')->insertBatch($pembelianData);
        }

        if ($this->db->table('pembayaran_rumah')->countAllResults() === 0) {
            $this->ensureApprovalColumns();

            $pembelianIds = array_column(
                $this->db->table('pembelian_rumah')->select('id')->orderBy('id', 'ASC')->get()->getResultArray(),
                'id'
            );

            $pembayaranTemplates = [
                [
                    'tanggal_bayar' => '2025-01-20',
                    'jumlah_bayar' => 30000000,
                    'jenis_pembayaran' => 'dp',
                    'metode_bayar' => 'Transfer Bank',
                    'keterangan' => 'Pembayaran DP rumah tahap awal',
                    'status_pengajuan' => 'disetujui',
                    'approved_at' => '2025-01-21 10:00:00',
                    'approved_by' => 1,
                ],
                [
                    'tanggal_bayar' => '2025-02-15',
                    'jumlah_bayar' => 25000000,
                    'jenis_pembayaran' => 'cicilan',
                    'metode_bayar' => 'Cash',
                    'keterangan' => 'Cicilan ke-1',
                    'status_pengajuan' => 'disetujui',
                    'approved_at' => '2025-02-16 09:00:00',
                    'approved_by' => 1,
                ],
                [
                    'tanggal_bayar' => '2025-03-07',
                    'jumlah_bayar' => 20000000,
                    'jenis_pembayaran' => 'booking_fee',
                    'metode_bayar' => 'Transfer Bank',
                    'keterangan' => 'Booking fee',
                    'status_pengajuan' => 'pending',
                    'approved_at' => null,
                    'approved_by' => null,
                ],
                [
                    'tanggal_bayar' => '2025-04-25',
                    'jumlah_bayar' => 50000000,
                    'jenis_pembayaran' => 'cicilan',
                    'metode_bayar' => 'Cicilan Internal',
                    'keterangan' => 'Pembayaran cicilan tahap awal',
                    'status_pengajuan' => 'disetujui',
                    'approved_at' => '2025-04-26 11:30:00',
                    'approved_by' => 1,
                ],
            ];

            $pembayaranData = [];
            foreach ($pembelianIds as $index => $pembelianId) {
                if (!isset($pembayaranTemplates[$index])) {
                    break;
                }

                $pembayaranData[] = array_merge($pembayaranTemplates[$index], [
                    'pembelian_rumah_id' => (int) $pembelianId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if ($pembayaranData !== []) {
                $this->db->table('pembayaran_rumah')->insertBatch($pembayaranData);
            }
        }
    }

    private function customerIdByNama(string $nama): ?int
    {
        $row = $this->db->table('customer')->select('id')->where('nama', $nama)->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }

    private function ensureApprovalColumns(): void
    {
        $forge = \Config\Database::forge();
        $fields = $this->db->getFieldNames('pembayaran_rumah');

        if (!in_array('status_pengajuan', $fields, true)) {
            $forge->addColumn('pembayaran_rumah', [
                'status_pengajuan' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'disetujui', 'ditolak'],
                    'default'    => 'disetujui',
                ],
            ]);
        }

        if (!in_array('approved_at', $fields, true)) {
            $forge->addColumn('pembayaran_rumah', [
                'approved_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
        }

        if (!in_array('approved_by', $fields, true)) {
            $forge->addColumn('pembayaran_rumah', [
                'approved_by' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                ],
            ]);
        }
    }
}
