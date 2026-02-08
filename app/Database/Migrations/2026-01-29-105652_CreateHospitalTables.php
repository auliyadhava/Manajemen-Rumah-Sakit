<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHospitalTables extends Migration
{
    public function up()
    {
        // 1. Tabel Patients [cite: 26-36]
        $this->forge->addField([
            'patient_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11],
            'nik'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'gender'     => ['type' => 'ENUM', 'constraint' => ['L', 'P']],
            'birth_date' => ['type' => 'DATE'],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'    => ['type' => 'TEXT'],
        ]);
        $this->forge->addKey('patient_id', true);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('patients');

        // 2. Tabel Doctors [cite: 54-62]
        $this->forge->addField([
            'doctor_id'     => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11],
            'department_id' => ['type' => 'INT', 'constraint' => 11],
            'specialization'=> ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('doctor_id', true);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('department_id', 'departments', 'department_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctors');

        // 3. Tabel Appointments [cite: 37-49]
        $this->forge->addField([
            'appointment_id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'patient_id'     => ['type' => 'INT', 'constraint' => 11],
            'schedule_date'  => ['type' => 'DATE'],
            'department_id'  => ['type' => 'INT', 'constraint' => 11],
            'doctor_id'      => ['type' => 'INT', 'constraint' => 11],
            'status'         => ['type' => 'ENUM', 'constraint' => ['waiting', 'confirmed', 'cancelled', 'completed'], 'default' => 'waiting'],
            'created_at'     => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('appointment_id', true);
        $this->forge->addForeignKey('patient_id', 'patients', 'patient_id');
        $this->forge->addForeignKey('department_id', 'departments', 'department_id');
        $this->forge->addForeignKey('doctor_id', 'doctors', 'doctor_id');
        $this->forge->createTable('appointments');

        // 4. Tabel Queues [cite: 63-71]
        $this->forge->addField([
            'queue_id'       => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'appointment_id' => ['type' => 'INT', 'constraint' => 11],
            'queue_number'   => ['type' => 'INT'],
            'status'         => ['type' => 'ENUM', 'constraint' => ['waiting', 'called', 'done'], 'default' => 'waiting'],
            'call_time'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('queue_id', true);
        $this->forge->addForeignKey('appointment_id', 'appointments', 'appointment_id');
        $this->forge->createTable('queues');

        // 5. Tabel Examinations [cite: 72-83]
        $this->forge->addField([
            'exam_id'        => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'appointment_id' => ['type' => 'INT', 'constraint' => 11],
            'doctor_id'      => ['type' => 'INT', 'constraint' => 11],
            'complaint'      => ['type' => 'TEXT'],
            'diagnosis'      => ['type' => 'TEXT'],
            'notes'          => ['type' => 'TEXT', 'null' => true],
            'exam_date'      => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('exam_id', true);
        $this->forge->addForeignKey('appointment_id', 'appointments', 'appointment_id');
        $this->forge->createTable('examinations');

        // Tambahkan Tabel Lainnya (Prescriptions, Payments, dll) sesuai kebutuhan dokumen [cite: 84-134]
    }

    public function down() { /* Drop semua tabel di sini */ }
}