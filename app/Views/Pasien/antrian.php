<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Antrian - RS Sejahtera</title>
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
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .icon-clock {
            margin-right: 10px;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th {
            background-color: #34495e;
            color: white;
            text-align: left;
            padding: 15px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .queue-number {
            font-size: 24px;
            font-weight: bold;
            color: #27ae60;
            background: #e8f5e9;
            padding: 5px 15px;
            border-radius: 8px;
            display: inline-block;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-waiting { background: #fff3cd; color: #856404; }
        .status-calling { background: #d1ecf1; color: #0c5460; animation: pulse 1.5s infinite; }
        .status-done { background: #d4edda; color: #155724; }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .btn-action {
            display: inline-block;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
        }

        .btn-back {
            text-decoration: none;
            color: #7f8c8d;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }

        .btn-back:hover { color: #34495e; }
    </style>
</head>
<body>

<div class="container">
    <h2><span class="icon-clock">⏱️</span> Status Antrian Anda</h2>

    <table>
        <thead>
            <tr>
                <th>No Antrian</th>
                <th>Poliklinik</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Info / Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($queues)): ?>
                <?php foreach ($queues as $q): ?>
                <tr>
                    <td><span class="queue-number"><?= $q->queue_number ?></span></td>
                    <td><strong><?= $q->department ?></strong></td>
                    <td><?= date('d/m/Y', strtotime($q->schedule_date)) ?></td>
                    <td>
                        <?php 
                            $class = 'status-waiting';
                            if($q->status == 'calling') $class = 'status-calling';
                            if($q->status == 'done') $class = 'status-done';
                        ?>
                        <span class="status-badge <?= $class ?>">
                            <?= $q->status ?>
                        </span>
                    </td>
                    <td>
                        <?php if($q->status == 'done'): ?>
                            <a href="<?= base_url('pasien/detail_pemeriksaan/'.$q->appointment_id) ?>" class="btn-action">
                                Lihat Hasil
                            </a>
                        <?php else: ?>
                            <span style="color: #95a5a6; font-style: italic;">Silahkan Menunggu...</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #95a5a6;">
                        Tidak ada antrian aktif saat ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="<?= base_url('pasien') ?>" class="btn-back">← Kembali ke Dashboard</a>
</div>

</body>
</html>