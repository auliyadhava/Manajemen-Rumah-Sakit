<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pembayaran Pasien | RS Rawat Jalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --sidebar-width: 16.666667%;
            --primary-color: #0d6efd;
            --danger-color: #ff4757;
            --success-color: #2ed573;
            --bg-light: #f4f7f6;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            overflow-x: hidden; 
        }

        /* Sidebar Styling */
        .sidebar { 
            min-height: 100vh; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 100; 
            width: var(--sidebar-width); 
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

         /* Nav Link Biasa (Tanpa Efek Menyala Active) */
        .nav-link {
            padding: 12px 15px;
            transition: 0.3s;
            color: rgba(255,255,255,0.7) !important;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff !important;
        }

        /* Khusus Logout agar tetap jelas */
        .nav-link.text-danger:hover {
            background: rgba(255, 71, 87, 0.1);
        }

        /* Main Content Area */
        .main-content { 
            margin-left: var(--sidebar-width); 
            padding: 30px;
        }

        .card-custom {
            background: white; 
            border-radius: 15px; 
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            padding: 25px;
        }

        .header-title { font-weight: 800; color: #2c3e50; }

        .badge-unpaid { 
            background-color: var(--danger-color); 
            color: white; 
            padding: 6px 12px; 
            border-radius: 6px; 
            font-size: 0.75rem;
            font-weight: 600;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #edf2f7;
            color: #718096;
            text-transform: uppercase;
            font-size: 0.75rem;
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
                    <a class="nav-link text-white" href="<?= base_url('dashboard_kasir'); ?>">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= base_url('kasir'); ?>">
                        <i class="fas fa-money-bill-wave me-2"></i> Pembayaran Pasien
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

        <main class="col-md-10 main-content ms-sm-auto px-md-4 py-4">
            
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="header-title">Data Pembayaran</h2>
                    <p class="text-muted">Daftar transaksi dan status tagihan pasien real-time.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card card-custom text-center">
                        <h5 class="mb-3 fw-bold">Status Hari Ini</h5>
                        <hr class="mb-4">
                        <div style="position: relative; height:240px;">
                            <canvas id="pembayaranChart"></canvas>
                        </div>
                        <div class="mt-4 pt-2 text-start">
                            <div class="p-3 rounded-3 bg-light border-start border-danger border-4">
                                <span class="text-muted small fw-bold d-block">Menunggu Pembayaran</span>
                                <h4 class="mb-0 fw-bold text-danger"><?= $count_unpaid ?> <small class="fs-6 text-muted">Pasien</small></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-custom">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-clipboard-list me-2"></i>Rincian Tagihan</h5>
                        </div>
                        
                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                                <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Pasien</th>
                                        <th>Poliklinik</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($tagihan as $t): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= $t['full_name'] ?></div>
                                            <small class="text-muted">ID: #<?= $t['appointment_id'] ?></small>
                                        </td>
                                        <td><span class="text-secondary"><?= $t['poli'] ?></span></td>
                                        <td><span class="badge-unpaid text-uppercase">Pending</span></td>
                                        <td>
                                            <form action="<?= base_url('kasir/bayar/' . $t['appointment_id']); ?>" method="POST" class="d-flex gap-2 justify-content-center">
                                                <select name="metode" class="form-select form-select-sm shadow-sm" required style="width: 120px;">
                                                    <option value="cash">💵 Tunai</option>
                                                    <option value="card">💳 Kartu</option>
                                                    <option value="insurance">🛡️ Asuransi</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                                                    Proses
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($tagihan)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fas fa-check-double fa-2x mb-3 d-block opacity-25"></i>
                                                Belum ada data tagihan baru.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    const ctx = document.getElementById('pembayaranChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Unpaid', 'Paid'],
            datasets: [{
                data: [<?= $count_unpaid ?>, <?= $count_paid ?>],
                backgroundColor: ['#ff4757', '#2ed573'],
                borderWidth: 5,
                borderColor: '#ffffff',
                hoverOffset: 10
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12, weight: 'bold' }
                    }
                } 
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>