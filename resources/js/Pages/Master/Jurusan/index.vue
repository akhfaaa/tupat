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

defineProps({ jurusans: Array });

const isModalOpen = ref(false);
const isEditMode = ref(false);
const form = useForm({ id: null, kode_jurusan: '', nama_jurusan: '', deskripsi: '' });

const openModal = (jurusan = null) => {
    isModalOpen.value = true;
    isEditMode.value = Boolean(jurusan);
    form.id = jurusan?.id ?? null;
    form.kode_jurusan = jurusan?.kode_jurusan ?? '';
    form.nama_jurusan = jurusan?.nama_jurusan ?? '';
    form.deskripsi = jurusan?.deskripsi ?? '';
};

const closeModal = () => {
    isModalOpen.value = false;
    form.clearErrors();
    form.reset();
};

const submitForm = () => {
    const options = { onSuccess: closeModal };
    isEditMode.value ? form.put(route('jurusan.update', form.id), options) : form.post(route('jurusan.store'), options);
};

const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus jurusan ini?')) form.delete(route('jurusan.destroy', id));
};
</script>

<template>
    <Head title="Master Data Jurusan" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Data Jurusan</h2>
                    <p class="mt-1 text-sm text-gray-500">Kelola konsentrasi keahlian sekolah.</p>
                </div>
                <PrimaryButton type="button" @click="openModal()">Tambah Jurusan</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-500">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                                <tr><th class="px-6 py-3">Kode</th><th class="px-6 py-3">Nama Jurusan</th><th class="px-6 py-3">Aksi</th></tr>
                            </thead>
                            <tbody>
                                <tr v-if="jurusans.length === 0"><td colspan="3" class="px-6 py-8 text-center italic">Belum ada data jurusan.</td></tr>
                                <tr v-for="jurusan in jurusans" v-else :key="jurusan.id" class="border-b">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ jurusan.kode_jurusan }}</td>
                                    <td class="px-6 py-4">{{ jurusan.nama_jurusan }}</td>
                                    <td class="space-x-4 px-6 py-4">
                                        <button type="button" class="text-blue-600 hover:underline" @click="openModal(jurusan)">Edit</button>
                                        <button type="button" class="text-red-600 hover:underline" @click="deleteData(jurusan.id)">Hapus</button>
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
                <h2 class="mb-4 text-lg font-medium text-gray-900">{{ isEditMode ? 'Edit Jurusan' : 'Tambah Jurusan Baru' }}</h2>
                <form class="space-y-4" @submit.prevent="submitForm">
                    <div><InputLabel for="kode_jurusan" value="Kode Jurusan" /><TextInput id="kode_jurusan" v-model="form.kode_jurusan" class="mt-1 block w-full" required /><InputError :message="form.errors.kode_jurusan" /></div>
                    <div><InputLabel for="nama_jurusan" value="Nama Jurusan" /><TextInput id="nama_jurusan" v-model="form.nama_jurusan" class="mt-1 block w-full" required /><InputError :message="form.errors.nama_jurusan" /></div>
                    <div><InputLabel for="deskripsi" value="Deskripsi" /><TextInput id="deskripsi" v-model="form.deskripsi" class="mt-1 block w-full" /><InputError :message="form.errors.deskripsi" /></div>
                    <div class="flex justify-end gap-3"><SecondaryButton type="button" @click="closeModal">Batal</SecondaryButton><PrimaryButton :disabled="form.processing">Simpan</PrimaryButton></div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
