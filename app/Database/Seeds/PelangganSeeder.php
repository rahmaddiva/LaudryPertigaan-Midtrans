<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PelangganSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_pelanggan' => 'Rahmad Diva',
                'no_telepon' => '081234567890',
                'alamat' => 'Jl. Mawar No. 12',
                'email' => 'rahmad@example.com',
                'password' => password_hash('rahmad123', PASSWORD_DEFAULT),
            ],
            [
                'nama_pelanggan' => 'Siti Aminah',
                'no_telepon' => '085678901234',
                'alamat' => 'Jl. Kenanga No. 8',
                'email' => 'siti@example.com',
                'password' => password_hash('siti123', PASSWORD_DEFAULT),
            ],
            [
                'nama_pelanggan' => 'Budi Santoso',
                'no_telepon' => '089912345678',
                'alamat' => 'Jl. Melati No. 10',
                'email' => 'budi@example.com',
                'password' => password_hash('budi123', PASSWORD_DEFAULT),
            ]
        ];

        // Insert batch
        $this->db->table('tb_pelanggan')->insertBatch($data);
    }
}
