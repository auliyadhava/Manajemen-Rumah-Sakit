<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\UserModel;
use App\Models\Admin\RoleModel;
use App\Models\Doctor\DoctorModel;
use App\Models\Doctor\DepartmentModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $doctorModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->doctorModel = new DoctorModel();
        $this->departmentModel = new DepartmentModel();
    }

    // 1. READ: Tampilkan semua user
    public function index()
    {
        // Join tabel users dengan roles agar nama role muncul (bukan angka ID)
        $data['users'] = $this->userModel
            ->select('users.*, roles.role_name')
            ->join('roles', 'roles.role_id = users.role_id')
            ->findAll();

        return view('admin/users/index', $data);
    }

    // 2. CREATE: Tampilkan form tambah
    public function create()
    {
        $data = [
            'roles'       => $this->roleModel->findAll(),
            'departments' => $this->departmentModel->findAll(),
        ];

        return view('admin/users/create', $data);
    }


    // 3. STORE: Proses simpan data baru
    public function store()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. SIMPAN USER
        $this->userModel->insert([
            'full_name'     => $this->request->getPost('full_name'),
            'username'      => $this->request->getPost('username'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'       => $this->request->getPost('role_id'),
            'status'        => 'active'
        ]);

        $userId = $this->userModel->getInsertID();

        // 2. CEK ROLE → DOKTER
        $role = $this->roleModel
            ->find($this->request->getPost('role_id'));

        if ($role && $role['role_name'] === 'dokter') {

            // VALIDASI KHUSUS DOKTER
            if (
                !$this->request->getPost('department_id') ||
                !$this->request->getPost('specialization')
            ) {

                $db->transRollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data dokter wajib diisi');
            }

            // SIMPAN DOKTER
            $this->doctorModel->insert([
                'user_id'        => $userId,
                'department_id'  => $this->request->getPost('department_id'),
                'specialization' => $this->request->getPost('specialization'),
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }

        return redirect()->to('/admin/users')->with('success', 'User berhasil ditambahkan');
    }



    // 4. EDIT: Tampilkan form edit
    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        // Ambil role
        $role = $this->roleModel->find($user['role_id']);

        // Ambil data dokter (jika role dokter)
        $doctor = null;
        if ($role && $role['role_name'] === 'dokter') {
            $doctor = $this->doctorModel
                ->where('user_id', $id)
                ->first();
        }

        $data = [
            'user'        => $user,
            'roles'       => $this->roleModel->findAll(),
            'departments' => $this->departmentModel->findAll(),
            'doctor'      => $doctor,
            'roleName'    => $role['role_name'] ?? null
        ];

        return view('admin/users/edit', $data);
    }


    // 5. UPDATE: Proses update data
    public function update($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Update USER
        $userData = [
            'full_name' => $this->request->getPost('full_name'),
            'username'  => $this->request->getPost('username'),
            'role_id'   => $this->request->getPost('role_id'),
        ];

        // Password optional
        if ($this->request->getPost('password')) {
            $userData['password_hash'] = password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            );
        }

        $this->userModel->update($id, $userData);

        // Cek role
        $role = $this->roleModel->find($this->request->getPost('role_id'));

        if ($role && $role['role_name'] === 'dokter') {

            $doctorData = [
                'department_id'  => $this->request->getPost('department_id'),
                'specialization' => $this->request->getPost('specialization'),
            ];

            $existingDoctor = $this->doctorModel
                ->where('user_id', $id)
                ->first();

            if ($existingDoctor) {
                $this->doctorModel->update($existingDoctor['doctor_id'], $doctorData);
            } else {
                $doctorData['user_id'] = $id;
                $this->doctorModel->insert($doctorData);
            }
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal update data');
        }

        return redirect()->to('/admin/users')->with('success', 'Data berhasil diperbarui');
    }


    // 6. DELETE: Hapus user
    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus');
    }
}
