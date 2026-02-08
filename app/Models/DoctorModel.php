<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'doctor_id';

    protected $allowedFields = [
        'user_id',
        'department_id',
        'specialization'
    ];
}
