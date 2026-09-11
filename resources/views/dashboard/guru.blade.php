<x-app-layout>
    <div class="min-h-screen bg-[#f5f5f7] pt-12 pb-24 font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">

            <!-- Hero Section -->
            <div class="text-center px-4">
                <h2 class="text-4xl md:text-6xl font-bold tracking-tighter text-gray-900 mb-4">
                    Selamat datang, {{ Auth::user()->name }}.
                </h2>
                <p class="text-xl md:text-2xl text-gray-500 font-medium tracking-tight">
                    Ruang kerja akademik Anda. Cepat dan intuitif.
                </p>
            </div>

            <!-- Bento Grid: Menu Cepat Guru -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-4 sm:px-0">

                <a href="{{ route('guru.jurnal.index') }}" class="group bg-white rounded-[3rem] p-12 shadow-[0_4px_24px_rgba(0,0,0,0.02)] transition duration-500 hover:shadow-[0_12px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 flex flex-col justify-between min-h-[280px]">
                    <div>
                        <h3 class="text-gray-400 font-bold tracking-widest text-sm uppercase mb-2">Modul Harian</h3>
                        <h4 class="text-4xl font-bold tracking-tighter text-gray-900 group-hover:text-blue-600 transition">Jurnal Mengajar.</h4>
                        <p class="text-gray-500 mt-4 text-lg tracking-tight">Catat agenda dan absensi harian kelas Anda dengan mulus.</p>
                    </div>
                    <div class="mt-8">
                        <span class="inline-block bg-[#f5f5f7] text-gray-800 font-semibold rounded-full px-6 py-3 group-hover:bg-blue-50 group-hover:text-blue-700 transition">Buka Jurnal</span>
                    </div>
                </a>

                <a href="{{ route('guru.nilai.index') }}" class="group bg-white rounded-[3rem] p-12 shadow-[0_4px_24px_rgba(0,0,0,0.02)] transition duration-500 hover:shadow-[0_12px_40px_rgba(0,0,0,0.06)] hover:-translate-y-1 flex flex-col justify-between min-h-[280px]">
                    <div>
                        <h3 class="text-gray-400 font-bold tracking-widest text-sm uppercase mb-2">Modul Evaluasi</h3>
                        <h4 class="text-4xl font-bold tracking-tighter text-gray-900 group-hover:text-indigo-600 transition">Input Nilai.</h4>
                        <p class="text-gray-500 mt-4 text-lg tracking-tight">Kelola nilai evaluasi belajar siswa dengan antarmuka secepat kilat.</p>
                    </div>
                    <div class="mt-8">
                        <span class="inline-block bg-[#f5f5f7] text-gray-800 font-semibold rounded-full px-6 py-3 group-hover:bg-indigo-50 group-hover:text-indigo-700 transition">Kelola Nilai</span>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>