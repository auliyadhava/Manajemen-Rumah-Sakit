<?= $this->extend('Admin/Layout/main') ?> <?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold"><i class="bi bi-grid-1x2-fill me-2 text-primary"></i>Dashboard Utama</h3>
    <div class="text-muted">
        <i class="bi bi-calendar3 me-1"></i> <?= date('l, d F Y') ?>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 border-start border-primary border-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-people-fill text-primary fs-3"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Pasien Hari Ini</small>
                    <h3 class="mb-0 fw-bold"><?= $stats['pasien'] ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 border-start border-success border-4">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-stethoscope text-success fs-3"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Pemeriksaan</small>
                    <h3 class="mb-0 fw-bold"><?= $stats['pemeriksaan'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 border-start border-warning border-4">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-cash-stack text-warning fs-3"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Pembayaran Lunas</small>
                    <h3 class="mb-0 fw-bold"><?= $stats['pembayaran'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 border-start border-info border-4">
            <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-capsule text-info fs-3"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Obat Diserahkan</small>
                    <h3 class="mb-0 fw-bold"><?= $stats['obat'] ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0">TREN KUNJUNGAN PASIEN</h6>
                <span class="badge bg-light text-primary">7 Hari Terakhir</span>
            </div>
            <canvas id="chartKunjungan" height="150"></canvas>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-4">AKTIVITAS TERBARU</h6>
            <div class="list-group list-group-flush">
                <?php if(!empty($riwayat)): ?>
                    <?php foreach($riwayat as $r): ?>
                    <div class="list-group-item px-0 border-0 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold small"><?= $r['pasien'] ?></h6>
                                <p class="text-muted mb-0" style="font-size: 11px;">
                                    <i class="bi bi-tag-fill me-1"></i> <?= $r['poli'] ?> - <?= $r['diagnosis'] ?>
                                </p>
                            </div>
                            <span class="badge bg-blue-100 text-blue-800" style="font-size: 10px;">
                                <?= date('H:i', strtotime($r['exam_date'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="text-muted small">Belum ada aktivitas hari ini.</p>
                    </div>
                <?php endif; ?>
            </div>
            <a href="#" class="btn btn-light btn-sm w-100 mt-auto">Lihat Semua Riwayat</a>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('chartKunjungan').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Jumlah Pasien',
                data: [15, 25, 20, 35, 30, 45, 12],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.05)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
<?= $this->endSection() ?>