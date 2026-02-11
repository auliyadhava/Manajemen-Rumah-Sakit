<?php

namespace App\Controllers\Pendaftaran;

use App\Controllers\BaseController;
use App\Models\Registration\AppointmentModel;
use App\Models\Registration\QueueModel;

class Pendaftaran extends BaseController
{
    protected $appointmentModel;
    protected $queueModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->queueModel       = new QueueModel();
    }

    /* =======================
       DASHBOARD PENDAFTARAN
       ======================= */
    public function index()
    {
        $today = date('Y-m-d');

        // Total pendaftaran terkonfirmasi/approved hari ini
        $totalConfirmed = $this->appointmentModel
            ->where('status', 'approved') // Ubah logic ke approved
            ->where('schedule_date', $today)
            ->countAllResults();

        // Total menunggu verifikasi (Pending)
        $totalWaiting = $this->appointmentModel
            ->where('status', 'pending')
            ->where('schedule_date', $today)
            ->countAllResults();

        // Total antrian hari ini (Waiting di RS)
        $totalQueue = $this->queueModel
            ->join('appointments', 'appointments.appointment_id = queues.appointment_id')
            ->where('appointments.schedule_date', $today)
            ->countAllResults();

        // Pendaftaran terbaru
        $pendaftaranTerbaru = $this->appointmentModel
            ->select('
                appointments.appointment_id,
                appointments.status,
                users.full_name,
                departments.name AS department_name
            ')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->orderBy('appointments.created_at', 'DESC')
            ->findAll(5);

        // Antrian terbaru
        $antrianTerbaru = $this->queueModel
            ->select('
                queues.queue_number,
                queues.status,
                users.full_name,
                departments.name AS department_name
            ')
            ->join('appointments', 'appointments.appointment_id = queues.appointment_id')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->orderBy('queues.queue_id', 'DESC')
            ->findAll(5);

        return view('Pendaftaran/dashboard', [
            'title'              => 'Dashboard Pendaftaran',
            'totalConfirmed'     => $totalConfirmed,
            'totalWaiting'       => $totalWaiting,
            'totalQueue'         => $totalQueue,
            'pendaftaranTerbaru' => $pendaftaranTerbaru,
            'antrianTerbaru'     => $antrianTerbaru
        ]);
    }

    /* =======================
       LIST PENDAFTARAN PASIEN
       ======================= */
    public function pasien()
    {
        // Menampilkan semua pasien (diurutkan dari yang terbaru daftar)
        $dataPasien = $this->appointmentModel
            ->select('
                appointments.appointment_id,
                appointments.status,
                appointments.schedule_date,
                users.full_name,
                departments.name AS department_name
            ')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->orderBy('appointments.created_at', 'DESC')
            ->findAll();

        return view('Pendaftaran/pendaftaran_pasien', [
            'title'      => 'Pendaftaran Pasien',
            'dataPasien' => $dataPasien
        ]);
    }

    /* =======================
       FITUR 1: VERIFIKASI ONLINE
       (Pending -> Approved/Rejected)
       ======================= */
    public function verifikasi($appointment_id, $status)
    {
        // Validasi input status
        if (!in_array($status, ['approved', 'rejected'])) {
            return redirect()->back()->with('error', 'Status tidak valid');
        }

        // Update status di database
        $this->appointmentModel->update($appointment_id, ['status' => $status]);

        // Feedback message
        $msg = ($status == 'approved')
            ? 'Jadwal disetujui. Pasien dapat melihat status diterima.'
            : 'Jadwal ditolak.';

        return redirect()->back()->with('success', $msg);
    }

    /* =======================
       FITUR 2: KONFIRMASI KEHADIRAN
       (Approved -> Waiting + Cetak Tiket)
       ======================= */
    public function konfirmasi_hadir($appointment_id)
    {
        // 1. Cek apakah yang akses adalah Petugas (Opsional: tambahkan filter auth)

        $appointment = $this->appointmentModel->find($appointment_id);

        if (!$appointment) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        // PASTIKAN: Hanya status 'approved' (janji temu valid) yang bisa check-in
        if ($appointment['status'] !== 'approved') {
            return redirect()->back()->with('error', 'Pasien belum diverifikasi atau sudah masuk antrean.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 2. Update Status ke 'waiting' (Artinya pasien sudah di lokasi & menunggu dokter)
        $this->appointmentModel->update($appointment_id, ['status' => 'waiting']);

        // 3. Generate Nomor Antrean (Hanya dibuat SAAT PASIEN DATANG)
        $today = date('Y-m-d');
        $lastQueue = $this->queueModel
            ->join('appointments', 'appointments.appointment_id = queues.appointment_id')
            ->where('appointments.schedule_date', $today)
            ->orderBy('queues.queue_number', 'DESC')
            ->first();

        $queueNumber = $lastQueue ? ($lastQueue['queue_number'] + 1) : 1;

        $this->queueModel->insert([
            'appointment_id' => $appointment_id,
            'queue_number'   => $queueNumber,
            'status'         => 'waiting', // Menunggu dipanggil poli
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        // 4. Siapkan Data Tiket untuk di-print petugas
        $detail = $this->appointmentModel
            ->select('users.full_name, departments.name as department_name')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->where('appointments.appointment_id', $appointment_id)
            ->first();

        session()->setFlashdata('queue_ticket', [
            'queue_number'  => str_pad($queueNumber, 3, '0', STR_PAD_LEFT),
            'full_name'     => $detail['full_name'],
            'department'    => $detail['department_name'],
            'schedule_date' => $today
        ]);

        return redirect()->to('/pendaftaran/pasien')->with('success', 'Check-in berhasil! Silakan cetak nomor antrean.');
    }

    /* =======================
       MONITORING ANTRIAN
       ======================= */
    public function antrian()
    {
        $today = date('Y-m-d');

        $dataAntrian = $this->queueModel
            ->select('
                queues.queue_number,
                queues.status,
                users.full_name,
                departments.name AS department_name
            ')
            ->join('appointments', 'appointments.appointment_id = queues.appointment_id', 'left')
            ->join('patients', 'patients.patient_id = appointments.patient_id', 'left')
            ->join('users', 'users.user_id = patients.user_id', 'left')
            ->join('departments', 'departments.department_id = appointments.department_id', 'left')


            ->orderBy('queues.queue_number', 'ASC')
            ->findAll();

        return view('Pendaftaran/antrian', [
            'title'       => 'Antrian Pasien',
            'dataAntrian' => $dataAntrian
        ]);
    }
}
