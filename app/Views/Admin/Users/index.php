<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Manajemen User</h3>
        <a href="/admin/users/create" class="btn btn-primary">+ Tambah User</a>
    </div>
    <div class="card-body">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($users)): ?>
                    <?php foreach($users as $key => $user): ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $user['full_name'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><span class="badge bg-info"><?= $user['role_name'] ?></span></td>
                        <td><?= $user['status'] ?></td>
                        <td>
                            <a href="/admin/users/edit/<?= $user['user_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="/admin/users/delete/<?= $user['user_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data user.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>