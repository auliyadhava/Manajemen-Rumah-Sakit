<div style="padding: 20px; font-family: Arial, sans-serif;">
    <h2>Detail Hasil Pemeriksaan</h2>
    <a href="<?= base_url('pasien/riwayat') ?>">← Kembali ke Riwayat</a>
    <hr>

    <section style="background: #eefbff; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h3>1. Hasil Diagnosa</h3>
        <?php if ($pemeriksaan): ?>
            <p><strong>Diagnosis:</strong> <?= $pemeriksaan->diagnosis; ?></p>
            <p><strong>Catatan Dokter:</strong> <?= $pemeriksaan->notes; ?></p>
        <?php else: ?>
            <p style="color: red;"><i>Hasil pemeriksaan belum diinput oleh Dokter Dhava.</i></p>
        <?php endif; ?>
    </section>

    <section style="background: #f4fff4; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h3>2. Resep & Obat</h3>
        <?php if ($resep): ?>
            <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
                <tr style="background: #ddd;">
                    <th>Nama Obat</th>
                    <th>Dosis</th>
                    <th>Instruksi</th>
                </tr>
                <?php foreach ($resep as $r): ?>
                <tr>
                    <td><?= $r->name; ?></td>
                    <td><?= $r->dosage; ?></td>
                    <td><?= $r->instructions; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Tidak ada resep obat.</p>
        <?php endif; ?>
    </section>

    <section style="background: #fff4f4; padding: 15px; border-radius: 8px;">
        <h3>3. Status Pembayaran</h3>
        <?php if ($pembayaran): ?>
            <p><strong>Total Tagihan:</strong> Rp <?= number_with_delimiters($pembayaran->amount); ?></p>
            <p><strong>Status:</strong> 
                <span style="padding: 5px; background: orange; color: white;">
                    <?= strtoupper($pembayaran->status); ?>
                </span>
            </p>
        <?php else: ?>
            <p>Tagihan belum tersedia.</p>
        <?php endif; ?>
    </section>
</div>