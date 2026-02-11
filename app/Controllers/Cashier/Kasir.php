<?php

namespace App\Controllers\Cashier;

use App\Controllers\BaseController;
use App\Models\Cashier\PaymentModel;
use App\Models\Registration\AppointmentModel;

class Kasir extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Statistik jumlah yang sudah bayar (Gunakan nama kolom asli: payment_status)
        $totalSelesai = $db->table('payments')
            ->where('payment_status', 'paid')
            ->countAllResults();

        // Daftar pasien yang perlu membayar
        $pendingPayments = $db->table('appointments')
            ->select('
            appointments.appointment_id, 
            users.full_name, 
            departments.name as poli, 
            "50000" as total_amount, 
            "pending" as payment_status
        ')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->join('payments', 'payments.appointment_id = appointments.appointment_id', 'left')
            ->where('appointments.status', 'completed')
            ->where('payments.payment_id IS NULL')
            ->get()
            ->getResultArray();

        return view('kasir/dashboard', [
            'title'       => 'Dashboard Kasir',
            'total_bayar' => $totalSelesai,
            'pending'     => $pendingPayments
        ]);
    }

    public function prosesBayar()
    {
        $db = \Config\Database::connect();

        // 1. Ambil data dari form
        $appointmentId = $this->request->getPost('appointment_id');
        $metodeBayar   = $this->request->getPost('metode'); // Sesuaikan name di select view

        if (!$appointmentId) {
            return redirect()->back()->with('error', 'ID Transaksi tidak ditemukan.');
        }

        // 2. Hitung Total Tagihan Otomatis (Join ke Resep)
        $queryTagihan = $db->table('appointments')
            ->join('examinations', 'examinations.appointment_id = appointments.appointment_id')
            ->join('prescriptions', 'prescriptions.exam_id = examinations.exam_id')
            ->join('prescription_items', 'prescription_items.prescription_id = prescriptions.prescription_id')
            ->join('medicines', 'medicines.medicine_id = prescription_items.medicine_id')
            ->where('appointments.appointment_id', $appointmentId)
            ->select('SUM(medicines.price * prescription_items.quantity) as grand_total', FALSE)
            ->get()
            ->getRow();

        $totalObat = $queryTagihan->grand_total ?? 0;

        $totalObat = $queryTagihan->grand_total ?? 0;
        $biayaJasa = 50000;
        $totalAkhir = $totalObat + $biayaJasa;

        // 3. Simpan ke tabel payments (Gunakan 'total_amount' sesuai rs (3).sql)
        try {
            $db->table('payments')->insert([
                'appointment_id' => $appointmentId,
                'total_amount'   => $totalAkhir,
                'payment_status' => 'paid',
                'payment_date'   => date('Y-m-d H:i:s')
                // 'method'       => $metodeBayar, // Aktifkan jika ada kolomnya
            ]);

            return redirect()->to('/kasir')->with('msg', 'Pembayaran Berhasil! Total: Rp ' . number_format($totalAkhir, 0, ',', '.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // Fitur Tambahan: Untuk melihat detail rincian sebelum bayar (AJAX/Modal)
    public function detailTagihan($appointmentId)
    {
        $db = \Config\Database::connect();

        $items = $db->table('appointments')
            ->join('examinations', 'examinations.appointment_id = appointments.appointment_id')
            ->join('prescriptions', 'prescriptions.exam_id = examinations.exam_id')
            ->join('prescription_items', 'prescription_items.prescription_id = prescriptions.prescription_id')
            ->join('medicines', 'medicines.medicine_id = prescription_items.medicine_id')
            ->where('appointments.appointment_id', $appointmentId)
            ->select('medicines.name, medicines.price, prescription_items.quantity')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($items);
    }
}
