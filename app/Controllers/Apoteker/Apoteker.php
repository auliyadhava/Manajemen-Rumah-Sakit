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
        $data['resep'] = $this->prescription
            ->select('prescriptions.*, patients.patient_id')
            ->join('examinations', 'examinations.exam_id = prescriptions.exam_id')
            ->join('appointments', 'appointments.appointment_id = examinations.appointment_id')
            ->join('patients', 'patients.patient_id = appointments.patient_id')
            ->join('payments', 'payments.appointment_id = appointments.appointment_id')
            ->where('payments.payment_status', 'paid')
            ->whereNotIn(
                'prescriptions.prescription_id',
                function($builder) {
                    return $builder->select('prescription_id')->from('medicine_pickups');
                }
            )
            ->findAll();

        return view('Apoteker/index', $data);
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
        $items = $this->item->where('prescription_id', $id)->findAll();

        foreach ($items as $item) {
            // kurangi stok obat
            $this->medicine->set(
                'stock',
                'stock - ' . $item['quantity'],
                false
            )->where('medicine_id', $item['medicine_id'])->update();
        }

        // simpan pengambilan obat
        $this->pickup->insert([
            'prescription_id' => $id,
            'pickup_date' => date('Y-m-d H:i:s'),
            'picked_by' => session()->get('user_id')
        ]);

        return redirect()->to('/apoteker')->with('success', 'Obat berhasil diserahkan');
    }
}
