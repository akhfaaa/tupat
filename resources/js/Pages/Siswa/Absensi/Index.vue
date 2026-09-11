<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    riwayat: Array,
    statistik: Object,
});

// Helper untuk persentase kehadiran
const persentaseHadir = computed(() => {
    if (props.statistik.total === 0) return 0;
    return Math.round((props.statistik.hadir / props.statistik.total) * 100);
});
</script>

<template>

    <Head title="Kehadiran Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rekapitulasi Kehadiran</h2>
        </template>

        <div class="py-12 font-sans">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <!-- Statistik Absensi -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <!-- Persentase Utama -->
                    <div
                        class="col-span-2 md:col-span-1 bg-indigo-600 rounded-2xl p-6 text-white shadow-sm flex flex-col justify-center items-center text-center">
                        <p class="text-indigo-100 text-sm font-medium tracking-wide uppercase">Persentase</p>
                        <p class="text-4xl font-bold tracking-tighter mt-1">{{ persentaseHadir }}%</p>
                    </div>

                    <!-- Kartu Detail -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-gray-100 flex flex-col items-center">
                        <span class="w-3 h-3 rounded-full bg-green-500 mb-2"></span>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Hadir</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ statistik.hadir }}</p>
                    </div>

                    <div
                        class="bg-white rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-gray-100 flex flex-col items-center">
                        <span class="w-3 h-3 rounded-full bg-yellow-400 mb-2"></span>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Sakit</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ statistik.sakit }}</p>
                    </div>

                    <div
                        class="bg-white rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-gray-100 flex flex-col items-center">
                        <span class="w-3 h-3 rounded-full bg-blue-500 mb-2"></span>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Izin</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ statistik.izin }}</p>
                    </div>

                    <div
                        class="bg-white rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.04)] border border-gray-100 flex flex-col items-center">
                        <span class="w-3 h-3 rounded-full bg-red-500 mb-2"></span>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Alpa</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ statistik.alpa }}</p>
                    </div>
                </div>

                <!-- Tabel Riwayat -->
                <div
                    class="bg-white overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.04)] sm:rounded-[2rem] border border-gray-100">
                    <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Riwayat Presensi Harian</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-400 uppercase bg-gray-50/50">
                                <tr>
                                    <th class="px-8 py-4 font-semibold">Tanggal</th>
                                    <th class="px-8 py-4 font-semibold">Mata Pelajaran</th>
                                    <th class="px-8 py-4 font-semibold">Guru Pengajar</th>
                                    <th class="px-8 py-4 font-semibold text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="riwayat.length === 0">
                                    <td colspan="4" class="px-8 py-8 text-center text-gray-400 italic">
                                        Belum ada riwayat presensi yang tercatat.
                                    </td>
                                </tr>
                                <tr v-for="item in riwayat" :key="item.id" class="hover:bg-gray-50/50 transition">
                                    <td class="px-8 py-4 whitespace-nowrap text-gray-900 font-medium">
                                        {{ new Date(item.jurnal?.tanggal).toLocaleDateString('id-ID', {
                                            weekday: 'long',
                                            day:
                                        'numeric', month: 'long', year: 'numeric' }) }}
                                    </td>
                                    <td class="px-8 py-4">
                                        {{ item.jurnal?.mata_pelajaran?.nama_mapel }}
                                        <span class="block text-xs text-gray-400 mt-0.5">Jam ke: {{ item.jurnal?.jam_ke
                                            }}</span>
                                    </td>
                                    <td class="px-8 py-4">{{ item.jurnal?.guru?.nama_lengkap || '-' }}</td>
                                    <td class="px-8 py-4 text-center">
                                        <span v-if="item.status === 'Hadir'"
                                            class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Hadir</span>
                                        <span v-else-if="item.status === 'Sakit'"
                                            class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">Sakit</span>
                                        <span v-else-if="item.status === 'Izin'"
                                            class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">Izin</span>
                                        <span v-else
                                            class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">Alpa</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>