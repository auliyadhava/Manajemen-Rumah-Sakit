<?php

namespace App\Models\Pasien;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';

    protected $allowedFields = [
        'user_id',
        'nik',
        'gender',
        'birth_date',
        'phone',
        'address'
    ];
}
