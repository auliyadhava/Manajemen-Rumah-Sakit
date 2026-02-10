<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Edit User
                </h5>
            </div>

            <div class="card-body">
                <form action="/admin/users/update/<?= $user['user_id'] ?>" method="post">
                    <?= csrf_field() ?>

                    <h6 class="text-muted mb-3">Informasi Akun</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="full_name"
                                value="<?= esc($user['full_name']) ?>"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username"
                                value="<?= esc($user['username']) ?>"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password (opsional)</label>
                            <input type="password" name="password"
                                class="form-control"
                                placeholder="Kosongkan jika tidak diubah">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select name="role_id" id="role_id" class="form-select" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['role_id'] ?>"
                                        <?= $r['role_id'] == $user['role_id'] ? 'selected' : '' ?>>
                                        <?= ucfirst($r['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- DATA DOKTER -->
                    <div id="dokter-form" style="<?= $roleName === 'dokter' ? '' : 'display:none;' ?>">
                        <hr>
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-heart-pulse"></i> Data Dokter
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poliklinik</label>
                                <select name="department_id" id="department_id" class="form-select">
                                    <?php foreach ($departments as $d): ?>
                                        <option value="<?= $d['department_id'] ?>"
                                            data-description="<?= esc($d['description']) ?>"
                                            <?= isset($doctor) && $doctor['department_id'] == $d['department_id'] ? 'selected' : '' ?>>
                                            <?= esc($d['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Spesialisasi</label>
                                <input type="text"
                                    name="specialization"
                                    id="specialization"
                                    class="form-control bg-light"
                                    value="<?= esc($doctor['specialization'] ?? '') ?>"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="/admin/users" class="btn btn-outline-secondary me-2">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('role_id').addEventListener('change', function() {
        const dokterForm = document.getElementById('dokter-form');
        dokterForm.style.display =
            this.options[this.selectedIndex].text.toLowerCase() === 'dokter' ?
            'block' : 'none';
    });

    document.getElementById('department_id')?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        document.getElementById('specialization').value =
            selected.getAttribute('data-description') ?? '';
    });
</script>

<?= $this->endSection() ?>