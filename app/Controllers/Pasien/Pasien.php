<?php

namespace App\Controllers\Pasien;

use CodeIgniter\Controller;

class Pasien extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /* ===============================
     * HELPER: ambil patient_id dari session
     * =============================== */
    private function getPatientId()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return null;
        }

        $patient = $this->db->table('patients')
            ->where('user_id', $userId)
            ->get()
            ->getRow();

        return $patient ? $patient->patient_id : null;
    }

    /* ===============================
     * 1. DASHBOARD PASIEN
     * =============================== */
    public function index()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        return view('pasien/dashboard');
    }

    /* ===============================
     * 2. FORM BOOKING
     * =============================== */
    public function booking()
    {
        $patientId = $this->getPatientId();

        if (!$patientId) {
            return redirect()->to('/login');
        }

        $data['departments'] = $this->db
            ->table('departments')
            ->get()
            ->getResult();

        return view('pasien/booking', $data);
    }

    /* ===============================
     * 2b. SIMPAN BOOKING
     * =============================== */
    public function store()
    {
        $patientId = $this->getPatientId();

        if (!$patientId) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        $departmentId = $this->request->getPost('department_id');

        // 🔍 Ambil 1 dokter berdasarkan poli
        $doctor = $db->table('doctors')
            ->where('department_id', $departmentId)
            ->get()
            ->getRow();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Dokter untuk poli ini belum tersedia.');
        }

        $data = [
            'patient_id'    => $patientId,
            'schedule_date' => $this->request->getPost('schedule_date'),
            'department_id' => $departmentId,
            'doctor_id'     => $doctor->doctor_id,
            'status'        => 'waiting'
        ];

        $db->table('appointments')->insert($data);

        return redirect()->to('/pasien/riwayat')->with('success', 'Booking berhasil dibuat!');
    }


    /* ===============================
     * 3. RIWAYAT APPOINTMENT
     * =============================== */
    public function riwayat()
    {
        $patientId = $this->getPatientId();

        if (!$patientId) {
            return redirect()->to('/login');
        }

        $data['appointments'] = $this->db->query("
            SELECT 
                a.appointment_id,
                a.schedule_date,
                a.status,
                d.name AS department
            FROM appointments a
            JOIN departments d ON d.department_id = a.department_id
            WHERE a.patient_id = ?
            ORDER BY a.schedule_date DESC
        ", [$patientId])->getResult();

        return view('pasien/riwayat', $data);
    }

    /* ===============================
     * 4. MONITOR ANTRIAN
     * =============================== */
    public function antrian()
    {
        $patientId = $this->getPatientId();

        if (!$patientId) {
            return redirect()->to('/login');
        }

        $data['queues'] = $this->db->query("
            SELECT 
                q.queue_number,
                q.status,
                a.schedule_date,
                d.name AS department
            FROM queues q
            JOIN appointments a ON a.appointment_id = q.appointment_id
            JOIN departments d ON d.department_id = a.department_id
            WHERE a.patient_id = ?
              AND q.status != 'done'
            ORDER BY q.queue_id DESC
        ", [$patientId])->getResult();

        return view('pasien/antrian', $data);
    }

    /* ===============================
     * 5. DETAIL PEMERIKSAAN
     * =============================== */
    public function detail_pemeriksaan($appointment_id)
    {
        $patientId = $this->getPatientId();

        if (!$patientId) {
            return redirect()->to('/login');
        }

        // pemeriksaan
        $data['pemeriksaan'] = $this->db
            ->table('examinations')
            ->where('appointment_id', $appointment_id)
            ->get()
            ->getRow();

        // resep
        if ($data['pemeriksaan']) {
            $data['resep'] = $this->db->query("
                SELECT 
                    m.name AS nama_obat,
                    pi.dosage,
                    pi.quantity,
                    pi.instructions
                FROM prescriptions p
                JOIN prescription_items pi ON p.prescription_id = pi.prescription_id
                JOIN medicines m ON pi.medicine_id = m.medicine_id
                WHERE p.exam_id = ?
            ", [$data['pemeriksaan']->exam_id])->getResult();
        } else {
            $data['resep'] = [];
        }

        // pembayaran
        $data['pembayaran'] = $this->db
            ->table('payments')
            ->where('appointment_id', $appointment_id)
            ->get()
            ->getRow();

        return view('pasien/detail_pemeriksaan', $data);
    }
}
