<?php

namespace App\Models;

use CodeIgniter\Model;

class ResepModel extends Model
{
    protected $table = 'prescriptions';
    protected $primaryKey = 'prescription_id';

    protected $allowedFields = [
        'patient_id',
        'doctor_id',
        'payment_status',
        'pickup_status'
    ];
}
