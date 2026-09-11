<x-app-layout>
    <div class="min-h-screen bg-[#f5f5f7] pt-12 pb-24 font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">

            <div class="text-center px-4">
                <h2 class="text-4xl md:text-6xl font-bold tracking-tighter text-gray-900 mb-4">
                    Bapak/Ibu {{ Auth::user()->name }}.
                </h2>
                <p class="text-xl md:text-2xl text-gray-500 font-medium tracking-tight">
                    Sistem pemantauan kelas perwalian Anda.
                </p>
            </div>

            <div class="px-4 sm:px-0">
                <a href="{{ route('walikelas.raport.index') }}" class="group relative bg-white rounded-[3rem] p-12 overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)] transition duration-500 block">
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
                        <div class="mb-6 md:mb-0">
                            <h3 class="text-gray-400 font-bold tracking-widest text-sm uppercase mb-3">Tugas Wali Kelas</h3>
                            <h4 class="text-4xl md:text-5xl font-bold tracking-tighter text-gray-900 mb-3 group-hover:text-green-600 transition">Kelola Cetak Rapor.</h4>
                            <p class="text-lg text-gray-500 font-medium max-w-xl">Pantau perkembangan nilai siswa perwalian dan cetak dokumen E-Rapor secara kolektif.</p>
                        </div>
                        <div>
                            <span class="inline-block bg-green-50 text-green-700 font-semibold rounded-full px-8 py-4 group-hover:bg-green-100 transition">
                                Buka Modul Rapor
                            </span>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>