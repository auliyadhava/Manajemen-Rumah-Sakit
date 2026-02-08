<!DOCTYPE html>
<html>

<head>
    <title>RS Rawat Jalan - <?= $title ?? 'Administrator' ?></title>
</head>

<body>
    <div class="sidebar">
        <a href="/dashboard">Dashboard</a>
        <?php if (session()->get('role') == 'admin'): ?>
            <a href="/admin/users">Manajemen User</a>
        <?php endif; ?>
    </div>
    <div class="content">
        <?= $this->renderSection('content') ?>
    </div>
</body>

</html>