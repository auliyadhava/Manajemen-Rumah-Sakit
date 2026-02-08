<?php namespace App\Controllers;

class Pasien extends BaseController {
    public function index() {
        // Alur: Dashboard riwayat medis pasien [cite: 139]
        return view('pasien/dashboard');
    }
    public function booking() {
        // Alur: Input ke tabel appointments [cite: 145]
    }
}