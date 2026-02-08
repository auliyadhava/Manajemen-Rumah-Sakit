<?= $this->extend('Pendaftaran/layout/main') ?>
<?= $this->section('content') ?>

<h3 class="mb-4">Monitoring Antrian Pasien</h3>

<div class="card shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No Antrian</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dataAntrian)): ?>
                        <?php foreach ($dataAntrian as $row): ?>
                            <tr>
                                <td class="fw-bold"><?= $row['queue_number'] ?></td>
                                <td><?= esc($row['full_name']) ?></td>
                                <td><?= esc($row['department_name']) ?></td>
                                <td>
                                    <span class="badge bg-<?=
                                        $row['status'] === 'waiting' ? 'warning text-dark' :
                                        ($row['status'] === 'called' ? 'info' : 'success')
                                    ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada antrian hari ini
                            </td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
