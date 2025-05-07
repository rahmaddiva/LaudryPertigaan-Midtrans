<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LayananLaundrySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_layanan' => 'Laundry Pakaian',
                'harga_per_unit' => 7000,
                'satuan' => 'kg',
            ],
            [
                'nama_layanan' => 'Laundry Karpet',
                'harga_per_unit' => 15000,
                'satuan' => 'm2',
            ],
            [
                'nama_layanan' => 'Laundry Boneka',
                'harga_per_unit' => 12000,
                'satuan' => 'pcs',
            ],
        ];

        $this->db->table('layanan_laundry')->insertBatch($data);
    }
}
