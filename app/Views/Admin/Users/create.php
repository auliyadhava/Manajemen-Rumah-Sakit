<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card mt-3">
    <div class="card-header">
        <h3>Tambah User Baru</h3>
    </div>
    <div class="card-body">
        <form action="/admin/users/store" method="post">
            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role_id" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    <?php foreach($roles as $role): ?>
                        <option value="<?= $role['role_id'] ?>"><?= $role['role_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Simpan User</button>
            <a href="/admin/users" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>