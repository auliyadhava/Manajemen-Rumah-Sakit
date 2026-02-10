<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Admin\UserModel;
use App\Models\Admin\RoleModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
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
        $data['roles'] = $this->roleModel->findAll(); // Ambil data role untuk dropdown
        return view('admin/users/create', $data);
    }

    // 3. STORE: Proses simpan data baru
    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'full_name' => 'required',
            'role_id'  => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Simpan ke database
        $this->userModel->save([
            'username'      => $this->request->getVar('username'),
            'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT), // Enkripsi password
            'full_name'     => $this->request->getVar('full_name'),
            'role_id'       => $this->request->getVar('role_id'),
            'status'        => 'active'
        ]);

        return redirect()->to('/admin/users')->with('success', 'User berhasil ditambahkan');
    }

    // 4. EDIT: Tampilkan form edit
    public function edit($id)
    {
        $data['user'] = $this->userModel->find($id);
        $data['roles'] = $this->roleModel->findAll();
        
        if (empty($data['user'])) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        return view('admin/users/edit', $data);
    }

    // 5. UPDATE: Proses update data
    public function update($id)
    {
        // Ambil data password baru jika ada
        $password = $this->request->getVar('password');
        
        $dataUpdate = [
            'user_id'   => $id,
            'full_name' => $this->request->getVar('full_name'),
            'role_id'   => $this->request->getVar('role_id'),
            'status'    => $this->request->getVar('status'),
        ];

        // Jika password diisi, update password baru. Jika kosong, biarkan password lama.
        if (!empty($password)) {
            $dataUpdate['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->save($dataUpdate);
        return redirect()->to('/admin/users')->with('success', 'Data user berhasil diperbarui');
    }

    // 6. DELETE: Hapus user
    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus');
    }
}