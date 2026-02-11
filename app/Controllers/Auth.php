<?php

namespace App\Controllers;

use App\Models\Admin\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika sudah login, langsung lempar ke dashboard masing-masing
        if (session()->get('logged_in')) {
            return $this->redirectBasedOnRole(session()->get('role'));
        }
        return view('auth/login');
    }

    public function loginProcess()
    {
        $session = session();
        $model = new UserModel();

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // Ambil data user + nama rolenya (JOIN table)
        // Kita butuh nama role (misal: 'admin') untuk logika redirect
        $data = $model->select('users.*, roles.role_name')
            ->join('roles', 'roles.role_id = users.role_id')
            ->where('username', $username)
            ->first();

        if ($data) {
            // Verifikasi Password
            if (password_verify($password, $data['password_hash'])) {

                // Cek Status Aktif
                if ($data['status'] != 'active') {
                    $session->setFlashdata('msg', 'Akun Anda dinonaktifkan.');
                    return redirect()->to('/');
                }

                // Simpan data penting ke SESSION
                $ses_data = [
                    'user_id'   => $data['user_id'],
                    'username'  => $data['username'],
                    'full_name' => $data['full_name'],
                    'role'      => $data['role_name'], // Penting untuk filter nanti
                    'logged_in' => TRUE
                ];
                $session->set($ses_data);

                // Arahkan sesuai Role
                return $this->redirectBasedOnRole($data['role_name']);
            } else {
                $session->setFlashdata('msg', 'Password salah.');
                return redirect()->to('/');
            }
        } else {
            $session->setFlashdata('msg', 'Username tidak ditemukan.');
            return redirect()->to('/');
        }
    }

    private function redirectBasedOnRole($role)
    {
        // Sesuaikan dengan folder controller teman-teman Anda
        switch ($role) {
            case 'admin':
                return redirect()->to('/admin/dashboard'); // Pastikan Controller Admin ada
            case 'dokter':
                return redirect()->to('/dokter/dashboard'); // Pastikan Controller Dokter ada
            case 'pendaftaran':
                return redirect()->to('/pendaftaran');      // Punya Rama
            case 'kasir':
                return redirect()->to('/kasir');            // Punya Marco
            case 'apoteker':
                return redirect()->to('/apoteker');         // Punya Hadi
            case 'pasien':
                return redirect()->to('/pasien');           // Punya Ardi
            default:
                return redirect()->to('/');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
