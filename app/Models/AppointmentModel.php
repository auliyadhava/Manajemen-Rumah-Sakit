<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'appointment_id';

    protected $allowedFields = [
        'patient_id',
        'schedule_date',
        'department_id',
        'doctor_id',
        'status'
    ];
}
