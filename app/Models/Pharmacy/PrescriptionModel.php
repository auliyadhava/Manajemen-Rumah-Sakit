<?php

namespace App\Models\Admin;

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
