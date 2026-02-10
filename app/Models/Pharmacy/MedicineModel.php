<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class MedicineModel extends Model
{
    protected $table = 'medicines';
    protected $primaryKey = 'medicine_id';

    protected $allowedFields = [
        'name',
        'stock',
        'price'
    ];

    protected $useTimestamps = false;
}
