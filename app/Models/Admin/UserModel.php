<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['username', 'password_hash', 'full_name', 'role_id', 'status'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
