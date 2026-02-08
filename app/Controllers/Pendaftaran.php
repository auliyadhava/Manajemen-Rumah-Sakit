<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\QueueModel;

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

        // Total pendaftaran terkonfirmasi hari ini
        $totalConfirmed = $this->appointmentModel
            ->where('status', 'confirmed')
            ->where('schedule_date', $today)
            ->countAllResults();

        // Total menunggu verifikasi
        $totalWaiting = $this->appointmentModel
            ->where('status', 'waiting')
            ->where('schedule_date', $today)
            ->countAllResults();

        // Total antrian hari ini
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

        return view('pendaftaran/dashboard', [
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

        return view('pendaftaran/pendaftaran_pasien', [
            'title'      => 'Pendaftaran Pasien',
            'dataPasien' => $dataPasien
        ]);
    }

    /* =======================
       KONFIRMASI PASIEN
       ======================= */
    public function konfirmasi($appointment_id)
    {
        // Ambil appointment + relasi
        $appointment = $this->appointmentModel
            ->select('
                appointments.appointment_id,
                users.full_name,
                departments.name AS department_name,
                appointments.schedule_date
            ')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->where('appointments.appointment_id', $appointment_id)
            ->first();

        if (!$appointment) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Update status appointment
        $this->appointmentModel->update($appointment_id, [
            'status' => 'confirmed'
        ]);

        // Hitung nomor antrian hari ini
        $today = date('Y-m-d');

        $lastQueue = $this->queueModel
            ->join('appointments', 'appointments.appointment_id = queues.appointment_id')
            ->where('appointments.schedule_date', $today)
            ->orderBy('queue_number', 'DESC')
            ->first();

        $queueNumber = $lastQueue ? $lastQueue['queue_number'] + 1 : 1;

        // Insert queue
        $this->queueModel->insert([
            'appointment_id' => $appointment_id,
            'queue_number'   => $queueNumber,
            'status'         => 'waiting'
        ]);

        // Simpan data tiket ke session
        session()->setFlashdata('queue_ticket', [
            'queue_number'   => $queueNumber,
            'full_name'      => $appointment['full_name'],
            'department'     => $appointment['department_name'],
            'schedule_date'  => $appointment['schedule_date']
        ]);

        return redirect()->to('/pendaftaran/pendaftaran_pasien');
    }

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
            ->join('appointments', 'appointments.appointment_id = queues.appointment_id')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->where('appointments.schedule_date', $today)
            ->orderBy('queues.queue_number', 'ASC')
            ->findAll();

        return view('Pendaftaran/antrian', [
            'title' => 'Antrian Pasien',
            'dataAntrian' => $dataAntrian
        ]);
    }
}
