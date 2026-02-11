<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter | Manajemen RS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-900 text-gray-100 font-sans">

    <div class="min-h-screen flex flex-col">

        <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <i class="fas fa-hospital-user text-blue-500 text-2xl"></i>
                <h1 class="text-xl font-bold tracking-tight">RS <span class="text-blue-500">Rawat Jalan</span></h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold"><?= session()->get('full_name') ?></p>
                    <p class="text-xs text-gray-400">Spesialis Dokter</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center border-2 border-gray-600">
                    <i class="fas fa-user-md text-white"></i>
                </div>
            </div>
        </nav>

        <main class="flex-grow p-6 lg:p-10">
            <div class="max-w-6xl mx-auto">

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="mb-6 p-4 bg-green-900/30 border border-green-500 text-green-400 rounded-lg flex items-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-white">Antrean Pasien</h2>
                        <p class="text-gray-400">Selamat bertugas, hari ini adalah <?= date('d M Y') ?></p>
                    </div>
                    <div class="flex gap-3">
                        <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 text-center min-w-[120px]">
                            <p class="text-xs text-gray-400 uppercase">Total Antrean</p>
                            <p class="text-2xl font-bold text-blue-500"><?= count($patients) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-800/50">
                        <h3 class="font-semibold text-lg italic text-blue-400"><i class="fas fa-list-ol mr-2"></i>Daftar Tunggu Poliklinik</h3>
                        <button class="text-gray-400 hover:text-white transition">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-400 text-sm uppercase tracking-wider border-b border-gray-700">
                                    <th class="px-6 py-4">No. Urut</th>
                                    <th class="px-6 py-4">Informasi Pasien</th>
                                    <th class="px-6 py-4">Jadwal</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <?php if (!empty($patients)) : ?>
                                    <?php foreach ($patients as $p) : ?>
                                        <tr class="hover:bg-gray-750/50 transition-colors group">
                                            <td class="px-6 py-5">
                                                <span class="bg-blue-900/50 text-blue-400 px-4 py-2 rounded-lg font-mono font-bold text-lg border border-blue-800 group-hover:bg-blue-600 group-hover:text-white transition">
                                                    <?= str_pad($p['queue_number'], 3, '0', STR_PAD_LEFT); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-5">
                                                <p class="text-white font-semibold"><?= $p['patient_name']; ?></p>
                                                <p class="text-xs text-gray-500">ID Janji: #<?= $p['appointment_id']; ?></p>
                                            </td>
                                            <td class="px-6 py-5">
                                                <p class="text-sm text-gray-300"><?= date('H:i', strtotime($p['created_at'])); ?> WIB</p>
                                            </td>
                                            <td class="px-6 py-5">
                                                <span class="flex items-center gap-2 text-yellow-500 text-sm">
                                                    <span class="h-2 w-2 rounded-full bg-yellow-500 animate-pulse"></span>
                                                    Menunggu
                                                </span>
                                            </td>
                                            <td class="px-6 py-5 text-right">
                                                <a href="<?= base_url('dokter/examine/' . $p['appointment_id']) ?>"
                                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg font-medium transition shadow-lg shadow-blue-900/20">
                                                    <i class="fas fa-stethoscope text-sm"></i>
                                                    Panggil Pasien
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-user-clock text-gray-600 text-5xl mb-4"></i>
                                                <p class="text-gray-500 text-lg">Tidak ada antrean pasien untuk saat ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-800/40 p-4 rounded-lg border border-gray-700 flex items-center gap-3">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span class="text-gray-400 italic">Pastikan data rekam medis diinput dengan teliti.</span>
                    </div>
                </div>

            </div>
        </main>

        <footer class="bg-gray-800 border-t border-gray-700 py-4 px-6 text-center text-xs text-gray-500">
            &copy; 2026 RS Rawat Jalan - Dhava Project | CI4 Management System
        </footer>
    </div>

</body>

</html>