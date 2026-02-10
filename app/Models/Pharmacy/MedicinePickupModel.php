<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class MedicinePickupModel extends Model
{
    protected $table = 'medicine_pickups';
    protected $primaryKey = 'pickup_id';

    protected $allowedFields = [
        'prescription_id',
        'pickup_date',
        'picked_by'
    ];

    protected $useTimestamps = false;
}
