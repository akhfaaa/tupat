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

defineProps({
    jurusans: Array,
});

// State untuk mengontrol Modal
const isModalOpen = ref(false);
const isEditMode = ref(false);

// State Form Inertia
const form = useForm({
    id: null,
    kode_jurusan: '',
    nama_jurusan: '',
    deskripsi: '',
});

// Fungsi buka modal (Bisa untuk Tambah atau Edit)
const openModal = (jurusan = null) => {
    isModalOpen.value = true;
    if (jurusan) {
        isEditMode.value = true;
        form.id = jurusan.id;
        form.kode_jurusan = jurusan.kode_jurusan;
        form.nama_jurusan = jurusan.nama_jurusan;
        form.deskripsi = jurusan.deskripsi || '';
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

// Fungsi Simpan Data
const submitForm = () => {
    if (isEditMode.value) {
        form.put(route('jurusan.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('jurusan.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

// Fungsi Hapus Data
const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus jurusan ini?')) {
        form.delete(route('jurusan.destroy', id));
    }
};
</script>

<template>

    <Head title="Master Data Jurusan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Jurusan</h2>
                <PrimaryButton @click="openModal()">+ Tambah Jurusan</PrimaryButton>
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
                                    <th class="px-6 py-3">Nama Jurusan</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="jurusans.length === 0" class="bg-white border-b">
                                    <td colspan="3" class="px-6 py-4 text-center italic">Belum ada data jurusan.</td>
                                </tr>
                                <tr v-else v-for="jurusan in jurusans" :key="jurusan.id" class="bg-white border-b">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ jurusan.kode_jurusan }}</td>
                                    <td class="px-6 py-4">{{ jurusan.nama_jurusan }}</td>
                                    <td class="px-6 py-4 space-x-4">
                                        <button @click="openModal(jurusan)"
                                            class="text-blue-600 hover:underline">Edit</button>
                                        <button @click="deleteData(jurusan.id)"
                                            class="text-red-600 hover:underline">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form Tambah/Edit -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ isEditMode ? 'Edit Jurusan' : 'Tambah Jurusan Baru' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="kode_jurusan" value="Kode Jurusan (Contoh: TKJ)" />
                        <TextInput id="kode_jurusan" v-model="form.kode_jurusan" type="text"
                            class="mt-1 block w-full" />
                        <InputError :message="form.errors.kode_jurusan" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="nama_jurusan" value="Nama Lengkap Jurusan" />
                        <TextInput id="nama_jurusan" v-model="form.nama_jurusan" type="text"
                            class="mt-1 block w-full" />
                        <InputError :message="form.errors.nama_jurusan" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="deskripsi" value="Deskripsi Singkat (Opsional)" />
                        <TextInput id="deskripsi" v-model="form.deskripsi" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.deskripsi" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Simpan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>