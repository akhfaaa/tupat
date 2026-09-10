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
    siswas: Array,
    jurusans: Array,
    rombels: Array,
});

const isModalOpen = ref(false);
const isEditMode = ref(false);

const form = useForm({
    id: null,
    nisn: '',
    nis: '',
    nama_lengkap: '',
    jenis_kelamin: 'L',
    no_telp: '',
    jurusan_id: '',
    rombel_id: '',
});

const openModal = (item = null) => {
    isModalOpen.value = true;
    if (item) {
        isEditMode.value = true;
        form.id = item.id;
        form.nisn = item.nisn;
        form.nis = item.nis || '';
        form.nama_lengkap = item.nama_lengkap;
        form.jenis_kelamin = item.jenis_kelamin;
        form.no_telp = item.no_telp || '';
        form.jurusan_id = item.jurusan_id;
        form.rombel_id = item.rombel_id || '';
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
        form.put(route('siswa.update', form.id), { onSuccess: () => closeModal() });
    } else {
        form.post(route('siswa.store'), { onSuccess: () => closeModal() });
    }
};

const deleteData = (id) => {
    if (confirm('Yakin ingin menghapus data siswa ini beserta akun loginnya secara permanen?')) {
        form.delete(route('siswa.destroy', id));
    }
};
</script>

<template>

    <Head title="Master Data Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Induk Siswa</h2>
                <PrimaryButton @click="openModal()">+ Daftarkan Siswa</PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">NISN</th>
                                    <th class="px-6 py-3">Nama Lengkap</th>
                                    <th class="px-6 py-3">L/P</th>
                                    <th class="px-6 py-3">Jurusan & Kelas</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="siswas.length === 0" class="bg-white border-b">
                                    <td colspan="5" class="px-6 py-4 text-center italic">Belum ada data siswa.</td>
                                </tr>
                                <tr v-else v-for="item in siswas" :key="item.id"
                                    class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-bold">{{ item.nisn }}</td>
                                    <td class="px-6 py-4 text-gray-900">{{ item.nama_lengkap }}</td>
                                    <td class="px-6 py-4">{{ item.jenis_kelamin }}</td>
                                    <td class="px-6 py-4">
                                        {{ item.jurusan?.kode_jurusan }} <br>
                                        <span class="text-xs text-indigo-600 font-semibold">{{ item.rombel?.nama_rombel
                                            ||
                                            'Belum Masuk Rombel' }}</span>
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
                <h2 class="text-lg font-medium text-gray-900 mb-2">
                    {{ isEditMode ? 'Edit Profil Siswa' : 'Daftarkan Siswa Baru' }}
                </h2>
                <p v-if="!isEditMode" class="text-sm text-amber-600 mb-6 bg-amber-50 p-2 rounded">
                    Sistem akan otomatis membuatkan akun login. <br>
                    <strong>Email:</strong> [nisn]@smk.com | <strong>Password:</strong> [nisn]
                </p>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="nisn" value="NISN (Nomor Induk Siswa Nasional)" />
                            <TextInput id="nisn" v-model="form.nisn" type="text" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.nisn" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="nis" value="NIS Sekolah (Opsional)" />
                            <TextInput id="nis" v-model="form.nis" type="text" class="mt-1 block w-full" />
                            <InputError :message="form.errors.nis" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="nama_lengkap" value="Nama Lengkap Siswa" />
                        <TextInput id="nama_lengkap" v-model="form.nama_lengkap" type="text" class="mt-1 block w-full"
                            required />
                        <InputError :message="form.errors.nama_lengkap" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="jenis_kelamin" value="Jenis Kelamin" />
                            <select id="jenis_kelamin" v-model="form.jenis_kelamin"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <InputError :message="form.errors.jenis_kelamin" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="no_telp" value="No. WhatsApp (Opsional)" />
                            <TextInput id="no_telp" v-model="form.no_telp" type="text" class="mt-1 block w-full" />
                            <InputError :message="form.errors.no_telp" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="jurusan_id" value="Konsentrasi Keahlian / Jurusan" />
                            <select id="jurusan_id" v-model="form.jurusan_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="" disabled>Pilih Jurusan...</option>
                                <option v-for="jurusan in jurusans" :key="jurusan.id" :value="jurusan.id">
                                    {{ jurusan.kode_jurusan }} - {{ jurusan.nama_jurusan }}
                                </option>
                            </select>
                            <InputError :message="form.errors.jurusan_id" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="rombel_id" value="Tempatkan di Rombel/Kelas" />
                            <select id="rombel_id" v-model="form.rombel_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Belum Masuk Kelas --</option>
                                <option v-for="rombel in rombels" :key="rombel.id" :value="rombel.id">
                                    {{ rombel.nama_rombel }} ({{ rombel.tahun_ajaran?.tahun }})
                                </option>
                            </select>
                            <InputError :message="form.errors.rombel_id" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Simpan
                            Data
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>