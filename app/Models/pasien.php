<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Models\AppointmentModel;

class pasien extends Model
{
    protected $table = 'pasien';

    protected $fillable = [
        'user_id',
        'nik',
        'gender',
        'birth_date',
        'phone',
        'address'
    ];

    public function appointments()
    {
        return $this->hasMany(AppointmentModel::class);
    }
}
