<!DOCTYPE html>
<html lang="id">

<head>
    <title><?= $title; ?> | RS Rawat Jalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 16.666667%;
            --bg-light: #f4f7f6;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            width: var(--sidebar-width);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 25px;
        }

        .nav-link {
            padding: 12px 15px;
            transition: 0.3s;
            color: rgba(255, 255, 255, 0.7) !important;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff !important;
        }

        .nav-link.text-danger:hover {
            background: rgba(255, 71, 87, 0.1);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card-stats {
            border-radius: 12px;
            border: none;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-dark sidebar p-3">
                <h5 class="text-white mb-4 mt-2"><i class="fas fa-hospital-alt me-2 text-primary"></i>RS Rawat Jalan</h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= base_url('kasir'); ?>">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <hr class="text-secondary">
                        <a class="nav-link text-danger fw-bold" href="<?= base_url('/logout'); ?>">
                            <i class="fas fa-power-off me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>

            <main class="col-md-10 main-content ms-sm-auto">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold text-dark">Dashboard Kasir</h4>
                    <span class="text-muted small"><?= date('l, d F Y'); ?></span>
                </div>
                <hr>

                <?php if (session()->getFlashdata('msg')) : ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('msg'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card card-stats bg-primary text-white p-4 shadow-sm">
                            <h6 class="opacity-75">Total Pembayaran Selesai</h6>
                            <h2 class="fw-bold mb-0"><?= $total_bayar; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="table-container shadow-sm border-0">
                    <h5 class="mb-4 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Antrian Pembayaran Pasien</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Nama Pasien</th>
                                    <th>Poliklinik</th>
                                    <th>Estimasi Tagihan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pending)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Tidak ada antrian pembayaran.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pending as $p): ?>
                                        <tr>
                                            <td class="fw-bold"><?= $p['full_name']; ?></td>
                                            <td><?= $p['poli']; ?></td>
                                            <td class="text-primary fw-bold">
                                                Rp <?= number_format(($p['total_amount'] ?? 50000), 0, ',', '.'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                                    <?= strtoupper($p['payment_status'] ?? 'PENDING'); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-success btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBayar<?= $p['appointment_id']; ?>">
                                                    <i class="fas fa-cash-register me-1"></i> Proses Bayar
                                                </button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="modalBayar<?= $p['appointment_id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?= base_url('kasir/prosesBayar'); ?>" method="post">
                                                        <?= csrf_field(); ?>
                                                        <input type="hidden" name="appointment_id" value="<?= $p['appointment_id']; ?>">

                                                        <div class="modal-body p-4 text-start">
                                                            <div class="mb-3">
                                                                <p class="text-muted mb-1">Nama Pasien:</p>
                                                                <h6 class="fw-bold"><?= $p['full_name']; ?></h6>
                                                            </div>
                                                            <div class="mb-4">
                                                                <p class="text-muted mb-1">Total Tagihan (Jasa + Obat):</p>
                                                                <h3 class="text-success fw-bold">Rp <?= number_format($p['total_amount'] ?? 50000, 0, ',', '.'); ?></h3>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Metode Pembayaran</label>
                                                                <select name="metode" class="form-select" required>
                                                                    <option value="cash">Tunai (Cash)</option>
                                                                    <option value="card">Kartu (Card/Debit)</option>
                                                                    <option value="insurance">Asuransi (Insurance)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success px-4 font-weight-bold">
                                                                Selesaikan Pembayaran
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>