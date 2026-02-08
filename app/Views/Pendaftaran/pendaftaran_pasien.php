<?= $this->extend('Pendaftaran/layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- JUDUL -->
    <div class="mb-4">
        <h3 class="fw-bold">Pendaftaran Pasien</h3>
        <p class="text-muted">Daftar pasien yang melakukan pendaftaran online</p>
    </div>

    <!-- CARD -->
    <div class="card shadow-sm">
        <div class="card-body">

            <!-- SEARCH -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Cari pasien...">
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Pasien</th>
                            <th>Poli</th>
                            <th>Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($dataPasien)): ?>
                            <?php foreach ($dataPasien as $row): ?>
                                <tr>
                                    <td><?= esc($row['full_name']) ?></td>
                                    <td><?= esc($row['department_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $row['status'] === 'waiting' ? 'warning text-dark' : 'success' ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] === 'waiting'): ?>
                                            <a href="<?= base_url('pendaftaran/konfirmasi/'.$row['appointment_id']) ?>"
                                               class="btn btn-success btn-sm"
                                               onclick="return confirm('Konfirmasi pasien ini?')">
                                                <i class="bi bi-check-circle"></i> Konfirmasi
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                Sudah dikonfirmasi
                                            </button>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada data pendaftaran
                                </td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<!-- MODAL KARCIS ANTRIAN -->
<?php if (session()->getFlashdata('queue_ticket')): 
    $ticket = session()->getFlashdata('queue_ticket');
?>

<div class="modal fade" id="ticketModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
            <i class="bi bi-ticket-perforated"></i> Kartu Antrian
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        <h1 class="display-3 fw-bold"><?= $ticket['queue_number'] ?></h1>
        <p class="fw-semibold mb-1"><?= esc($ticket['full_name']) ?></p>
        <p class="mb-1">Poli: <strong><?= esc($ticket['department']) ?></strong></p>
        <p class="text-muted"><?= date('d M Y', strtotime($ticket['schedule_date'])) ?></p>

        <hr>
        <small class="text-muted">Silakan menunggu panggilan</small>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak
        </button>
      </div>

    </div>
  </div>
</div>

<script>
    const ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));
    ticketModal.show();
</script>

<?php endif ?>

<?= $this->endSection() ?>
