<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Appointment - RS Sejahtera</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 40px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            max-width: 900px;
            margin: auto;
        }

        h2 {
            color: #2c3e50;
            border-left: 5px solid #3498db;
            padding-left: 15px;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th {
            background-color: #f8f9fa;
            color: #34495e;
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #dee2e6;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #555;
        }

        tr:hover {
            background-color: #fcfcfc;
        }

        /* Styling Badge Status */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .btn-detail {
            display: inline-block;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
            transition: 0.3s;
        }

        .btn-detail:hover {
            background-color: #2980b9;
        }

        .btn-back {
            background-color: #95a5a6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-back:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📋 Riwayat Appointment</h2>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Poliklinik</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($appointments)): ?>
                <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><?= date('d M Y', strtotime($a->schedule_date)) ?></td>
                    <td><strong><?= $a->department ?></strong></td>
                    <td>
                        <?php 
                            $bg = '#eee'; $color = '#666';
                            if($a->status == 'waiting') { $bg = '#fff3cd'; $color = '#856404'; }
                            if($a->status == 'confirmed') { $bg = '#d1ecf1'; $color = '#0c5460'; }
                            if($a->status == 'done') { $bg = '#d4edda'; $color = '#155724'; }
                        ?>
                        <span class="badge" style="background-color: <?= $bg ?>; color: <?= $color ?>;">
                            <?= $a->status ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($a->status == 'done'): ?>
                            <a href="<?= base_url('pasien/detail_pemeriksaan/' . $a->appointment_id) ?>" class="btn-detail">
                                Lihat Hasil & Resep
                            </a>
                        <?php else: ?>
                            <i style="color: #bbb; font-size: 13px;">Proses...</i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px;">Belum ada riwayat booking.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button class="btn-back" onclick="window.location.href='<?= base_url('pasien') ?>'">
        ← Kembali ke Dashboard
    </button>
</div>

</body>
</html>