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

        // 1. Mengambil statistik jumlah pembayaran yang sudah 'paid'
        $totalSelesai = $db->table('payments')
            ->where('payment_status', 'paid')
            ->countAllResults();

        // 2. Mengambil daftar pasien yang PERLU membayar
        // Logic: Appointment status sudah 'completed' (dari Dokter) TAPI belum ada record di tabel payments
        $pendingPayments = $db->table('appointments')
            ->select('appointments.appointment_id, users.full_name, appointments.schedule_date, departments.name as poli')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('users', 'users.user_id = patients.user_id')
            ->join('departments', 'departments.department_id = appointments.department_id')
            ->join('payments', 'payments.appointment_id = appointments.appointment_id', 'left') // Join kiri untuk cari yang null
            ->where('appointments.status', 'completed') // Pasien sudah diperiksa dokter
            ->where('payments.payment_id IS NULL')      // Belum ada data pembayaran
            ->orderBy('appointments.schedule_date', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'       => 'Dashboard Kasir',
            'total_bayar' => $totalSelesai,
            'pending'     => $pendingPayments
        ];

        return view('kasir/dashboard', $data);
    }

    public function prosesBayar()
    {
        $db = \Config\Database::connect();
        $paymentModel = new PaymentModel();

        // Ambil data dari form (pastikan view mengirim appointment_id & metode)
        $appointmentId = $this->request->getPost('appointment_id');
        $metodeBayar   = $this->request->getPost('payment_method'); // enum: 'cash', 'credit_card', 'insurance'

        if (!$appointmentId) {
            return redirect()->back()->with('error', 'ID Tagihan tidak ditemukan.');
        }

        // 1. Hitung Total Biaya Obat secara Otomatis
        // Alur Join: appointments -> examinations -> prescriptions -> prescription_items -> medicines
        $queryTagihan = $db->table('appointments')
            ->join('examinations', 'examinations.appointment_id = appointments.appointment_id')
            ->join('prescriptions', 'prescriptions.exam_id = examinations.exam_id')
            ->join('prescription_items', 'prescription_items.prescription_id = prescriptions.prescription_id')
            ->join('medicines', 'medicines.medicine_id = prescription_items.medicine_id')
            ->where('appointments.appointment_id', $appointmentId)
            ->selectSum('medicines.price * prescription_items.quantity', 'grand_total') // Hitung (Harga x Qty)
            ->get()
            ->getRow();

        // Jika tidak ada obat, set 0
        $totalObat = $queryTagihan->grand_total ?? 0;

        // Biaya Jasa Dokter/Pendaftaran (Bisa dibuat dinamis dari tabel config jika ada)
        $biayaJasa = 50000;

        $totalAkhir = $totalObat + $biayaJasa;

        // 2. Simpan ke tabel payments
        // Catatan: Kolom di tabel Anda adalah 'amount', bukan 'total_amount'
        try {
            $paymentModel->insert([
                'appointment_id' => $appointmentId,
                'amount'         => $totalAkhir,
                'payment_method' => $metodeBayar,
                'payment_status' => 'paid',
                'payment_date'   => date('Y-m-d H:i:s')
            ]);

            // Opsional: Tidak perlu update status appointment ke 'completed' lagi 
            // karena sudah dilakukan oleh Dokter. Tapi jika ingin mengubah status jadi 'paid' 
            // di tabel appointment (jika kolom enum ditambah), lakukan di sini.

            return redirect()->to('/kasir')->with(
                'success',
                'Pembayaran Berhasil! Total: Rp ' . number_format($totalAkhir, 0, ',', '.')
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
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
