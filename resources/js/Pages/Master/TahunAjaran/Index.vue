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
    tahun_ajarans: Array,
});

const isModalOpen = ref(false);
const isEditMode = ref(false);

const form = useForm({
    id: null,
    tahun: '',
    semester: 'Ganjil',
    is_active: false,
});

const openModal = (item = null) => {
    isModalOpen.value = true;
    if (item) {
        isEditMode.value = true;
        form.id = item.id;
        form.tahun = item.tahun;
        form.semester = item.semester;
        form.is_active = item.is_active === 1; // Konversi tinyint mysql ke boolean
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
        form.put(route('tahun-ajaran.update', form.id), { onSuccess: () => closeModal() });
    } else {
        form.post(route('tahun-ajaran.store'), { onSuccess: () => closeModal() });
    }
};

const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        form.delete(route('tahun-ajaran.destroy', id));
    }
};
</script>

<template>

    <Head title="Master Data Tahun Ajaran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Tahun Ajaran</h2>
                <PrimaryButton @click="openModal()">+ Tambah Data</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Tahun Ajaran</th>
                                    <th class="px-6 py-3">Semester</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="tahun_ajarans.length === 0" class="bg-white border-b">
                                    <td colspan="4" class="px-6 py-4 text-center italic">Belum ada data.</td>
                                </tr>
                                <tr v-else v-for="item in tahun_ajarans" :key="item.id" class="bg-white border-b">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ item.tahun }}</td>
                                    <td class="px-6 py-4">{{ item.semester }}</td>
                                    <td class="px-6 py-4">
                                        <span v-if="item.is_active"
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Aktif</span>
                                        <span v-else
                                            class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">Nonaktif</span>
                                    </td>
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
                    {{ isEditMode ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="tahun" value="Tahun (Contoh: 2025/2026)" />
                        <TextInput id="tahun" v-model="form.tahun" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.tahun" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="semester" value="Semester" />
                        <select id="semester" v-model="form.semester"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                        <InputError :message="form.errors.semester" class="mt-2" />
                    </div>

                    <div class="flex items-center mt-4">
                        <input id="is_active" v-model="form.is_active" type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="ms-2 text-sm text-gray-600">Jadikan Semester Aktif saat
                            ini</label>
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