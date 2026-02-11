<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Pasien | RS Rawat Jalan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-900 text-gray-100 font-sans">

    <div class="min-h-screen flex flex-col">
        <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-stethoscope text-blue-500 text-2xl"></i>
                <h1 class="text-xl font-bold tracking-tight">Form <span class="text-blue-500">Pemeriksaan</span></h1>
            </div>
            <a href="<?= base_url('dokter/dashboard') ?>" class="text-gray-400 hover:text-white transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </nav>

        <main class="flex-grow p-6 lg:p-10">
            <div class="max-w-4xl mx-auto">

                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 mb-8 shadow-lg">
                    <h3 class="text-blue-400 font-bold mb-4 uppercase text-xs tracking-widest">Data Pasien</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-500 text-sm">Nama Lengkap</p>
                            <p class="text-lg font-semibold"><?= $patient['full_name'] ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">NIK</p>
                            <p class="text-lg font-semibold"><?= $patient['nik'] ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Jenis Kelamin</p>
                            <p class="text-lg font-semibold"><?= $patient['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Tanggal Lahir</p>
                            <p class="text-lg font-semibold"><?= date('d M Y', strtotime($patient['birth_date'])) ?></p>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('dokter/submitExamination') ?>" method="POST" class="space-y-6">
                    <?= csrf_field() ?>
                    <input type="hidden" name="appointment_id" value="<?= $patient['appointment_id'] ?>">

                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-lg">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Keluhan Pasien</label>
                                <textarea name="complaint" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-white" placeholder="Input keluhan utama pasien..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Diagnosa (ICD-10 / Deskripsi)</label>
                                <input type="text" name="diagnosis" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-white" placeholder="Contoh: Common Cold / J00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Catatan Tambahan</label>
                                <textarea name="notes" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-white" placeholder="Saran istirahat, diet, dll..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-lg p-6">
                        <h3 class="text-sm font-bold text-gray-400 mb-4 uppercase tracking-widest">Resep Obat</h3>

                        <div id="medicine-container" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end border-b border-gray-700 pb-4">
                                <div class="md:col-span-2">
                                    <label class="text-xs text-gray-500">Nama Obat</label>
                                    <select name="medicines[0][id]" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                                        <option value="">-- Pilih Obat --</option>
                                        <?php foreach ($medicines as $med): ?>
                                            <option value="<?= $med['medicine_id'] ?>"><?= $med['name'] ?> (Stok: <?= $med['stock'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Jumlah (Qty)</label>
                                    <input type="number" name="medicines[0][qty]" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">Aturan Pakai</label>
                                    <input type="text" name="medicines[0][instructions]" placeholder="3x1 hari" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                                    <input type="hidden" name="medicines[0][dosage]" value="-">
                                </div>
                            </div>
                        </div>

                        <button type="button" class="mt-4 text-sm text-blue-400 hover:text-blue-300">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Obat Lain
                        </button>
                    </div>

                    <div class="flex justify-end gap-4">
                        <button type="reset" class="px-6 py-2 rounded-lg border border-gray-700 hover:bg-gray-800 transition">Reset</button>
                        <button type="submit" class="px-10 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-900/30 transition">
                            SIMPAN PEMERIKSAAN
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <script>
        let medicineIndex = 1;
        document.querySelector('button[type="button"]').addEventListener('click', function() {
            const container = document.getElementById('medicine-container');
            const firstRow = container.querySelector('.grid');
            const newRow = firstRow.cloneNode(true);

            // Update index name input
            newRow.querySelectorAll('select, input').forEach(input => {
                input.name = input.name.replace('[0]', '[' + medicineIndex + ']');
                input.value = '';
            });

            container.appendChild(newRow);
            medicineIndex++;
        });
    </script>
</body>

</html>