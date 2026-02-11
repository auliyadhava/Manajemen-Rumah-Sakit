<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>RS Rawat Jalan - <?= $title ?? 'Administrator' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: 260px;
            background: linear-gradient(180deg, #1e293b, #0f172a);
            display: flex; flex-direction: column; box-shadow: 2px 0 10px rgba(0,0,0,0.1); z-index: 1000;
        }

        .sidebar h4 { color: #fff; text-align: center; padding: 25px 0; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .menu-items { flex-grow: 1; overflow-y: auto; padding: 10px 0; }

        .sidebar a {
            display: flex; align-items: center; padding: 12px 25px; color: #94a3b8;
            text-decoration: none; font-size: 15px; transition: 0.3s;
        }

        .sidebar a i { margin-right: 12px; font-size: 1.2rem; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.05); color: #fff; border-left: 4px solid #3b82f6; }

        .logout-link { margin-top: auto; padding: 15px 25px; color: #f87171 !important; border-top: 1px solid rgba(255,255,255,0.1); }
        .logout-link:hover { background: #ef4444 !important; color: white !important; }

        .content { margin-left: 260px; padding: 35px; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    </style>
</head>

<body>

    <div class="sidebar">
        <h4><i class="bi bi-hospital"></i> RS PANEL</h4>

        <div class="menu-items">
            <a href="<?= base_url('admin/dashboard') ?>" class="<?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?= base_url('admin/pendaftaran') ?>">
                <i class="bi bi-pencil-square"></i> Pendaftaran
            </a>
            <a href="#">
                <i class="bi bi-person-check"></i> Kedatangan
            </a>
            <a href="#">
                <i class="bi bi-people"></i> Antrian
            </a>
            <a href="#">
                <i class="bi bi-stethoscope"></i> Pemeriksaan
            </a>
            <a href="#">
                <i class="bi bi-cash-stack"></i> Pembayaran
            </a>
            <a href="#">
                <i class="bi bi-capsule"></i> Pengambilan Obat
            </a>
            <a href="<?= base_url('admin/users') ?>" class="<?= (uri_string() == 'admin/users') ? 'active' : '' ?>">
                <i class="bi bi-person-gear"></i> Manajemen User
            </a>
        </div>

        <a href="/logout" class="logout-link" id="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <div class="content">
        <?= $this->renderSection('content') ?>
    </div>

    <script>
        document.getElementById('btn-logout').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Keluar dari sistem?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Logout'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = "<?= base_url('logout') ?>";
            })
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>