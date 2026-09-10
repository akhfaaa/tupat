<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

// Mengambil data user yang dikirim dari HandleInertiaRequests.php
const page = usePage();
const user = computed(() => page.props.auth.user);

// Fungsi pembantu untuk mengecek role
const hasRole = (roleName) => {
    return user.value.roles.includes(roleName);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                SIAKAD SMK Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Kotak Selamat Datang Umum -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        Selamat datang,
                        <span class="font-bold">{{ user.name }}</span
                        >!
                        <br />
                        Status Akses Anda:
                        <span
                            v-for="role in user.roles"
                            :key="role"
                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 uppercase mr-2 mt-2"
                        >
                            {{ role }}
                        </span>
                    </div>
                </div>

                <!-- Modul Khusus Super Admin & TU -->
                <div
                    v-if="hasRole('super-admin') || hasRole('tu')"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                >
                    <div class="bg-indigo-500 text-white p-4 rounded-lg shadow">
                        <h3 class="font-bold text-lg">Master Data</h3>
                        <p class="text-sm mt-1">
                            Kelola Siswa, Guru, dan Jurusan
                        </p>
                    </div>
                </div>

                <!-- Modul Khusus Guru -->
                <div
                    v-if="hasRole('guru')"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                >
                    <div
                        class="bg-emerald-500 text-white p-4 rounded-lg shadow"
                    >
                        <h3 class="font-bold text-lg">Jurnal Mengajar</h3>
                        <p class="text-sm mt-1">Isi presensi & materi harian</p>
                    </div>
                    <div
                        class="bg-emerald-600 text-white p-4 rounded-lg shadow"
                    >
                        <h3 class="font-bold text-lg">Input Nilai</h3>
                        <p class="text-sm mt-1">Kelola nilai asesmen siswa</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
