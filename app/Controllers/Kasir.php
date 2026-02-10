<?php namespace App\Controllers;

use App\Models\PembayaranModel;
use App\Models\PendaftaranModel;

class Kasir extends BaseController {
    
    public function index() {
    $pembayaranModel = new PembayaranModel();
    $db = \Config\Database::connect();

    // Mengambil jumlah asli pembayaran yang sudah 'paid' dari database
    // Ini menghubungkan data yang sudah diproses sebelumnya 
    $totalSelesai = $db->table('payments')
                       ->where('payment_status', 'paid')
                       ->countAllResults();

    $data = [
        'title' => 'Dashboard Kasir',
        'total_bayar' => $totalSelesai, // Sekarang otomatis, bukan lagi 85 statis
        'pending' => $pembayaranModel->getPendingPayments() 
    ];
    
    return view('kasir/dashboard', $data);
}

    public function prosesBayar($id) {
        $pembayaranModel = new PembayaranModel();
        $db = \Config\Database::connect();

        // 1. Hitung Otomatis: Biaya Periksa + Total Harga Obat dari Dokter
        $totalObat = $db->table('prescription_items')
            ->join('prescriptions', 'prescriptions.prescription_id = prescription_items.prescription_id')
            ->join('examinations', 'examinations.exam_id = prescriptions.exam_id')
            ->join('medicines', 'medicines.medicine_id = prescription_items.medicine_id')
            ->where('examinations.appointment_id', $id)
            ->selectSum('prescription_items.quantity * medicines.price', 'total')
            ->get()->getRow()->total ?? 0;

        $biayaPemeriksaan = 50000; // Biaya standar [cite: 185]
        $grandTotal = $totalObat + $biayaPemeriksaan;

        // 2. Simpan ke tabel payments [cite: 108, 117]
        $pembayaranModel->save([
            'appointment_id' => $id,
            'total_amount'   => $grandTotal,
            'payment_method' => $this->request->getPost('metode'), // cash/card/insurance [cite: 114]
            'payment_status' => 'paid',
            'payment_date'   => date('Y-m-d H:i:s')
        ]);

        // 3. Update status pendaftaran jadi 'completed' 
        $appointmentModel = new PendaftaranModel();
        $appointmentModel->update($id, ['status' => 'completed']);

        return redirect()->to('/kasir')->with('msg', 'Pembayaran Berhasil Disimpan!');
    }
}