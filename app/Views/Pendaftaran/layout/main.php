<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard' ?></title>

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- BOOTSTRAP ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/pendaftaran/sidebar.css') ?>">




</head>

<body>

    <div class="app-wrapper">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h4>RSUD Sehat</h4>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="<?= base_url('pendaftaran') ?>"
                        class="nav-link <?= uri_string() === 'pendaftaran' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('pendaftaran/pasien') ?>"
                        class="nav-link <?= uri_string() === 'pendaftaran/pasien' ? 'active' : '' ?>">
                        <i class="bi bi-person-plus"></i> Pendaftaran Pasien
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('pendaftaran/antrian') ?>"
                        class="nav-link <?= uri_string() === 'pendaftaran/antrian' ? 'active' : '' ?>">
                        <i class="bi bi-list-ol"></i> Antrian
                    </a>
                </li>
            </ul>

        </div>

        <!-- MAIN -->
        <div class="content-wrapper">

            <!-- TOP NAVBAR -->
            <nav class="top-navbar d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Registration</h5>

                <div class="dropdown user-info">
                    <a href="#" class="dropdown-toggle d-flex align-items-center"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-2"></i>
                        <?= session()->get('full_name') ?>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">Logout</a></li>
                    </ul>
                </div>
            </nav>

            <!-- ISI HALAMAN -->
            <?= $this->renderSection('content') ?>

        </div>
    </div>

    <!-- BOOTSTRAP JS (WAJIB UNTUK DROPDOWN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>