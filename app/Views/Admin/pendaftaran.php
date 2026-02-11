<?= $this->extend('Admin/Layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Data Pendaftaran Pasien</h3>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPasien">
        <i class="bi bi-person-plus-fill me-2"></i> Daftar Pasien Baru
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Pasien</th>
                        <th>Poliklinik</th>
                        <th>Dokter</th>
                        <th>Tanggal Periksa</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pendaftaran)): ?>
                        <?php foreach ($pendaftaran as $key => $p): ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td class="fw-bold"><?= $p['full_name'] ?></td>
                                <td><span class="badge bg-info bg-opacity-10 text-info px-3"><?= $p['poli'] ?></span></td>
                                <td><?= $p['nama_dokter'] ?></td>
                                <td><?= date('d/m/Y', strtotime($p['schedule_date'])) ?></td>
                                <td>
                                    <?php if ($p['status'] == 'scheduled'): ?>
                                        <span class="badge bg-warning text-dark">Terjadwal</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url('admin/pendaftaran/edit/' . $p['appointment_id']) ?>"
                                            class="btn btn-sm btn-light text-primary"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>

                                        <a href="<?= base_url('admin/pendaftaran/delete/' . $p['appointment_id']) ?>"
                                            class="btn btn-sm btn-light text-danger"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data pendaftaran pasien.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahPasien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Form Pendaftaran Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/pendaftaran/simpan') ?>" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Pasien</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">-- Cari Pasien --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Poliklinik</label>
                        <select name="department_id" class="form-select" required>
                            <option value="">-- Pilih Poli --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Daftarkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>