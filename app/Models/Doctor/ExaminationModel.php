<?php

namespace App\Models\Doctor;
use CodeIgniter\Model;

class ExaminationModel extends Model {
protected $table = 'examinations';
protected $primaryKey = 'exam_id';
protected $allowedFields = ['appointment_id', 'doctor_id', 'complaint', 'diagnosis', 'notes', 'exam_date'];
}