<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Pasien extends Controller
{
    // --- 1. Dashboard Utama ---
    public function index()
    {
        return view('pasien/dashboard');
    }

    // --- 2. Booking Online (Poin 2) ---
    public function booking()
    {
        $db = \Config\Database::connect();
        // Mengambil daftar poli agar dropdown di view dinamis
        $data['departments'] = $db->table('departments')->get()->getResult();
        return view('pasien/booking', $data);
    }

    // Proses simpan booking ke database
    public function store()
    {
        $db = \Config\Database::connect();

        // Ambil ID User dari Session Login
        $userId = session()->get('user_id');

        // Cari ID Patient berdasarkan User ID tersebut
        $pasien = $db->table('patients')->where('user_id', $userId)->get()->getRow();

        if (!$pasien) {
            return redirect()->back()->with('error', 'Data pasien tidak ditemukan.');
        }

        $data = [
            'patient_id'    => $pasien->patient_id, // Sekarang Dinamis!
            'schedule_date' => $this->request->getPost('schedule_date'),
            'department_id' => $this->request->getPost('department_id'),
            'doctor_id'     => 1, // Logic pemilihan dokter bisa dikembangkan nanti
            'status'        => 'waiting'
        ];

        $db->table('appointments')->insert($data);

        return redirect()->to('/pasien/riwayat')->with('success', 'Booking berhasil dibuat!');
    }

    // --- 3. Riwayat Appointment (Poin 3) ---
    public function riwayat()
    {
        $db = \Config\Database::connect();

        // Mengambil riwayat booking pasien
        $query = $db->query("
            SELECT a.appointment_id,
                   a.schedule_date,
                   a.status,
                   d.name AS department
            FROM appointments a
            JOIN departments d ON d.department_id = a.department_id
            WHERE a.patient_id = 3
            ORDER BY a.schedule_date DESC
        ");

        $data['appointments'] = $query->getResult();

        return view('pasien/riwayat', $data);
    }

    // --- 5. Monitor Antrian (Poin 5) ---
    public function antrian()
    {
        $db = \Config\Database::connect();

        // Menampilkan antrian aktif milik pasien
        $query = $db->query("
            SELECT q.queue_number,
                   q.status,
                   a.schedule_date,
                   d.name AS department
            FROM queues q
            JOIN appointments a ON a.appointment_id = q.appointment_id
            JOIN departments d ON d.department_id = a.department_id
            WHERE a.patient_id = 3
            AND q.status != 'done'
            ORDER BY q.queue_id DESC
        ");

        $data['queues'] = $query->getResult();

        return view('pasien/antrian', $data);
    }

    // --- 7, 8, 9. Hasil Medis, Resep, & Pembayaran (Poin 7-9) ---
    public function detail_pemeriksaan($appointment_id)
    {
        $db = \Config\Database::connect();

        // Ambil Data Pemeriksaan/Diagnosa (Poin 7)
        $data['pemeriksaan'] = $db->table('examinations')
            ->where('appointment_id', $appointment_id)
            ->get()->getRow();

        // Ambil Data Resep Obat (Poin 9)
        // Jika data pemeriksaan sudah ada, ambil resepnya
        if ($data['pemeriksaan']) {
            $data['resep'] = $db->query(
                "
                SELECT m.name as nama_obat, pi.dosage, pi.quantity, pi.instructions
                FROM prescriptions p
                JOIN prescription_items pi ON p.prescription_id = pi.prescription_id
                JOIN medicines m ON pi.medicine_id = m.medicine_id
                WHERE p.exam_id = " . $data['pemeriksaan']->exam_id
            )->getResult();
        } else {
            $data['resep'] = [];
        }

        // Ambil Data Pembayaran (Poin 8)
        $data['pembayaran'] = $db->table('payments')
            ->where('appointment_id', $appointment_id)
            ->get()->getRow();

        return view('pasien/detail_pemeriksaan', $data);
    }
}
