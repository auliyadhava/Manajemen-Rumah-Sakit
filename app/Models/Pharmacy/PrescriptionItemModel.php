<?php

namespace App\Models\Admin;
use CodeIgniter\Model;

class PrescriptionItemModel extends Model {
    protected $table = 'prescription_items';
    protected $allowedFields = ['prescription_id', 'medicine_id', 'dosage', 'quantity', 'instructions'];
}
