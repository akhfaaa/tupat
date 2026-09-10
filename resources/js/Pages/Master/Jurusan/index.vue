<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Menerima data 'jurusans' yang dikirim dari JurusanController
defineProps({
    jurusans: Array,
});
</script>

<template>

    <Head title="Master Data Jurusan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Jurusan</h2>
                <!-- Tombol Tambah (Fungsinya menyusul) -->
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                    + Tambah Jurusan
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Kode</th>
                                    <th scope="col" class="px-6 py-3">Nama Jurusan</th>
                                    <th scope="col" class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Jika data kosong -->
                                <tr v-if="jurusans.length === 0" class="bg-white border-b">
                                    <td colspan="3" class="px-6 py-4 text-center italic">Belum ada data jurusan.</td>
                                </tr>

                                <!-- Looping data jurusan -->
                                <tr v-else v-for="jurusan in jurusans" :key="jurusan.id" class="bg-white border-b">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ jurusan.kode_jurusan }}</td>
                                    <td class="px-6 py-4">{{ jurusan.nama_jurusan }}</td>
                                    <td class="px-6 py-4 space-x-2">
                                        <button class="text-blue-600 hover:underline">Edit</button>
                                        <button class="text-red-600 hover:underline">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>