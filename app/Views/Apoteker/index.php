<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Apoteker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        }

        .header {
            background: #2c7be5;
            color: white;
            padding: 15px;
        }

        .container {
            padding: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background: #2c7be5;
            color: white;
            padding: 10px;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .btn {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 5px;
            color: white;
            font-size: 13px;
        }

        .btn-ambil {
            background: #28a745;
        }

        .alert-success {
            background: #d4edda;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .alert-error {
            background: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Dashboard Apoteker</h2>
    </div>

    <div class="container">

        <div class="card">

            <h3>Resep Siap Diambil</h3>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-error">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID Resep</th>
                        <th>ID Pasien</th>
                        <th>Status Ambil</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($resep)): ?>
                        <?php foreach ($resep as $r): ?>
                            <tr>
                                <td><strong>#<?= $r['prescription_id'] ?></strong></td>

                                <td><?= $r['patient_name'] ?></td>

                                <td>
                                    <span style="color: #28a745; font-weight: bold;">
                                        <i class="fas fa-check-circle"></i> Lunas (Siap Ambil)
                                    </span>
                                </td>

                                <td>
                                    <?php if (empty($r['pickup_id'])): ?>
                                        <form action="<?= base_url('apoteker/pickup/' . $r['prescription_id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-ambil">Berikan Obat</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Selesai Diserahkan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" align="center" style="padding: 40px; color: #666;">
                                <img src="https://cdn-icons-png.flaticon.com/512/102/102661.png" width="50" style="opacity: 0.3;"><br><br>
                                Belum ada resep yang lunas dari Kasir.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>

        </div>

    </div>

</body>

</html>