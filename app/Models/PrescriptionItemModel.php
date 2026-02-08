<?php

namespace App\Models;

use CodeIgniter\Model;

class PrescriptionItemModel extends Model
{
    protected $table = 'prescription_items';
    protected $primaryKey = 'item_id';

    protected $allowedFields = [
        'prescription_id',
        'medicine_id',
        'dosage',
        'quantity',
        'instructions'
    ];

    protected $useTimestamps = false;
}
