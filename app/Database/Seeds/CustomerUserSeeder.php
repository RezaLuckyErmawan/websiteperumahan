<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerUserSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $password = password_hash('customer123', PASSWORD_DEFAULT);

        $customers = $this->db->table('customer')
            ->select('id, nama')
            ->get()
            ->getResultArray();

        $customerIdsByName = array_column($customers, 'id', 'nama');

        $data = [
            $this->makeUser('Budi Santoso', 'budi', $password, $now, $customerIdsByName),
            $this->makeUser('Siti Rahayu', 'siti', $password, $now, $customerIdsByName),
            $this->makeUser('Ahmad Wijaya', 'ahmad', $password, $now, $customerIdsByName),
            $this->makeUser('Dewi Lestari', 'dewi', $password, $now, $customerIdsByName),
            $this->makeUser('Ahmad Subagyo', 'ahmad_subagyo', $password, $now, $customerIdsByName),
            $this->makeUser('Siti Mariam', 'siti_mariam', $password, $now, $customerIdsByName),
        ];

        $existingUsernames = $this->db->table('user')
            ->select('username')
            ->whereIn('username', array_column($data, 'username'))
            ->get()
            ->getResultArray();

        $existingUsernames = array_column($existingUsernames, 'username');

        foreach ($data as $user) {
            if (in_array($user['username'], $existingUsernames, true)) {
                $this->db->table('user')
                    ->where('username', $user['username'])
                    ->update([
                        'customer_id' => $user['customer_id'],
                        'role'        => 'customer',
                        'status'      => 'aktif',
                        'updated_at'  => $now,
                    ]);
                continue;
            }

            $this->db->table('user')->insert($user);
        }
    }

    private function makeUser(string $nama, string $username, string $password, string $now, array $customerIdsByName): array
    {
        return [
            'nama'        => $nama,
            'username'    => $username,
            'password'    => $password,
            'customer_id' => $customerIdsByName[$nama] ?? null,
            'role'        => 'customer',
            'status'      => 'aktif',
            'created_at'  => $now,
            'updated_at'  => $now,
        ];
    }
}
