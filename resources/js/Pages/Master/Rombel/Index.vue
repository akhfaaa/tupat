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
    rombels: Array,
    tahun_ajarans: Array,
    jurusans: Array,
    gurus: Array,
});

const isModalOpen = ref(false);
const isEditMode = ref(false);

const form = useForm({
    id: null,
    tahun_ajaran_id: '',
    jurusan_id: '',
    wali_kelas_id: '',
    tingkat: 'X',
    nama_rombel: '',
});

const openModal = (item = null) => {
    isModalOpen.value = true;
    if (item) {
        isEditMode.value = true;
        form.id = item.id;
        form.tahun_ajaran_id = item.tahun_ajaran_id;
        form.jurusan_id = item.jurusan_id;
        form.wali_kelas_id = item.wali_kelas_id || '';
        form.tingkat = item.tingkat;
        form.nama_rombel = item.nama_rombel;
    } else {
        isEditMode.value = false;
        form.reset();

        // Auto-select tahun ajaran aktif jika ada
        const tahunAktif = props.tahun_ajarans.find(t => t.is_active);
        if (tahunAktif) form.tahun_ajaran_id = tahunAktif.id;
    }
};

const closeModal = () => {
    isModalOpen.value = false;
    form.clearErrors();
    form.reset();
};

const submitForm = () => {
    if (isEditMode.value) {
        form.put(route('rombel.update', form.id), { onSuccess: () => closeModal() });
    } else {
        form.post(route('rombel.store'), { onSuccess: () => closeModal() });
    }
};

const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus kelas ini? Data siswa di dalamnya mungkin akan terpengaruh.')) {
        form.delete(route('rombel.destroy', id));
    }
};
</script>

<template>

    <Head title="Master Data Kelas / Rombel" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Rombongan Belajar (Kelas)</h2>
                <PrimaryButton @click="openModal()">+ Tambah Kelas</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Nama Kelas</th>
                                    <th class="px-6 py-3">Tingkat & Jurusan</th>
                                    <th class="px-6 py-3">Tahun Ajaran</th>
                                    <th class="px-6 py-3">Wali Kelas</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="rombels.length === 0" class="bg-white border-b">
                                    <td colspan="5" class="px-6 py-4 text-center italic">Belum ada data kelas.</td>
                                </tr>
                                <tr v-else v-for="item in rombels" :key="item.id" class="bg-white border-b">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ item.nama_rombel }}</td>
                                    <td class="px-6 py-4">Kelas {{ item.tingkat }} - {{ item.jurusan?.kode_jurusan }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ item.tahun_ajaran?.tahun }}
                                        <span class="text-xs text-gray-400">({{ item.tahun_ajaran?.semester }})</span>
                                    </td>
                                    <td class="px-6 py-4">{{ item.wali_kelas?.nama_lengkap || 'Belum Ditentukan' }}</td>
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
                    {{ isEditMode ? 'Edit Kelas / Rombel' : 'Tambah Kelas Baru' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">

                    <!-- Grid Layout untuk Form -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="tahun_ajaran_id" value="Tahun Ajaran" />
                            <select id="tahun_ajaran_id" v-model="form.tahun_ajaran_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="" disabled>Pilih Tahun Ajaran...</option>
                                <option v-for="ta in tahun_ajarans" :key="ta.id" :value="ta.id">
                                    {{ ta.tahun }} ({{ ta.semester }}) {{ ta.is_active ? ' - Aktif' : '' }}
                                </option>
                            </select>
                            <InputError :message="form.errors.tahun_ajaran_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="jurusan_id" value="Jurusan" />
                            <select id="jurusan_id" v-model="form.jurusan_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="" disabled>Pilih Jurusan...</option>
                                <option v-for="jurusan in jurusans" :key="jurusan.id" :value="jurusan.id">
                                    {{ jurusan.nama_jurusan }}
                                </option>
                            </select>
                            <InputError :message="form.errors.jurusan_id" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="tingkat" value="Tingkat Kelas" />
                            <select id="tingkat" v-model="form.tingkat"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="X">X (Sepuluh)</option>
                                <option value="XI">XI (Sebelas)</option>
                                <option value="XII">XII (Dua Belas)</option>
                            </select>
                            <InputError :message="form.errors.tingkat" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="nama_rombel" value="Nama Unik Kelas (Contoh: X TKJ 1)" />
                            <TextInput id="nama_rombel" v-model="form.nama_rombel" type="text" class="mt-1 block w-full"
                                required />
                            <InputError :message="form.errors.nama_rombel" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="wali_kelas_id" value="Wali Kelas (Opsional)" />
                        <select id="wali_kelas_id" v-model="form.wali_kelas_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Belum Ditentukan --</option>
                            <option v-for="guru in gurus" :key="guru.id" :value="guru.id">
                                {{ guru.nama_lengkap }}
                            </option>
                        </select>
                        <InputError :message="form.errors.wali_kelas_id" class="mt-2" />
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