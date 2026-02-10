<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Harus login
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        }

        // 2. Proteksi berdasarkan folder URL
        $segment   = service('uri')->getSegment(1);
        $userRole  = session()->get('role');

        // Daftar role yang dilindungi
        $protectedRoles = ['admin', 'pasien', 'dokter', 'kasir'];

        // Jika akses folder tertentu tapi role tidak sesuai
        if (in_array($segment, $protectedRoles) && $segment !== $userRole) {
            return redirect()->to('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // kosong
    }
}
