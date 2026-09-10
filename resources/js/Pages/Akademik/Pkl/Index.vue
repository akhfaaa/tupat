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
    pkls: Array,
    siswas: Array,
    gurus: Array,
});

const isModalOpen = ref(false);
const isEditMode = ref(false);

const form = useForm({
    id: null,
    siswa_id: '',
    guru_id: '',
    nama_perusahaan: '',
    divisi_pekerjaan: '',
    alamat_perusahaan: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    status: 'Pengajuan',
});

const openModal = (item = null) => {
    isModalOpen.value = true;
    if (item) {
        isEditMode.value = true;
        form.id = item.id;
        form.siswa_id = item.siswa_id;
        form.guru_id = item.guru_id || '';
        form.nama_perusahaan = item.nama_perusahaan;
        form.divisi_pekerjaan = item.divisi_pekerjaan || '';
        form.alamat_perusahaan = item.alamat_perusahaan || '';
        form.tanggal_mulai = item.tanggal_mulai;
        form.tanggal_selesai = item.tanggal_selesai;
        form.status = item.status;
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
        form.put(route('pkl.update', form.id), { onSuccess: () => closeModal() });
    } else {
        form.post(route('pkl.store'), { onSuccess: () => closeModal() });
    }
};

const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus data PKL siswa ini?')) {
        form.delete(route('pkl.destroy', id));
    }
};

const formatTanggal = (tanggal) => {
    return new Date(tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};
</script>

<template>
    <Head title="Manajemen Data PKL" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Praktik Kerja Lapangan (PKL)</h2>
                <PrimaryButton @click="openModal()">+ Tambah Data PKL</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Siswa</th>
                                    <th class="px-6 py-3">Tempat PKL</th>
                                    <th class="px-6 py-3">Waktu Pelaksanaan</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="pkls.length === 0" class="bg-white border-b">
                                    <td colspan="5" class="px-6 py-4 text-center italic">Belum ada data PKL.</td>
                                </tr>
                                <tr v-else v-for="item in pkls" :key="item.id" class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-900">{{ item.siswa?.nama_lengkap }}</span><br>
                                        <span class="text-xs text-gray-500">Pembimbing: {{ item.guru?.nama_lengkap || 'Belum diatur' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-indigo-600">{{ item.nama_perusahaan }}</span><br>
                                        <span class="text-xs">{{ item.divisi_pekerjaan || '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        {{ formatTanggal(item.tanggal_mulai) }} - <br>
                                        {{ formatTanggal(item.tanggal_selesai) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span v-if="item.status === 'Pengajuan'" class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Pengajuan</span>
                                        <span v-if="item.status === 'Aktif'" class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Aktif</span>
                                        <span v-if="item.status === 'Selesai'" class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Selesai</span>
                                    </td>
                                    <td class="px-6 py-4 space-x-3">
                                        <button @click="openModal(item)" class="text-blue-600 hover:underline">Edit</button>
                                        <button @click="deleteData(item.id)" class="text-red-600 hover:underline">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form PKL -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ isEditMode ? 'Edit Data PKL' : 'Daftarkan Siswa PKL' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="siswa_id" value="Pilih Siswa" />
                            <select id="siswa_id" v-model="form.siswa_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled>Pilih Siswa...</option>
                                <option v-for="siswa in siswas" :key="siswa.id" :value="siswa.id">
                                    {{ siswa.nama_lengkap }} ({{ siswa.nisn }})
                                </option>
                            </select>
                            <InputError :message="form.errors.siswa_id" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="guru_id" value="Guru Pembimbing Sekolah" />
                            <select id="guru_id" v-model="form.guru_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Belum Ditentukan --</option>
                                <option v-for="guru in gurus" :key="guru.id" :value="guru.id">
                                    {{ guru.nama_lengkap }}
                                </option>
                            </select>
                            <InputError :message="form.errors.guru_id" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="nama_perusahaan" value="Nama Instansi/Perusahaan" />
                            <TextInput id="nama_perusahaan" v-model="form.nama_perusahaan" type="text" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.nama_perusahaan" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="divisi_pekerjaan" value="Divisi / Bagian (Opsional)" />
                            <TextInput id="divisi_pekerjaan" v-model="form.divisi_pekerjaan" type="text" class="mt-1 block w-full" />
                            <InputError :message="form.errors.divisi_pekerjaan" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="tanggal_mulai" value="Tanggal Mulai" />
                            <TextInput id="tanggal_mulai" v-model="form.tanggal_mulai" type="date" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.tanggal_mulai" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="tanggal_selesai" value="Tanggal Selesai" />
                            <TextInput id="tanggal_selesai" v-model="form.tanggal_selesai" type="date" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.tanggal_selesai" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <div>
                            <InputLabel for="status" value="Status Pelaksanaan" />
                            <select id="status" v-model="form.status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                                <option value="Pengajuan">Pengajuan (Belum Mulai)</option>
                                <option value="Aktif">Aktif (Sedang Berjalan)</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <InputError :message="form.errors.status" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan Data</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>