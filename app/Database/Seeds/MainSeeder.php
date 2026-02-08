<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // Isi Role [cite: 25, 137]
        $roles = [
            ['role_name' => 'admin'],
            ['role_name' => 'pendaftaran'],
            ['role_name' => 'dokter'],
            ['role_name' => 'apoteker'],
            ['role_name' => 'kasir'],
            ['role_name' => 'pasien'],
        ];
        $this->db->table('roles')->insertBatch($roles);

        // Isi User Admin Awal [cite: 198-201]
        $admin = [
            'username'      => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'full_name'     => 'Administrator RS',
            'role_id'       => 1, // role_id admin
            'status'        => 'active'
        ];
        $this->db->table('users')->insert($admin);
    }
}