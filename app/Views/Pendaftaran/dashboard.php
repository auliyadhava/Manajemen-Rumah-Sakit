<?= $this->extend('Pendaftaran/layout/main') ?>
<?= $this->section('content') ?>

<h3 class="mb-4">Dashboard Pendaftaran</h3>

<!-- SUMMARY CARD -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h6>Total Pendaftaran Hari Ini</h6>
                <h3><?= $totalConfirmed ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6>Menunggu Verifikasi</h6>
                <h3><?= $totalWaiting ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6>Total Antrian Hari Ini</h6>
                <h3><?= $totalQueue ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- PENDAFTARAN ONLINE TERBARU -->
<div class="card mb-4">
    <div class="card-header">
        Pendaftaran Online Terbaru
    </div>
    <div class="card-body">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>Poli</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pendaftaranTerbaru)): ?>
                    <?php foreach ($pendaftaranTerbaru as $item): ?>
                        <tr>
                            <td><?= esc($item['full_name']) ?></td>
                            <td><?= esc($item['department_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= $item['status'] === 'waiting' ? 'warning' : 'success' ?>">
                                    <?= ucfirst($item['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            Belum ada pendaftaran
                        </td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ANTRIAN TERBARU -->
<div class="card">
    <div class="card-header">
        Antrian Terbaru
    </div>
    <div class="card-body">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>No Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Poli</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($antrianTerbaru)): ?>
                    <?php foreach ($antrianTerbaru as $queue): ?>
                        <tr>
                            <td><?= $queue['queue_number'] ?></td>
                            <td><?= esc($queue['full_name']) ?></td>
                            <td><?= esc($queue['department_name']) ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?= ucfirst($queue['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Belum ada antrian
                        </td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
