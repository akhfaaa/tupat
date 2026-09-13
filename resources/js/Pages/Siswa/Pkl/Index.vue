<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    pkls: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    tanggal: '',
    kegiatan: '',
    hasil: '',
});
const attendanceForm = useForm({
    attendance_date: new Date().toISOString().slice(0, 10),
    latitude: '',
    longitude: '',
    accuracy_meters: '',
});
const attendanceMessage = ref('');

const submit = (pklId) => {
    form.post(route('siswa.pkl.logbook.store', pklId), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const submitAttendance = (pklId) => {
    attendanceMessage.value = '';
    if (! navigator.geolocation) {
        attendanceMessage.value = 'Browser tidak mendukung geolocation.';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            attendanceForm.latitude = position.coords.latitude;
            attendanceForm.longitude = position.coords.longitude;
            attendanceForm.accuracy_meters = position.coords.accuracy;
            attendanceForm.post(route('pkl.attendance.store', pklId), {
                preserveScroll: true,
                onSuccess: () => {
                    attendanceMessage.value = 'Presensi lokasi berhasil dikirim.';
                    attendanceForm.reset('latitude', 'longitude', 'accuracy_meters');
                },
            });
        },
        (error) => {
            attendanceMessage.value = error.code === 1
                ? 'Izin lokasi ditolak. Aktifkan lokasi untuk melakukan presensi.'
                : 'Lokasi tidak dapat dibaca. Coba lagi.';
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 },
    );
};
</script>

<template>
    <Head title="PKL Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">PKL dan Logbook</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <section v-for="pkl in pkls" :key="pkl.id" class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ pkl.nama_perusahaan }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ pkl.divisi_pekerjaan || 'Penempatan PKL' }} · {{ pkl.status }}
                            </p>
                            <p v-if="pkl.mitra_dudi" class="text-sm text-gray-500">Mitra: {{ pkl.mitra_dudi.nama_perusahaan }}</p>
                        </div>
                        <p class="text-sm text-gray-500">{{ pkl.tanggal_mulai }} sampai {{ pkl.tanggal_selesai }}</p>
                    </div>

                    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_20rem]">
                        <div>
                            <h4 class="font-medium text-gray-900">Riwayat logbook</h4>
                            <div v-if="pkl.logbooks.length" class="mt-3 divide-y divide-gray-200 border rounded-lg">
                                <article v-for="logbook in pkl.logbooks" :key="logbook.id" class="p-4">
                                    <div class="flex justify-between gap-4 text-sm">
                                        <time class="font-medium text-gray-900">{{ logbook.tanggal }}</time>
                                        <span class="capitalize text-gray-500">{{ logbook.status_verifikasi }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-700">{{ logbook.kegiatan }}</p>
                                    <p v-if="logbook.hasil" class="mt-1 text-sm text-gray-500">{{ logbook.hasil }}</p>
                                </article>
                            </div>
                            <p v-else class="mt-3 text-sm text-gray-500">Belum ada logbook.</p>
                        </div>

                        <form class="space-y-3" @submit.prevent="submit(pkl.id)">
                            <h4 class="font-medium text-gray-900">Tambah aktivitas</h4>
                            <input v-model="form.tanggal" type="date" class="w-full rounded-md border-gray-300 text-sm" required>
                            <textarea v-model="form.kegiatan" class="w-full rounded-md border-gray-300 text-sm" rows="4" placeholder="Kegiatan hari ini" required />
                            <textarea v-model="form.hasil" class="w-full rounded-md border-gray-300 text-sm" rows="3" placeholder="Hasil atau catatan" />
                            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="form.processing">
                                Kirim logbook
                            </button>
                        </form>
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">Presensi lokasi PKL</h4>
                                <p class="mt-1 text-sm text-gray-500">Kirim titik lokasi saat hadir di tempat praktik.</p>
                            </div>
                            <button type="button" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="attendanceForm.processing" @click="submitAttendance(pkl.id)">
                                {{ attendanceForm.processing ? 'Membaca lokasi...' : 'Kirim presensi lokasi' }}
                            </button>
                        </div>
                        <p v-if="attendanceMessage" class="mt-2 text-sm text-gray-600">{{ attendanceMessage }}</p>
                    </div>
                </section>

                <p v-if="!pkls.length" class="rounded-lg bg-white p-6 text-sm text-gray-500 shadow-sm">Belum ada penempatan PKL aktif.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>