<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Admin extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $today = date('Y-m-d');

        $data = [
            'title'   => 'Dashboard Admin',
            'stats'   => [
                'pasien'      => $this->db->table('appointments')->where('schedule_date', $today)->countAllResults(),
                'pemeriksaan' => $this->db->table('examinations')->countAllResults(),
                'pembayaran'  => $this->db->table('payments')->where('payment_status', 'paid')->countAllResults(),
                'obat'        => $this->db->table('medicine_pickups')->countAllResults(),
            ],
            // Mengambil 5 aktivitas pemeriksaan terakhir
            'riwayat' => $this->db->table('examinations')
                ->select('users.full_name as pasien, departments.name as poli, diagnosis, exam_date')
                ->join('appointments', 'appointments.appointment_id = examinations.appointment_id')
                ->join('patients', 'patients.patient_id = appointments.patient_id')
                ->join('users', 'users.user_id = patients.user_id')
                ->join('departments', 'departments.department_id = appointments.department_id')
                ->orderBy('exam_date', 'DESC')
                ->limit(5)
                ->get()->getResultArray()
        ];

        return view('Admin/index', $data);
    }

    public function pendaftaran()
    {
        $db = \Config\Database::connect();

        // Query untuk mengambil data pendaftaran
        $data['pendaftaran'] = $db->table('appointments')
            ->select('appointments.*, users.full_name, departments.name as poli, d_user.full_name as nama_dokter')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->join('doctors', 'doctors.doctor_id = appointments.doctor_id', 'left')
            ->join('users d_user', 'd_user.user_id = doctors.user_id', 'left')
            ->get()->getResultArray();

        $data['title'] = "Pendaftaran";

        return view('Admin/pendaftaran', $data);
    }

    public function edit_pendaftaran($id)
    {
        $db = \Config\Database::connect();

        // Ambil data pendaftaran berdasarkan ID
        $data['pendaftaran'] = $db->table('appointments')->where('appointment_id', $id)->get()->getRowArray();

        // Ambil data pendukung untuk dropdown
        $data['pasien'] = $db->table('patients')
            ->join('users', 'users.user_id = patients.user_id')
            ->get()->getResultArray();

        $data['poli'] = $db->table('departments')->get()->getResultArray();

        $data['dokter'] = $db->table('doctors')
            ->join('users', 'users.user_id = doctors.user_id')
            ->get()->getResultArray();

        $data['title'] = "Edit Pendaftaran";

        return view('Admin/edit_pendaftaran', $data);
    }

    public function update_pendaftaran($id)
    {
        $db = \Config\Database::connect();

        $data = [
            'patient_id'    => $this->request->getPost('patient_id'),
            'department_id' => $this->request->getPost('department_id'),
            'doctor_id'     => $this->request->getPost('doctor_id'),
            'schedule_date' => $this->request->getPost('schedule_date'),
            'status'        => $this->request->getPost('status'),
        ];

        $db->table('appointments')->where('appointment_id', $id)->update($data);

        return redirect()->to(base_url('admin/pendaftaran'))->with('success', 'Data pendaftaran berhasil diperbarui');
    }

    public function users()
    {
        return view('Admin/users', ['title' => 'Manajemen User']);
    }
}
