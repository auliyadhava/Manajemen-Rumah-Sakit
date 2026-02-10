<?php

namespace App\Controllers\Doctor;

use App\Models\Doctor\ExaminationModel;
use App\Models\Pharmacy\PrescriptionModel;
use App\Models\Pharmacy\PrescriptionItemModel;
use App\Models\Registration\AppointmentModel;
use App\Models\Registration\QueueModel;
use App\Controllers\BaseController;

class DoctorController extends BaseController
{
    // Menampilkan daftar antrean pasien hari ini untuk dokter yang login
    public function index()
    {
        $appointmentModel = new AppointmentModel();
        $doctorId = session()->get('user_id');

        $data['patients'] = $appointmentModel->select('appointments.*, users.full_name as patient_name, queues.queue_number')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('queues', 'queues.appointment_id = appointments.appointment_id')
            ->where('appointments.doctor_id', $doctorId)
            ->where('appointments.schedule_date', date('Y-m-d'))
            ->where('appointments.status', 'confirmed') // Pasien yang sudah datang
            ->findAll();

        return view('doctor/dashboard', $data);
    }

    // Menyimpan hasil pemeriksaan dan resep (Database Transaction)
    public function submitExamination()
    {
        $db = \Config\Database::connect();
        $db->transStart(); // Proteksi data agar konsisten

        $examModel = new ExaminationModel();
        $prescModel = new PrescriptionModel();
        $itemModel = new PrescriptionItemModel();
        $appModel  = new AppointmentModel();
        $queueModel = new QueueModel();

        $appointmentId = $this->request->getPost('appointment_id');

        // 1. Simpan Rekam Medis
        $examId = $examModel->insert([
            'appointment_id' => $appointmentId,
            'doctor_id'      => session()->get('user_id'),
            'complaint'      => $this->request->getPost('complaint'),
            'diagnosis'      => $this->request->getPost('diagnosis'),
            'notes'          => $this->request->getPost('notes'),
            'exam_date'      => date('Y-m-d H:i:s')
        ], true);

        // 2. Simpan Resep jika ada obat yang dipilih
        $medicines = $this->request->getPost('medicines');
        if (!empty($medicines)) {
            $prescId = $prescModel->insert(['exam_id' => $examId], true);

            foreach ($medicines as $med) {
                $itemModel->insert([
                    'prescription_id' => $prescId,
                    'medicine_id'     => $med['id'],
                    'dosage'          => $med['dosage'],
                    'quantity'        => $med['qty'],
                    'instructions'    => $med['instructions']
                ]);
            }
        }

        // 3. Update Status Appointment & Antrian menjadi Selesai
        $appModel->update($appointmentId, ['status' => 'completed']);
        $queueModel->where('appointment_id', $appointmentId)->set(['status' => 'done'])->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }

        return redirect()->to('/doctor')->with('success', 'Data pemeriksaan berhasil disimpan.');
    }
}
