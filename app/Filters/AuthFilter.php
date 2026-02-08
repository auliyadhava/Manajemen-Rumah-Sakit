<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek apakah user sudah login?
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        }

        // 2. (Opsional tapi Bagus) Cek apakah Role sesuai dengan Folder URL?
        // Contoh: Jika URL diawali 'admin', tapi role user bukan 'admin', tendang keluar.
        $uri = service('uri');
        $segment = $uri->getSegment(1); // Mengambil kata pertama di URL (admin, kasir, dll)
        $userRole = session()->get('role');

        // Logika Proteksi Folder
        // Jika URL adalah 'admin' TAPI user bukan 'admin', blokir!
        if ($segment == 'admin' && $userRole != 'admin') {
            return redirect()->to('/'); 
        }
        
        // Lakukan hal yang sama untuk role lain jika perlu pengetatan ekstra
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}