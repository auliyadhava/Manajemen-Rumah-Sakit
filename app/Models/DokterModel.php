<?

namespace App\Models;

use CodeIgniter\Model;

class DokterModel extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama_dokter',
        'department_id',
        'no_telp'
    ];
}

