<?php

namespace App\Controllers\Pasien;

use App\Controllers\BaseController;
use App\Models\Admin\UserModel;
use App\Models\Pasien\PatientModel;
use Config\Database;

class Register extends BaseController
{
    protected $userModel;
    protected $pasienModel;
    protected $db;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->pasienModel = new PatientModel();
        $this->db          = Database::connect();
    }

    public function index()
    {
        return view('pasien/register');
    }

    public function process()
    {
        // ================= VALIDASI =================
        if (!$this->validate([
            'username'   => 'required|is_unique[users.username]',
            'password'   => 'required|min_length[6]',
            'full_name'  => 'required',
            'nik'        => 'required|numeric|is_unique[patients.nik]',
            'gender'     => 'required|in_list[L,P]',
            'birth_date' => 'required|valid_date',
            'phone'      => 'required',
        ])) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // ================= TRANSAKSI =================
        $this->db->transStart();

        // Ambil role_id pasien
        $role = $this->db
            ->table('roles')
            ->where('role_name', 'pasien')
            ->get()
            ->getRow();

        $roleId = $role ? $role->role_id : 3;

        // ================= INSERT USERS =================
        $this->userModel->insert([
            'username'      => $this->request->getPost('username'),
            'password_hash' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'full_name' => $this->request->getPost('full_name'),
            'role_id'   => $roleId,
            'status'    => 'active'
        ]);

        $newUserId = $this->db->insertID();

        // ================= INSERT PATIENTS =================
        $this->pasienModel->insert([
            'user_id'    => $newUserId,
            'nik'        => $this->request->getPost('nik'),
            'gender'     => $this->request->getPost('gender'),
            'birth_date' => $this->request->getPost('birth_date'),
            'phone'      => $this->request->getPost('phone'),
            'address'    => $this->request->getPost('address'),
        ]);

        // ================= SELESAI =================
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Registrasi gagal, silakan coba lagi.');
        }

        return redirect()
            ->to('/')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }
}
