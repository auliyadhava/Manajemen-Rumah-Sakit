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
        $db = \Config\Database::connect();

        // 1. Cek Data Appointment
        $appointment = $this->appointmentModel->find($appointment_id);

        if (!$appointment) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Hanya boleh konfirmasi hadir jika status sudah 'approved'
        if ($appointment['status'] != 'approved') {
            return redirect()->back()->with('error', 'Pasien belum disetujui admin (Approved) atau status tidak valid.');
        }

        // 2. Cek Duplikat Antrian
        $cekAntrian = $this->queueModel->where('appointment_id', $appointment_id)->first();
        if ($cekAntrian) {
            return redirect()->back()->with('error', 'Pasien ini sudah masuk antrian.');
        }

        $db->transStart();

        // 3. Update Status Appointment jadi 'waiting' (Menunggu Dokter)
        $this->appointmentModel->update($appointment_id, [
            'status' => 'waiting'
        ]);

        // 4. Hitung Nomor Antrean Hari Ini
        $today = date('Y-m-d');

        $lastQueue = $this->queueModel
            ->join('appointments', 'appointments.appointment_id = queues.appointment_id')
            ->where('appointments.schedule_date', $today)
            ->orderBy('queues.queue_number', 'DESC')
            ->first();

        $queueNumber = $lastQueue ? ($lastQueue['queue_number'] + 1) : 1;

        // 5. Insert ke Tabel Queues
        $this->queueModel->insert([
            'appointment_id' => $appointment_id,
            'queue_number'   => $queueNumber,
            'status'         => 'waiting',
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal memproses kehadiran.');
        }

        // 6. Ambil Data Lengkap untuk Tiket
        $detail = $this->appointmentModel
            ->select('users.full_name, departments.name as department_name, appointments.schedule_date')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->where('appointments.appointment_id', $appointment_id)
            ->first();

        // 7. Simpan Tiket ke Session
        session()->setFlashdata('queue_ticket', [
            'queue_number'   => str_pad($queueNumber, 3, '0', STR_PAD_LEFT),
            'full_name'      => $detail['full_name'],
            'department'     => $detail['department_name'],
            'schedule_date'  => $detail['schedule_date']
        ]);

        return redirect()->to('/pendaftaran/pasien')->with('success', 'Kehadiran Dikonfirmasi! Tiket dicetak.');
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
