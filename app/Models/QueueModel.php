<?php

namespace App\Models;

use CodeIgniter\Model;

class QueueModel extends Model
{
    protected $table = 'queues';
    protected $primaryKey = 'queue_id';

    protected $allowedFields = [
        'appointment_id',
        'queue_number',
        'status',
        'call_time'
    ];
}
