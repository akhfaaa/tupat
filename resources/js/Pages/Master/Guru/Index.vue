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
    gurus: Array,
});

const isModalOpen = ref(false);
const isEditMode = ref(false);

const form = useForm({
    id: null,
    nip: '',
    nama_lengkap: '',
    jenis_kelamin: 'L',
    no_telp: '',
});

const openModal = (item = null) => {
    isModalOpen.value = true;
    if (item) {
        isEditMode.value = true;
        form.id = item.id;
        form.nip = item.nip || '';
        form.nama_lengkap = item.nama_lengkap;
        form.jenis_kelamin = item.jenis_kelamin;
        form.no_telp = item.no_telp || '';
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
        form.put(route('guru.update', form.id), { onSuccess: () => closeModal() });
    } else {
        form.post(route('guru.store'), { onSuccess: () => closeModal() });
    }
};

const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus data guru ini?')) {
        form.delete(route('guru.destroy', id));
    }
};
</script>

<template>

    <Head title="Master Data Guru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Tenaga Pendidik (Guru)</h2>
                <PrimaryButton @click="openModal()">+ Tambah Guru</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">NIP</th>
                                    <th class="px-6 py-3">Nama Lengkap</th>
                                    <th class="px-6 py-3">L/P</th>
                                    <th class="px-6 py-3">No. Telp</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="gurus.length === 0" class="bg-white border-b">
                                    <td colspan="5" class="px-6 py-4 text-center italic">Belum ada data guru.</td>
                                </tr>
                                <tr v-else v-for="item in gurus" :key="item.id" class="bg-white border-b">
                                    <td class="px-6 py-4">{{ item.nip || '-' }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ item.nama_lengkap }}</td>
                                    <td class="px-6 py-4">{{ item.jenis_kelamin }}</td>
                                    <td class="px-6 py-4">{{ item.no_telp || '-' }}</td>
                                    <td class="px-6 py-4 space-x-4">
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
                    {{ isEditMode ? 'Edit Data Guru' : 'Tambah Guru Baru' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="nip" value="NIP (Opsional)" />
                        <TextInput id="nip" v-model="form.nip" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.nip" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="nama_lengkap" value="Nama Lengkap (beserta gelar)" />
                        <TextInput id="nama_lengkap" v-model="form.nama_lengkap" type="text" class="mt-1 block w-full"
                            required />
                        <InputError :message="form.errors.nama_lengkap" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
                        <select id="jenis_kelamin" v-model="form.jenis_kelamin"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <InputError :message="form.errors.jenis_kelamin" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="no_telp" value="Nomor Telepon/WhatsApp (Opsional)" />
                        <TextInput id="no_telp" v-model="form.no_telp" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.no_telp" class="mt-2" />
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