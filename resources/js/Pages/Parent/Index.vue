<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    children: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Pantauan Anak" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Pantauan Anak</h2>
                <p class="mt-1 text-sm text-gray-500">Ringkasan kehadiran dan perkembangan akademik anak.</p>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <section v-for="child in children" :key="child.id" class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ child.nama_lengkap }}</h3>
                            <p class="text-sm text-gray-500">NISN {{ child.nisn }} · {{ child.jurusan || 'Jurusan belum ditentukan' }} · {{ child.rombel || 'Rombel belum ditentukan' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
                        <div class="rounded-md bg-gray-50 p-4"><p class="text-xs text-gray-500">Total nilai</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ child.grades_count }}</p></div>
                        <div class="rounded-md bg-green-50 p-4"><p class="text-xs text-green-700">Hadir</p><p class="mt-1 text-2xl font-bold text-green-800">{{ child.attendance.hadir }}</p></div>
                        <div class="rounded-md bg-yellow-50 p-4"><p class="text-xs text-yellow-700">Sakit</p><p class="mt-1 text-2xl font-bold text-yellow-800">{{ child.attendance.sakit }}</p></div>
                        <div class="rounded-md bg-blue-50 p-4"><p class="text-xs text-blue-700">Izin</p><p class="mt-1 text-2xl font-bold text-blue-800">{{ child.attendance.izin }}</p></div>
                        <div class="rounded-md bg-red-50 p-4"><p class="text-xs text-red-700">Alpa</p><p class="mt-1 text-2xl font-bold text-red-800">{{ child.attendance.alpa }}</p></div>
                    </div>
                </section>

                <p v-if="!children.length" class="rounded-lg bg-white p-6 text-sm text-gray-500 shadow-sm">Belum ada siswa yang terhubung ke akun orang tua ini.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>