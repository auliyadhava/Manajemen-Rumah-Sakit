<?php

namespace App\Models\Cashier;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table      = 'payments';
    protected $primaryKey = 'payment_id';

    // Perbaikan: sesuaikan dengan kolom di tabel database 'payments'
    protected $allowedFields = [
        'appointment_id',
        'amount',          // Di database namanya 'amount', bukan 'total_amount'
        'payment_method',
        'payment_status',
        'payment_date'
    ];

    // Karena di controller kita set manual date('Y-m-d H:i:s'), ini diset false
    protected $useTimestamps = false;

    // Opsional: Agar saat diambil datanya berbentuk Array
    protected $returnType = 'array';
}
