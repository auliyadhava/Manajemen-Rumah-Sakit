<?= $this->extend('admin/layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus"></i> Tambah User Baru
                </h5>
            </div>

            <div class="card-body">

                <form action="/admin/users/store" method="post">
                    <?= csrf_field() ?>

                    <!-- DATA USER -->
                    <h6 class="text-muted mb-3">Informasi Akun</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select name="role_id" id="role_id" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['role_id'] ?>">
                                        <?= ucfirst($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- DATA DOKTER -->
                    <div id="dokter-form" style="display:none;">
                        <hr>
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-heart-pulse"></i> Data Dokter
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poliklinik</label>
                                <select name="department_id" id="department_id" class="form-select">
                                    <option value="">-- Pilih Poli --</option>
                                    <?php foreach ($departments as $d): ?>
                                        <option value="<?= $d['department_id'] ?>"
                                            data-description="<?= esc($d['description']) ?>">
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
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION -->
                    <div class="d-flex justify-content-end mt-4">
                        <a href="/admin/users" class="btn btn-outline-secondary me-2">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan User
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<!-- SCRIPT ROLE -->
<script>
    document.getElementById('role_id').addEventListener('change', function() {
        const dokterForm = document.getElementById('dokter-form');
        const selectedText = this.options[this.selectedIndex].text.toLowerCase();
        dokterForm.style.display = selectedText === 'dokter' ? 'block' : 'none';
    });
</script>

<!-- SCRIPT AUTO SPESIALISASI -->
<script>
    document.getElementById('department_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const desc = selected.getAttribute('data-description');
        document.getElementById('specialization').value = desc ?? '';
    });
</script>

<?= $this->endSection() ?>