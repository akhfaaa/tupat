<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
});

const user = usePage().props.auth.user;
const roles = usePage().props.auth.roles || [];

// Fungsi bantuan untuk mengecek role
const isRole = (roleName) => roles.includes(roleName);
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Kartu Ucapan Selamat Datang -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-600">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-2xl font-bold">Selamat datang kembali, {{ user.name }}! 👋</h3>
                        <p class="mt-1 text-gray-500">
                            Anda login sebagai
                            <span class="font-semibold text-indigo-600 uppercase">{{ roles.join(', ') || 'User'
                                }}</span>.
                            Gunakan menu navigasi di atas untuk mengelola sistem akademik.
                        </p>
                    </div>
                </div>

                <!-- Statistik Khusus Admin / TU -->
                <div v-if="isRole('super-admin') || isRole('tu')"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <!-- Card Total Siswa -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Siswa Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_siswa || 0 }}</p>
                        </div>
                    </div>

                    <!-- Card Total Guru -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Guru</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_guru || 0 }}</p>
                        </div>
                    </div>

                    <!-- Card Total Rombel -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Kelas / Rombel</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_kelas || 0 }}</p>
                        </div>
                    </div>

                    <!-- Card Siswa PKL -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Siswa PKL Aktif</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.siswa_pkl_aktif || 0 }}</p>
                        </div>
                    </div>

                </div>

                <!-- Tampilan Khusus Guru -->
                <div v-if="isRole('guru')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="font-semibold text-lg text-gray-800 mb-4">Akses Cepat Akademik</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a :href="route('penilaian.index')"
                            class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                            <h5 class="font-bold text-indigo-600">Input Nilai Kelas</h5>
                            <p class="text-sm text-gray-500 mt-1">Kelola nilai Tugas, UTS, dan UAS siswa yang Anda ajar.
                            </p>
                        </a>
                        <!-- Ruang untuk menu masa depan (Misal: Jadwal Mengajar) -->
                        <div class="block p-4 border rounded-lg bg-gray-50 opacity-75 cursor-not-allowed">
                            <h5 class="font-bold text-gray-600">Jadwal Mengajar (Segera Hadir)</h5>
                            <p class="text-sm text-gray-500 mt-1">Pantau jadwal mengajar harian Anda di sini.</p>
                        </div>
                    </div>
                </div>

                <!-- Tampilan Khusus Siswa -->
                <div v-if="isRole('siswa')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="font-semibold text-lg text-gray-800 mb-4">Menu Akademik Siswa</h4>
                    <a :href="route('siswa.rapor')"
                        class="block p-4 border border-indigo-200 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition w-full md:w-1/2">
                        <h5 class="font-bold text-indigo-700">Lihat E-Rapor</h5>
                        <p class="text-sm text-indigo-600 mt-1">Cetak dan pantau hasil belajar akhir semester Anda.</p>
                    </a>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>