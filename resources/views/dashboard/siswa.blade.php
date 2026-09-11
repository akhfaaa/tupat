<x-app-layout>
    <div class="min-h-screen bg-[#f5f5f7] pt-12 pb-24 font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">

            <div class="text-center px-4">
                <h2 class="text-4xl md:text-6xl font-bold tracking-tighter text-gray-900 mb-4">
                    Halo, {{ explode(' ', Auth::user()->name)[0] }}.
                </h2>
                <p class="text-xl text-gray-500 font-medium tracking-tight">
                    Pantau progres belajar Anda semester ini.
                </p>
            </div>

            <!-- Bento Grid: Fitur Siswa -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 sm:px-0">

                <!-- Card Rapor (Utama) -->
                <a href="{{ route('siswa.raport.index') }}" class="md:col-span-2 group block bg-black rounded-[3rem] p-12 text-center shadow-2xl transition duration-500 hover:scale-[1.01] flex flex-col justify-center items-center min-h-[320px]">
                    <h4 class="text-4xl md:text-5xl font-bold tracking-tighter text-white mb-4 group-hover:text-gray-200 transition">
                        E-Rapor Anda.
                    </h4>
                    <p class="text-xl text-gray-400 font-medium max-w-lg mx-auto mb-8 tracking-tight">
                        Lihat capaian nilai akhir semester dan unduh laporan akademik secara real-time.
                    </p>
                    <span class="inline-block bg-white text-black font-semibold rounded-full px-8 py-4 hover:bg-gray-200 transition">
                        Lihat Nilai
                    </span>
                </a>

                <!-- Card Absensi & Jurnal -->
                <div class="grid grid-rows-2 gap-6 h-full">
                    <a href="{{ route('siswa.absensi.index') }}" class="bg-white rounded-[2.5rem] p-8 shadow-[0_4px_24px_rgba(0,0,0,0.02)] transition duration-500 hover:shadow-[0_12px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 flex flex-col justify-center items-center text-center">
                        <h4 class="text-2xl font-bold tracking-tighter text-gray-900 mb-2">Kehadiran</h4>
                        <p class="text-gray-500 tracking-tight">Cek riwayat absen harian.</p>
                    </a>

                    <a href="{{ route('siswa.jurnal.index') }}" class="bg-white rounded-[2.5rem] p-8 shadow-[0_4px_24px_rgba(0,0,0,0.02)] transition duration-500 hover:shadow-[0_12px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 flex flex-col justify-center items-center text-center">
                        <h4 class="text-2xl font-bold tracking-tighter text-gray-900 mb-2">Jurnal Kelas</h4>
                        <p class="text-gray-500 tracking-tight">Lihat materi pembelajaran.</p>
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>