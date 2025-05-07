<?php

namespace App\Models;

use CodeIgniter\Model;

class LayananLaundryModel extends Model
{
    protected $table = 'layanan_laundry';
    protected $primaryKey = 'id_layanan';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['nama_layanan', 'harga_per_unit', 'satuan', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

}
