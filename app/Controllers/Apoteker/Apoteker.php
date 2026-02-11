<?php

namespace App\Controllers\Apoteker;

use App\Models\Pharmacy\PrescriptionModel;
use App\Models\Pharmacy\PrescriptionItemModel;
use App\Models\Pharmacy\MedicineModel;
use App\Models\Pharmacy\MedicinePickupModel;
use App\Models\Cashier\PaymentModel;
use App\Controllers\BaseController;

class Apoteker extends BaseController
{
    protected $prescription;
    protected $item;
    protected $medicine;
    protected $pickup;
    protected $payment;

    public function __construct()
    {
        $this->prescription = new PrescriptionModel();
        $this->item = new PrescriptionItemModel();
        $this->medicine = new MedicineModel();
        $this->pickup = new MedicinePickupModel();
        $this->payment = new PaymentModel();
    }

    /**
     * daftar resep yang sudah dibayar
     */
    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('prescriptions');
        $builder->select('
        prescriptions.prescription_id, 
        users.full_name as patient_name,
        medicine_pickups.pickup_id
    '); 
        $builder->join('examinations', 'examinations.exam_id = prescriptions.exam_id');
        $builder->join('appointments', 'appointments.appointment_id = examinations.appointment_id');
        $builder->join('patients', 'patients.patient_id = appointments.patient_id');
        $builder->join('users', 'users.user_id = patients.user_id');
        $builder->join('payments', 'payments.appointment_id = appointments.appointment_id');

        // Join ke tabel pengambilan
        $builder->join('medicine_pickups', 'medicine_pickups.prescription_id = prescriptions.prescription_id', 'left');

        // Filter 1: Harus sudah dibayar
        $builder->where('payments.payment_status', 'paid');

        // Filter 2: HANYA yang BELUM ada di tabel medicine_pickups
        $builder->where('medicine_pickups.pickup_id IS NULL');

        $resep = $builder->get()->getResultArray();

        return view('apoteker/index', [
            'resep' => $resep,
            'title' => 'Dashboard Apoteker'
        ]);
    }

    /**
     * detail resep
     */
    public function detail($id)
    {
        $data['items'] = $this->item
            ->select('prescription_items.*, medicines.name')
            ->join('medicines', 'medicines.medicine_id = prescription_items.medicine_id')
            ->where('prescription_id', $id)
            ->findAll();

        $data['prescription_id'] = $id;

        return view('apoteker/detail', $data);
    }

    /**
     * konfirmasi pengambilan obat
     */
    public function pickup($id)
    {
        $db = \Config\Database::connect();
        $db->transStart(); // Mulai proteksi

        $items = $this->item->where('prescription_id', $id)->findAll();

        foreach ($items as $item) {
            $this->medicine->set('stock', 'stock - ' . $item['quantity'], false)
                ->where('medicine_id', $item['medicine_id'])
                ->update();
        }

        $this->pickup->insert([
            'prescription_id' => $id,
            'pickup_date'     => date('Y-m-d H:i:s'),
            'picked_by'       => session()->get('user_id')
        ]);

        $db->transComplete(); // Selesaikan

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses pengambilan obat.');
        }

        return redirect()->to('/apoteker')->with('success', 'Obat berhasil diserahkan');
    }
}
