<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    rombels: Array,
    mapels: Array,
    siswas: Array,
    filters: Object,
});

const selectedRombel = ref(props.filters.rombel_id || '');
const selectedMapel = ref(props.filters.mata_pelajaran_id || '');
const form = useForm({
    mata_pelajaran_id: selectedMapel.value,
    nilais: [],
});

const filterData = () => {
    router.get(route('penilaian.index'), {
        rombel_id: selectedRombel.value,
        mata_pelajaran_id: selectedMapel.value,
    }, { preserveState: true, preserveScroll: true });
};

watch(() => props.siswas, (siswas) => {
    form.nilais = siswas.map((siswa) => ({
        siswa_id: siswa.id,
        nama_lengkap: siswa.nama_lengkap,
        nilai_tugas: siswa.nilais?.[0]?.nilai_tugas ?? '',
        nilai_uts: siswa.nilais?.[0]?.nilai_uts ?? '',
        nilai_uas: siswa.nilais?.[0]?.nilai_uas ?? '',
    }));
}, { immediate: true });

watch(selectedMapel, (value) => {
    form.mata_pelajaran_id = value;
});

const nilaiAkhir = (nilai) => ((Number(nilai.nilai_tugas || 0) * 0.3)
    + (Number(nilai.nilai_uts || 0) * 0.3)
    + (Number(nilai.nilai_uas || 0) * 0.4)).toFixed(2);

const submitNilai = () => {
    form.post(route('penilaian.store'), {
        preserveScroll: true,
        onSuccess: () => alert('Nilai berhasil disimpan!'),
    });
};
</script>

<template>
    <Head title="E-Rapor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Input Nilai E-Rapor</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel value="Pilih Kelas" />
                            <select v-model="selectedRombel" @change="filterData" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">-- Pilih Kelas --</option>
                                <option v-for="rombel in rombels" :key="rombel.id" :value="rombel.id">{{ rombel.nama_rombel }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Mata Pelajaran" />
                            <select v-model="selectedMapel" @change="filterData" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                <option v-for="mapel in mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div v-if="!selectedRombel || !selectedMapel" class="text-center py-8 text-gray-500 italic">
                        Pilih kelas dan mata pelajaran untuk memuat daftar nilai.
                    </div>
                    <form v-else @submit.prevent="submitNilai">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3">Nama Siswa</th>
                                        <th class="px-4 py-3">Tugas</th>
                                        <th class="px-4 py-3">UTS</th>
                                        <th class="px-4 py-3">UAS</th>
                                        <th class="px-4 py-3">Nilai Akhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="nilai in form.nilais" :key="nilai.siswa_id" class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ nilai.nama_lengkap }}</td>
                                        <td class="px-4 py-3"><input v-model="nilai.nilai_tugas" type="number" min="0" max="100" class="w-24 border-gray-300 rounded-md text-sm"></td>
                                        <td class="px-4 py-3"><input v-model="nilai.nilai_uts" type="number" min="0" max="100" class="w-24 border-gray-300 rounded-md text-sm"></td>
                                        <td class="px-4 py-3"><input v-model="nilai.nilai_uas" type="number" min="0" max="100" class="w-24 border-gray-300 rounded-md text-sm"></td>
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ nilaiAkhir(nilai) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="!form.nilais.length" class="text-center py-8 text-gray-500">Belum ada siswa di kelas ini.</div>
                        <div v-else class="mt-6 flex justify-end">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan Nilai</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
