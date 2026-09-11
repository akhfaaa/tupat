<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    siswa: Object,
});
</script>

<template>

    <Head title="E-Rapor Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Hasil Belajar (E-Rapor)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Kartu Identitas Siswa -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row justify-between items-start md:items-center border-l-4 border-indigo-500">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ siswa.nama_lengkap }}</h3>
                        <p class="text-sm text-gray-500 mt-1">NISN: {{ siswa.nisn }} | NIS: {{ siswa.nis || '-' }}</p>
                    </div>
                    <div class="mt-4 md:mt-0 text-left md:text-right">
                        <p class="font-semibold text-indigo-600">{{ siswa.rombel?.nama_rombel || 'Belum masuk kelas' }}
                        </p>
                        <p class="text-sm text-gray-600">{{ siswa.jurusan?.nama_jurusan }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Wali Kelas: {{ siswa.rombel?.wali_kelas?.nama_lengkap || '-' }}
                        </p>
                    </div>
                </div>

                <!-- Tabel Nilai -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">
                        Daftar Nilai Akademik
                        <span class="text-sm font-normal text-gray-500 ml-2">
                            ({{ siswa.rombel?.tahun_ajaran?.tahun }} - Semester {{ siswa.rombel?.tahun_ajaran?.semester
                            }})
                        </span>
                    </h4>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Mata Pelajaran</th>
                                    <th class="px-4 py-3 text-center">Tugas</th>
                                    <th class="px-4 py-3 text-center">UTS</th>
                                    <th class="px-4 py-3 text-center">UAS</th>
                                    <th class="px-4 py-3 text-center">Nilai Akhir</th>
                                    <th class="px-4 py-3 text-center">Predikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!siswa.nilais || siswa.nilais.length === 0">
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-400 italic">
                                        Belum ada nilai yang diunggah untuk semester ini.
                                    </td>
                                </tr>
                                <tr v-for="nilai in siswa.nilais" :key="nilai.id" class="border-b">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ nilai.mata_pelajaran?.nama_mapel }}
                                        <span class="block text-xs text-gray-500">Kelompok {{
                                            nilai.mata_pelajaran?.kelompok
                                            }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ nilai.nilai_tugas }}</td>
                                    <td class="px-4 py-3 text-center">{{ nilai.nilai_uts }}</td>
                                    <td class="px-4 py-3 text-center">{{ nilai.nilai_uas }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-indigo-600">{{ nilai.nilai_akhir }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span v-if="nilai.nilai_akhir >= 90"
                                            class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">A
                                            (Sangat Baik)</span>
                                        <span v-else-if="nilai.nilai_akhir >= 80"
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">B
                                            (Baik)</span>
                                        <span v-else-if="nilai.nilai_akhir >= 70"
                                            class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded">C
                                            (Cukup)</span>
                                        <span v-else
                                            class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded">D
                                            (Kurang)</span>
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