<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir | RS Kelompok</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom {
            background: white; border-radius: 15px; border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fbff; transform: scale(1.005); transition: 0.2s;
        }
        .header-title { font-weight: 700; color: #2c3e50; }
        .badge-unpaid { background-color: #ff4757; color: white; padding: 6px 12px; border-radius: 6px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row mb-4 animate__animated animate__fadeInDown">
        <div class="col-12">
            <h2 class="header-title">Kasir Dashboard</h2>
            <p class="text-muted">Kelola tagihan pasien dan pembayaran farmasi.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4 animate__animated animate__fadeInLeft">
            <div class="card card-custom p-4 text-center">
                <h5>Status Tagihan</h5>
                <hr>
                <canvas id="pembayaranChart" style="max-height: 250px;"></canvas>
                <div class="mt-3">
                    <small class="text-muted">Total Pending: <strong><?= $count_unpaid ?></strong></small>
                </div>
            </div>
        </div>

        <div class="col-lg-8 animate__animated animate__fadeInRight">
            <div class="card card-custom p-4">
                <h5 class="mb-4">Daftar Tagihan Pasien</h5>
                
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <table class="table table-hover mt-2">
                    <thead class="table-light">
                        <tr>
                            <th>Pasien</th>
                            <th>Poli</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tagihan as $t): ?>
                        <tr>
                            <td class="align-middle"><strong><?= $t['full_name'] ?></strong></td>
                            <td class="align-middle"><?= $t['poli'] ?></td>
                            <td class="align-middle">
                                <span class="badge-unpaid">Belum Bayar</span>
                            </td>
                            <td>
                                <form action="/kasir/bayar/<?= $t['appointment_id'] ?>" method="POST" class="d-flex gap-2">
                                    <select name="metode" class="form-select form-select-sm" required style="width: 120px;">
                                        <option value="cash">Tunai</option>
                                        <option value="card">Kartu</option>
                                        <option value="insurance">Asuransi</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Bayar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($tagihan)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Semua tagihan sudah lunas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
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
                hoverOffset: 15
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } },
            animation: { animateScale: true }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>