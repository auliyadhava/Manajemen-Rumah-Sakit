<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Admin\UserModel;
use App\Models\Admin\PatientModel;

class Register extends BaseController
{
    public function index()
    {
        return view('auth/register');
    }

    public function process()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $pasienModel = new PatientModel();

        // Validasi Input Sederhana
        if (!$this->validate([
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length(6)',
            'nik'      => 'required|numeric',
            'full_name'=> 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // --- MULAI TRANSAKSI DATABASE ---
        $db->transStart();

        // 1. Cari ID Role untuk 'pasien' (Biasanya ID nya dinamis, tapi kita cari berdasarkan nama)
        $role = $db->table('roles')->where('role_name', 'pasien')->get()->getRow();
        $roleId = $role ? $role->role_id : 3; // Default 3 jika tidak ketemu (sesuaikan dengan DB kamu)

        // 2. Simpan ke Tabel USERS
        $userData = [
            'username'      => $this->request->getPost('username'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'full_name'     => $this->request->getPost('full_name'),
            'role_id'       => $roleId,
            'status'        => 'active'
        ];
        
        // Insert User dan Ambil ID-nya
        $userModel->insert($userData);
        $newUserId = $userModel->getInsertID();

        // 3. Simpan ke Tabel PATIENTS
        $pasienData = [
            'user_id'    => $newUserId,
            'nik'        => $this->request->getPost('nik'),
            'gender'     => $this->request->getPost('gender'),
            'birth_date' => $this->request->getPost('birth_date'),
            'phone'      => $this->request->getPost('phone'),
            'address'    => $this->request->getPost('address')
        ];

        $pasienModel->insert($pasienData);

        // --- SELESAI TRANSAKSI ---
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Jika gagal, rollback otomatis
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftar, coba lagi.');
        }

        return redirect()->to('/')->with('success', 'Registrasi berhasil! Silakan Login.');
    }
}