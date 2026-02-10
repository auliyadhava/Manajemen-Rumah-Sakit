<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Pasien Baru</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #34495e; }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box; /* Agar padding tidak merusak lebar */
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover { background-color: #2980b9; }
        .alert {
            padding: 10px; background: #ffdede; color: #c0392b; 
            border-radius: 5px; margin-bottom: 15px; text-align: center;
        }
        .login-link { text-align: center; margin-top: 15px; font-size: 14px; }
        .login-link a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>📝 Daftar Pasien Baru</h2>

    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert">
            <?= implode('<br>', session()->getFlashdata('errors')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('pasien/register/process') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="full_name" required placeholder="Contoh: Dhava Auliya">
        </div>
        
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Untuk login nanti">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <hr style="border: 0; border-top: 1px dashed #ccc; margin: 20px 0;">

        <div class="form-group">
            <label>NIK (Nomor Induk Kependudukan)</label>
            <input type="number" name="nik" required>
        </div>

        <div class="form-group">
            <label>Jenis Kelamin</label>
            <select name="gender">
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>

        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="birth_date" required>
        </div>

        <div class="form-group">
            <label>No. HP / WhatsApp</label>
            <input type="text" name="phone" required placeholder="08xxxxx">
        </div>

        <div class="form-group">
            <label>Alamat Lengkap</label>
            <textarea name="address" rows="3" required></textarea>
        </div>

        <button type="submit">Daftar Sekarang</button>
    </form>

    <div class="login-link">
        Sudah punya akun? <a href="<?= base_url('/') ?>">Login disini</a>
    </div>
</div>

</body>
</html>