<?php

namespace App\Models\Doctor;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'department_id';

    protected $allowedFields = ['name', 'description'];
}
