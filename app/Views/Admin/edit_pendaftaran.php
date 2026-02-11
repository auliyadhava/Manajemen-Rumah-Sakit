<?= $this->extend('Admin/Layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">Edit Data Pendaftaran</h5>
    </div>
    <div class="card-body p-4">
        <form action="<?= base_url('admin/pendaftaran/update/' . $pendaftaran['appointment_id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Pasien</label>
                    <select name="patient_id" class="form-select" required>
                        <?php foreach ($pasien as $ps): ?>
                            <option value="<?= $ps['patient_id'] ?>" <?= ($ps['patient_id'] == $pendaftaran['patient_id']) ? 'selected' : '' ?>>
                                <?= $ps['full_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Poliklinik</label>
                    <select name="department_id" class="form-select" required>
                        <?php foreach ($poli as $pl): ?>
                            <option value="<?= $pl['department_id'] ?>" <?= ($pl['department_id'] == $pendaftaran['department_id']) ? 'selected' : '' ?>>
                                <?= $pl['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Dokter</label>
                    <select name="doctor_id" class="form-select" required>
                        <?php foreach ($dokter as $dk): ?>
                            <option value="<?= $dk['doctor_id'] ?>" <?= ($dk['doctor_id'] == $pendaftaran['doctor_id']) ? 'selected' : '' ?>>
                                <?= $dk['full_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Tanggal Periksa</label>
                    <input type="date" name="schedule_date" class="form-control" value="<?= $pendaftaran['schedule_date'] ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="scheduled" <?= ($pendaftaran['status'] == 'scheduled') ? 'selected' : '' ?>>Terjadwal (Scheduled)</option>
                        <option value="completed" <?= ($pendaftaran['status'] == 'completed') ? 'selected' : '' ?>>Selesai (Completed)</option>
                        <option value="cancelled" <?= ($pendaftaran['status'] == 'cancelled') ? 'selected' : '' ?>>Dibatalkan (Cancelled)</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <a href="<?= base_url('admin/pendaftaran') ?>" class="btn btn-light px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>