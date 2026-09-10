<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    jadwals: Array,
    rombels: Array,
    mapels: Array,
    gurus: Array,
});

const isModalOpen = ref(false);
const isEditMode = ref(false);

// State Form Inertia untuk Jadwal
const form = useForm({
    id: null,
    rombel_id: '',
    mata_pelajaran_id: '',
    guru_id: '',
    hari: 'Senin',
    jam_mulai: '',
    jam_selesai: '',
});

const openModal = (item = null) => {
    isModalOpen.value = true;
    if (item) {
        isEditMode.value = true;
        form.id = item.id;
        form.rombel_id = item.rombel_id;
        form.mata_pelajaran_id = item.mata_pelajaran_id;
        form.guru_id = item.guru_id;
        form.hari = item.hari;
        form.jam_mulai = item.jam_mulai.substring(0, 5); // Format HH:mm
        form.jam_selesai = item.jam_selesai.substring(0, 5);
    } else {
        isEditMode.value = false;
        form.reset();
    }
};

const closeModal = () => {
    isModalOpen.value = false;
    form.clearErrors();
    form.reset();
};

const submitForm = () => {
    if (isEditMode.value) {
        form.put(route('jadwal.update', form.id), { onSuccess: () => closeModal() });
    } else {
        form.post(route('jadwal.store'), { onSuccess: () => closeModal() });
    }
};

const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus jadwal ini?')) {
        form.delete(route('jadwal.destroy', id));
    }
};
</script>

<template>

    <Head title="Manajemen Jadwal Pelajaran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jadwal Pelajaran</h2>
                <PrimaryButton @click="openModal()">+ Tambah Jadwal</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Hari & Waktu</th>
                                    <th class="px-6 py-3">Kelas</th>
                                    <th class="px-6 py-3">Mata Pelajaran</th>
                                    <th class="px-6 py-3">Guru Pengajar</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="jadwals.length === 0" class="bg-white border-b">
                                    <td colspan="5" class="px-6 py-4 text-center italic">Belum ada jadwal pelajaran.
                                    </td>
                                </tr>
                                <tr v-else v-for="item in jadwals" :key="item.id"
                                    class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-900">{{ item.hari }}</span><br>
                                        <span class="text-xs">{{ item.jam_mulai.substring(0, 5) }} - {{
                                            item.jam_selesai.substring(0, 5) }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-indigo-600">{{ item.rombel?.nama_rombel }}
                                    </td>
                                    <td class="px-6 py-4">{{ item.mata_pelajaran?.nama_mapel }}</td>
                                    <td class="px-6 py-4">{{ item.guru?.nama_lengkap }}</td>
                                    <td class="px-6 py-4 space-x-3">
                                        <button @click="openModal(item)"
                                            class="text-blue-600 hover:underline">Edit</button>
                                        <button @click="deleteData(item.id)"
                                            class="text-red-600 hover:underline">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form Jadwal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ isEditMode ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Baru' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="hari" value="Hari" />
                            <select id="hari" v-model="form.hari"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                            <InputError :message="form.errors.hari" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="jam_mulai" value="Jam Mulai" />
                            <TextInput id="jam_mulai" v-model="form.jam_mulai" type="time" class="mt-1 block w-full"
                                required />
                            <InputError :message="form.errors.jam_mulai" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="jam_selesai" value="Jam Selesai" />
                            <TextInput id="jam_selesai" v-model="form.jam_selesai" type="time" class="mt-1 block w-full"
                                required />
                            <InputError :message="form.errors.jam_selesai" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="rombel_id" value="Kelas / Rombongan Belajar" />
                        <select id="rombel_id" v-model="form.rombel_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                            required>
                            <option value="" disabled>Pilih Kelas...</option>
                            <option v-for="rombel in rombels" :key="rombel.id" :value="rombel.id">
                                {{ rombel.nama_rombel }} (Tingkat {{ rombel.tingkat }})
                            </option>
                        </select>
                        <InputError :message="form.errors.rombel_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="mata_pelajaran_id" value="Mata Pelajaran" />
                        <select id="mata_pelajaran_id" v-model="form.mata_pelajaran_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                            required>
                            <option value="" disabled>Pilih Mata Pelajaran...</option>
                            <option v-for="mapel in mapels" :key="mapel.id" :value="mapel.id">
                                {{ mapel.nama_mapel }} (Kelompok {{ mapel.kelompok }})
                            </option>
                        </select>
                        <InputError :message="form.errors.mata_pelajaran_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="guru_id" value="Guru Pengajar" />
                        <select id="guru_id" v-model="form.guru_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                            required>
                            <option value="" disabled>Pilih Guru...</option>
                            <option v-for="guru in gurus" :key="guru.id" :value="guru.id">
                                {{ guru.nama_lengkap }}
                            </option>
                        </select>
                        <InputError :message="form.errors.guru_id" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                            Jadwal
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>