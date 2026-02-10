<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Online - RS Sejahtera</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            color: #2c3e50;
            margin-top: 0;
            text-align: center;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
        }

        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }

        .alert-error {
            background: #fdecea;
            color: #c0392b;
        }

        .alert-success {
            background: #eafaf1;
            color: #27ae60;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
        }

        input[type="date"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #dcdfe6;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            background-color: #27ae60;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #219150;
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #7f8c8d;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>🏥 Booking Online</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('pasien/booking/store') ?>">
        <?= csrf_field() ?>

        <div class="form-group">
            <label>Tanggal Kunjungan</label>
            <input
                type="date"
                name="schedule_date"
                min="<?= date('Y-m-d') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label>Pilih Poliklinik</label>
            <select name="department_id" required>
                <option value="">-- Pilih Poli --</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d->department_id ?>">
                        <?= esc($d->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Daftar Sekarang</button>
        <a href="<?= base_url('pasien/riwayat') ?>" class="btn-cancel">
            Batal & Kembali
        </a>
    </form>
</div>

</body>
</html>
