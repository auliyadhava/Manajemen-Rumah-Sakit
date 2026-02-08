<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\PrescriptionItemModel;
use App\Models\MedicineModel;
use App\Models\PaymentModel;

class Kasir extends BaseController
{
    protected $appointment;
    protected $item;
    protected $medicine;
    protected $payment;

    public function __construct()
    {
        $this->appointment = new AppointmentModel();
        $this->item = new PrescriptionItemModel();
        $this->medicine = new MedicineModel();
        $this->payment = new PaymentModel();
    }

    /**
     * daftar pasien yang siap dibayar
     */
    public function index()
    {
        $data['data'] = $this->appointment
            ->select('appointments.*, patients.patient_id')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('examinations', 'examinations.appointment_id = appointments.appointment_id')
            ->whereNotIn(
                'appointments.appointment_id',
                function ($builder) {
                    return $builder->select('appointment_id')->from('payments');
                }
            )
            ->findAll();

        return view('kasir/index', $data);
    }

    /**
     * detail tagihan
     */
    public function detail($appointmentId)
    {
        // ambil item resep
        $items = $this->item
            ->select('prescription_items.*, medicines.price')
            ->join('prescriptions', 'prescriptions.prescription_id = prescription_items.prescription_id')
            ->join('examinations', 'examinations.exam_id = prescriptions.exam_id')
            ->join('medicines', 'medicines.medicine_id = prescription_items.medicine_id')
            ->join('appointments', 'appointments.appointment_id = examinations.appointment_id')
            ->where('appointments.appointment_id', $appointmentId)
            ->findAll();

        $totalObat = 0;
        foreach ($items as $item) {
            $totalObat += $item['price'] * $item['quantity'];
        }

        $biayaPemeriksaan = 50000; // contoh statis (boleh kamu jadikan master)
        $total = $biayaPemeriksaan + $totalObat;

        return view('kasir/detail', [
            'items' => $items,
            'total_obat' => $totalObat,
            'biaya_pemeriksaan' => $biayaPemeriksaan,
            'total' => $total,
            'appointment_id' => $appointmentId
        ]);
    }

    /**
     * simpan pembayaran
     */
    public function bayar($appointmentId)
    {
        $this->payment->insert([
            'appointment_id' => $appointmentId,
            'total_amount' => $this->request->getPost('total'),
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_status' => 'paid',
            'payment_date' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/kasir')->with('success', 'Pembayaran berhasil');
    }
}
