<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $allowedFields = [
        'appointment_id',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_date'
    ];

    protected $useTimestamps = false;
}
