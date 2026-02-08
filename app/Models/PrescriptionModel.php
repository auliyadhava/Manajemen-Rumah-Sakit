<?php

namespace App\Models;

use CodeIgniter\Model;

class PrescriptionModel extends Model
{
    protected $table = 'prescriptions';
    protected $primaryKey = 'prescription_id';

    protected $allowedFields = [
        'exam_id',
        'created_at'
    ];

    protected $useTimestamps = false;
}
