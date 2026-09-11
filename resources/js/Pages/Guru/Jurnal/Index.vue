<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    rombels: Array,
    mapels: Array,
    siswas: Array,
    filters: Object,
    riwayat_jurnals: Array,
});

const selectedRombel = ref(props.filters.rombel_id || '');

const filterData = () => {
    form.rombel_id = selectedRombel.value;
    router.get(route('jurnal.index'), {
        rombel_id: selectedRombel.value,
    }, { preserveState: true, preserveScroll: true });
};

const form = useForm({
    rombel_id: selectedRombel.value,
    mata_pelajaran_id: '',
    tanggal: new Date().toISOString().substr(0, 10),
    jam_ke: '',
    materi_pembelajaran: '',
    catatan_kelas: '',
    absensi: [],
});

watch(() => props.siswas, (newSiswas) => {
    form.absensi = newSiswas.map(siswa => ({
        siswa_id: siswa.id,
        nama_lengkap: siswa.nama_lengkap,
        nisn: siswa.nisn,
        status: 'Hadir',
        keterangan: '',
    }));
}, { immediate: true });

const submitJurnal = () => {
    form.post(route('jurnal.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('materi_pembelajaran', 'catatan_kelas', 'jam_ke');
            alert('Jurnal dan presensi berhasil disimpan!');
        }
    });
};
</script>

<template>
    <Head title="Jurnal & Presensi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jurnal Mengajar & Presensi Harian</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-fit">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Informasi Jurnal</h3>

                        <div class="space-y-4">
                            <div>
                                <InputLabel value="Pilih Kelas" />
                                <select v-model="selectedRombel" @change="filterData"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">-- Pilih Kelas --</option>
                                    <option v-for="rombel in rombels" :key="rombel.id" :value="rombel.id">{{ rombel.nama_rombel }}</option>
                                </select>
                            </div>

                            <div>
                                <InputLabel value="Mata Pelajaran" />
                                <select v-model="form.mata_pelajaran_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="" disabled>-- Pilih Mapel --</option>
                                    <option v-for="mapel in mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel value="Tanggal" />
                                    <TextInput type="date" v-model="form.tanggal" class="mt-1 block w-full text-sm" required />
                                </div>
                                <div>
                                    <InputLabel value="Jam Ke-" />
                                    <TextInput type="text" v-model="form.jam_ke" placeholder="Contoh: 1-2" class="mt-1 block w-full text-sm" required />
                                </div>
                            </div>

                            <div>
                                <InputLabel value="Materi Pembelajaran" />
                                <textarea v-model="form.materi_pembelajaran" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required></textarea>
                            </div>

                            <div>
                                <InputLabel value="Catatan Kelas (Opsional)" />
                                <textarea v-model="form.catatan_kelas" rows="2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Presensi Siswa</h3>
                            <span v-if="selectedRombel" class="text-sm text-gray-500">Total: {{ form.absensi.length }} Siswa</span>
                        </div>

                        <div v-if="!selectedRombel" class="text-center py-8 text-gray-500 italic">
                            Silakan pilih kelas terlebih dahulu untuk memuat daftar siswa.
                        </div>

                        <form v-else @submit.prevent="submitJurnal">
                            <div class="overflow-x-auto max-h-[500px]">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3">Nama Siswa</th>
                                            <th class="px-4 py-3 text-center">Hadir</th>
                                            <th class="px-4 py-3 text-center">Sakit</th>
                                            <th class="px-4 py-3 text-center">Izin</th>
                                            <th class="px-4 py-3 text-center">Alpa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in form.absensi" :key="item.siswa_id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ item.nama_lengkap }}</td>
                                            <td v-for="status in ['Hadir', 'Sakit', 'Izin', 'Alpa']" :key="status" class="px-4 py-3 text-center">
                                                <input type="radio" :name="'status_' + index" :value="status" v-model="item.status"
                                                    class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Simpan Jurnal & Presensi
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
