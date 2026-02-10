<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Pasien' ?></title>
</head>
<body>

<header>
    <h3>Portal Pasien</h3>
    <p>Halo, <?= session()->get('full_name') ?></p>
</header>

<nav>
    <a href="/pasien">Dashboard</a>
    <a href="/pasien/appointment">Daftar Poli</a>
    <a href="/pasien/profile">Profil</a>
    <a href="/logout">Logout</a>
</nav>

<main>
    <?= $this->renderSection('content') ?>
</main>

</body>
</html>
