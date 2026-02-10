<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>RS Rawat Jalan - <?= $title ?? 'Administrator' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 240px;
            background: linear-gradient(180deg, #0d6efd, #084298);
            padding-top: 20px;
            padding-bottom: 20px;
            /* Tambahan padding bawah */
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);

            /* Agar Logout ada di paling bawah */
            display: flex;
            flex-direction: column;
        }

        .sidebar h4 {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        /* Menu Container untuk memisahkan menu atas dan tombol logout */
        .menu-items {
            flex-grow: 1;
            /* Mengisi ruang kosong */
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #e9ecef;
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        /* Styling Khusus Tombol Logout */
        .logout-link {
            margin-top: auto;
            /* Memastikan tetap di bawah */
            background-color: rgba(220, 53, 69, 0.1);
            /* Merah transparan */
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout-link:hover {
            background-color: #dc3545 !important;
            /* Merah Solid saat hover */
            color: white !important;
        }

        /* CONTENT */
        .content {
            margin-left: 240px;
            padding: 30px;
            min-height: 100vh;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h4>RS Panel</h4>

        <div class="menu-items">
            <a href="/dashboard">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <?php if (session()->get('role') == 'admin'): ?>
                <a href="/admin/users">
                    <i class="bi bi-people"></i> Manajemen User
                </a>
            <?php endif; ?>

        </div>
        <a href="/logout" class="logout-link" id="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <div class="content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <script>
        document.getElementById('btn-logout').addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah link langsung jalan

            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Anda harus login kembali untuk mengakses halaman ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "/logout"; // Redirect manual
                }
            })
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>