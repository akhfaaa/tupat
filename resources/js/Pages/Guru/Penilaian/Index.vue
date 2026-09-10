<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    rombels: Array,
    mapels: Array,
    siswas: Array,
    filters: Object,
});

const selectedRombel = ref(props.filters.rombel_id || '');
const selectedMapel = ref(props.filters.mata_pelajaran_id || '');

// Ketika filter kelas/mapel berubah, ambil data siswa secara reaktif
const filterData = () => {
    router.get(route('penilaian.index'), {
        rombel_id: selectedRombel.value,
        mata_pelajaran_id: selectedMapel.value,
    }, { preserveState: true, preserveScroll: true });
};

// Form Inertia untuk menampung array nilai seluruh siswa
const form = useForm({
    mata_pelajaran_id: selectedMapel,
    nilais: [],
});

// Sinkronkan data siswa ke dalam form saat props siswas berubah
watch(() => props.siswas, (newSiswas) => {
    form.nilais = newSiswas.map(siswa => {
        const existingNilai = siswa.nilais && siswa.nilais.length > 0 ? siswa.nilais[0] : null;
        return {
            siswa_id: siswa.id,
            nama_lengkap: siswa.nama_lengkap,
            nisn: siswa.nisn,
            nilai_tugas: existingNilai ? existingNilai.nilai_tugas : 0,
            nilai_uts: existingNilai ? existingNilai.nilai_uts : 0,
            nilai_uas: existingNilai ? existingNilai.nilai_uas : 0,
        };
    });
}, { immediate: true });

const submitPenilaian = () => {
    form.mata_pelajaran_id = selectedMapel.value;
    form.post(route('penilaian.store'), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Input Nilai Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengelolaan Nilai Akademik Siswa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Kotak Filter Kelas & Mapel -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kelas (Rombel)</label>
                            <select v-model="selectedRombel" @change="filterData"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Kelas --</option>
                                <option v-for="rombel in rombels" :key="rombel.id" :value="rombel.id">
                                    {{ rombel.nama_rombel }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Mata Pelajaran</label>
                            <select v-model="selectedMapel" @change="filterData"
                                class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                <option v-for="mapel in mapels" :key="mapel.id" :value="mapel.id">
                                    {{ mapel.nama_mapel }} (Kelompok {{ mapel.kelompok }})
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tabel Input Nilai Massal -->
                <div v-if="selectedRombel && selectedMapel"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submitPenilaian">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3">NISN</th>
                                        <th class="px-4 py-3">Nama Siswa</th>
                                        <th class="px-4 py-3 text-center">Tugas (30%)</th>
                                        <th class="px-4 py-3 text-center">UTS (30%)</th>
                                        <th class="px-4 py-3 text-center">UAS (40%)</th>
                                        <th class="px-4 py-3 text-center">Nilai Akhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="form.nilais.length === 0">
                                        <td colspan="6" class="px-4 py-4 text-center italic">Tidak ada siswa di kelas
                                            ini.</td>
                                    </tr>
                                    <tr v-for="(item, index) in form.nilais" :key="item.siswa_id" class="border-b">
                                        <td class="px-4 py-3 font-medium">{{ item.nisn }}</td>
                                        <td class="px-4 py-3 text-gray-900 font-semibold">{{ item.nama_lengkap }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" min="0" max="100" v-model.number="item.nilai_tugas"
                                                class="w-20 text-center border-gray-300 rounded-md shadow-sm text-sm" />
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" min="0" max="100" v-model.number="item.nilai_uts"
                                                class="w-20 text-center border-gray-300 rounded-md shadow-sm text-sm" />
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" min="0" max="100" v-model.number="item.nilai_uas"
                                                class="w-20 text-center border-gray-300 rounded-md shadow-sm text-sm" />
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-indigo-600">
                                            {{ ((item.nilai_tugas * 0.3) + (item.nilai_uts * 0.3) + (item.nilai_uas *
                                            0.4)).toFixed(2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Simpan Seluruh Nilai Kelas
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <div v-else class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-lg text-sm text-center">
                    Silakan tentukan <strong>Kelas</strong> dan <strong>Mata Pelajaran</strong> terlebih dahulu pada
                    filter di
                    atas untuk memuat daftar lembar kerja siswa.
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>