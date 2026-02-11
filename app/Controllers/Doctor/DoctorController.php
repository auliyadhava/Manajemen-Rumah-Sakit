<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;
use App\Models\Doctor\ExaminationModel;
use App\Models\Pharmacy\PrescriptionModel;
use App\Models\Pharmacy\PrescriptionItemModel;
use App\Models\Registration\AppointmentModel;
use App\Models\Registration\QueueModel;

class DoctorController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $appointmentModel = new AppointmentModel();
        
        // 1. Ambil ID User yang sedang login
        $userId = session()->get('user_id'); 

        // 2. Cari 'doctor_id' di tabel 'doctors' berdasarkan 'user_id' tersebut
        $dokterData = $db->table('doctors')->where('user_id', $userId)->get()->getRow();

        // Jika user ini ternyata tidak terdaftar di tabel doctors, tampilkan kosong
        if (!$dokterData) {
            return view('doctor/dashboard', ['patients' => []]);
        }

        $realDoctorId = $dokterData->doctor_id;

        // 3. Query Pasien
        $data['patients'] = $appointmentModel
            ->select('
                appointments.appointment_id,
                appointments.schedule_date,
                appointments.created_at,
                users.full_name as patient_name, 
                queues.queue_number,
                queues.status as queue_status
            ')
            // Join Data Pasien
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            
            // Join Antrian (Wajib ada karena Dokter hanya periksa yg sudah datang)
            ->join('queues', 'queues.appointment_id = appointments.appointment_id')
            
            // Filter Berdasarkan ID Dokter yang Benar
            ->where('appointments.doctor_id', $realDoctorId)

            // Filter status antrian (Waiting = Nunggu Dokter, Called = Dipanggil)
            ->whereIn('queues.status', ['waiting', 'called'])
            
            // Urutkan berdasarkan nomor antrian
            ->orderBy('queues.queue_number', 'ASC')
            ->findAll();

        return view('doctor/dashboard', $data);
    }

    // Function Simpan Pemeriksaan (Tetap sama, saya rapikan sedikit)
    public function submitExamination()
    {
        $db = \Config\Database::connect();
        $db->transStart(); 

        $examModel  = new ExaminationModel();
        $prescModel = new PrescriptionModel();
        $itemModel  = new PrescriptionItemModel();
        $appModel   = new AppointmentModel();
        $queueModel = new QueueModel();

        $appointmentId = $this->request->getPost('appointment_id');

        // 1. Simpan Rekam Medis
        $examId = $examModel->insert([
            'appointment_id' => $appointmentId,
            'doctor_id'      => $this->getDoctorId(), // Pakai helper function
            'complaint'      => $this->request->getPost('complaint'),
            'diagnosis'      => $this->request->getPost('diagnosis'),
            'notes'          => $this->request->getPost('notes'),
            'exam_date'      => date('Y-m-d H:i:s')
        ], true);

        // 2. Simpan Resep
        $medicines = $this->request->getPost('medicines');
        if (!empty($medicines)) {
            $prescId = $prescModel->insert(['exam_id' => $examId], true);

            foreach ($medicines as $med) {
                if(empty($med['id'])) continue; 

                $itemModel->insert([
                    'prescription_id' => $prescId,
                    'medicine_id'     => $med['id'],
                    'dosage'          => $med['dosage'],
                    'quantity'        => $med['qty'],
                    'instructions'    => $med['instructions']
                ]);
            }
        }

        // 3. Update Status Selesai
        $appModel->update($appointmentId, ['status' => 'completed']);
        $queueModel->where('appointment_id', $appointmentId)->set(['status' => 'done'])->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan data.');
        }

        return redirect()->to('/dokter')->with('success', 'Pemeriksaan selesai.');
    }
    
    // Halaman Periksa
    public function examine($appointmentId)
    {
        $db = \Config\Database::connect();
        
        $patient = $db->table('appointments')
            ->select('appointments.*, users.full_name, users.user_id, patients.birth_date, patients.gender, patients.nik')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->where('appointments.appointment_id', $appointmentId)
            ->get()->getRowArray();

        if (!$patient) {
            return redirect()->to('/dokter')->with('error', 'Pasien tidak ditemukan');
        }
        
        $medicines = $db->table('medicines')->get()->getResultArray();

        return view('doctor/examine', [
            'patient' => $patient,
            'medicines' => $medicines
        ]);
    }

    // Helper sederhana untuk ambil ID Dokter
    private function getDoctorId() {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $doc = $db->table('doctors')->where('user_id', $userId)->get()->getRow();
        return $doc ? $doc->doctor_id : 0;
    }
}