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

defineProps({ mapels: Array });

const isModalOpen = ref(false);
const isEditMode = ref(false);

const form = useForm({
    id: null,
    kode_mapel: '',
    nama_mapel: '',
    kelompok: 'A',
});

const openModal = (item = null) => {
    isModalOpen.value = true;
    if (item) {
        isEditMode.value = true;
        form.id = item.id;
        form.kode_mapel = item.kode_mapel;
        form.nama_mapel = item.nama_mapel;
        form.kelompok = item.kelompok;
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
        form.put(route('mata-pelajaran.update', form.id), { onSuccess: () => closeModal() });
    } else {
        form.post(route('mata-pelajaran.store'), { onSuccess: () => closeModal() });
    }
};

const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus mata pelajaran ini?')) {
        form.delete(route('mata-pelajaran.destroy', id));
    }
};
</script>

<template>

    <Head title="Master Data Mata Pelajaran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Mata Pelajaran</h2>
                <PrimaryButton @click="openModal()">+ Tambah Mapel</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Kode</th>
                                    <th class="px-6 py-3">Nama Mata Pelajaran</th>
                                    <th class="px-6 py-3">Kelompok</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="mapels.length === 0" class="bg-white border-b">
                                    <td colspan="4" class="px-6 py-4 text-center italic">Belum ada data.</td>
                                </tr>
                                <tr v-else v-for="item in mapels" :key="item.id" class="bg-white border-b">
                                    <td class="px-6 py-4 font-bold">{{ item.kode_mapel }}</td>
                                    <td class="px-6 py-4 text-gray-900">{{ item.nama_mapel }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            Kelompok {{ item.kelompok }}
                                        </span>
                                    </td>
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

        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ isEditMode ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="kode_mapel" value="Kode Mapel (Contoh: PAI, BING)" />
                        <TextInput id="kode_mapel" v-model="form.kode_mapel" type="text"
                            class="mt-1 block w-full uppercase" required />
                        <InputError :message="form.errors.kode_mapel" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="nama_mapel" value="Nama Mata Pelajaran" />
                        <TextInput id="nama_mapel" v-model="form.nama_mapel" type="text" class="mt-1 block w-full"
                            required />
                        <InputError :message="form.errors.nama_mapel" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="kelompok" value="Kelompok Mata Pelajaran (Kurikulum SMK)" />
                        <select id="kelompok" v-model="form.kelompok"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                            <option value="A">Kelompok A (Muatan Nasional)</option>
                            <option value="B">Kelompok B (Muatan Kewilayahan)</option>
                            <option value="C">Kelompok C (Muatan Peminatan Kejuruan)</option>
                        </select>
                        <InputError :message="form.errors.kelompok" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>