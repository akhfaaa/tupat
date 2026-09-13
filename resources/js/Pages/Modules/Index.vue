<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    schemes: Array,
    disciplineRecords: Array,
    inventory: Array,
    loans: Array,
    jobs: Array,
    alumni: Array,
    students: Array,
    subjects: Array,
    academicPeriods: Array,
});

const importForm = useForm({ file: null });
const disciplineForm = useForm({
    siswa_id: '',
    type: 'pelanggaran',
    points: 0,
    category: '',
    notes: '',
    occurred_at: '',
});
const assessmentForm = useForm({
    scheme_id: '',
    siswa_id: '',
    score: '',
    status: 'belum_dinilai',
    notes: '',
});
const loanForm = useForm({
    inventory_item_id: '',
    quantity: 1,
    borrowed_at: '',
    due_at: '',
});
const itemForm = useForm({
    code: '',
    name: '',
    category: '',
    location: '',
    quantity: 0,
    unit: 'unit',
    condition: 'baik',
});
const schemeForm = useForm({
    name: '',
    jurusan_id: '',
    certification_body: '',
    status: 'draft',
});
const jobForm = useForm({
    title: '',
    company: '',
    location: '',
    description: '',
    closing_date: '',
    status: 'aktif',
});
const reportForm = useForm({
    siswa_id: '',
    mata_pelajaran_id: '',
    tahun_ajaran_id: '',
    component: 'formatif',
    score: '',
    description: '',
});

const submitForm = (form, endpoint) => {
    form.post(route(endpoint), { preserveScroll: true, onSuccess: () => form.reset() });
};

const importDapodik = () => {
    importForm.post(route('dapodik.import.siswa'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => importForm.reset(),
    });
};
</script>

<template>
    <Head title="Modul SMK" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pusat Modul SMK</h2>
                <p class="mt-1 text-sm text-gray-500">UKK, BK, PKL, BKK, inventaris, dan sinkronisasi data.</p>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <p class="text-sm text-gray-500">Skema UKK/LSP</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ schemes.length }}</p>
                        <p class="mt-1 text-sm text-gray-500">Skema terdaftar</p>
                    </section>
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <p class="text-sm text-gray-500">Catatan BK</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ disciplineRecords.length }}</p>
                        <p class="mt-1 text-sm text-gray-500">Catatan terbaru</p>
                    </section>
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <p class="text-sm text-gray-500">Inventaris</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ inventory.length }}</p>
                        <p class="mt-1 text-sm text-gray-500">Jenis alat tercatat</p>
                    </section>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">Tambah Skema UKK/LSP</h3>
                        <form class="mt-4 grid gap-3 sm:grid-cols-2" @submit.prevent="submitForm(schemeForm, 'ukk.schemes.store')">
                            <input v-model="schemeForm.name" class="w-full rounded-md border-gray-300 text-sm" placeholder="Nama skema" required>
                            <input v-model="schemeForm.certification_body" class="w-full rounded-md border-gray-300 text-sm" placeholder="LSP/lembaga sertifikasi">
                            <select v-model="schemeForm.status" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="draft">Draft</option>
                                <option value="aktif">Aktif</option>
                                <option value="selesai">Selesai</option>
                            </select>
                            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" :disabled="schemeForm.processing">Simpan skema</button>
                        </form>
                    </section>

                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">Komponen Rapor Merdeka</h3>
                        <p class="mt-1 text-sm text-gray-500">Input capaian formatif, sumatif, praktik, P5, atau PKL.</p>
                        <form class="mt-4 grid gap-3 sm:grid-cols-2" @submit.prevent="submitForm(reportForm, 'report-components.store')">
                            <select v-model="reportForm.siswa_id" class="w-full rounded-md border-gray-300 text-sm" required><option value="">Pilih siswa</option><option v-for="student in students" :key="student.id" :value="student.id">{{ student.nama_lengkap }}</option></select>
                            <select v-model="reportForm.mata_pelajaran_id" class="w-full rounded-md border-gray-300 text-sm" required><option value="">Pilih mata pelajaran</option><option v-for="subject in subjects" :key="subject.id" :value="subject.id">{{ subject.nama_mapel }}</option></select>
                            <select v-model="reportForm.tahun_ajaran_id" class="w-full rounded-md border-gray-300 text-sm" required><option value="">Pilih periode</option><option v-for="period in academicPeriods" :key="period.id" :value="period.id">{{ period.tahun }} - {{ period.semester }}</option></select>
                            <select v-model="reportForm.component" class="w-full rounded-md border-gray-300 text-sm"><option value="formatif">Formatif</option><option value="sumatif">Sumatif</option><option value="praktik">Praktik</option><option value="projek_p5">Projek P5</option><option value="pkl">PKL</option></select>
                            <input v-model="reportForm.score" type="number" min="0" max="100" class="w-full rounded-md border-gray-300 text-sm" placeholder="Nilai 0-100" required>
                            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" :disabled="reportForm.processing">Simpan capaian</button>
                            <textarea v-model="reportForm.description" class="sm:col-span-2 w-full rounded-md border-gray-300 text-sm" rows="2" placeholder="Deskripsi capaian pembelajaran" />
                        </form>
                    </section>

                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">Tambah Lowongan BKK</h3>
                        <form class="mt-4 grid gap-3 sm:grid-cols-2" @submit.prevent="submitForm(jobForm, 'job-postings.store')">
                            <input v-model="jobForm.title" class="w-full rounded-md border-gray-300 text-sm" placeholder="Posisi pekerjaan" required>
                            <input v-model="jobForm.company" class="w-full rounded-md border-gray-300 text-sm" placeholder="Nama perusahaan" required>
                            <input v-model="jobForm.location" class="w-full rounded-md border-gray-300 text-sm" placeholder="Lokasi">
                            <input v-model="jobForm.closing_date" type="date" class="w-full rounded-md border-gray-300 text-sm">
                            <select v-model="jobForm.status" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="draft">Draft</option>
                                <option value="aktif">Aktif</option>
                                <option value="ditutup">Ditutup</option>
                            </select>
                            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" :disabled="jobForm.processing">Simpan lowongan</button>
                            <textarea v-model="jobForm.description" class="sm:col-span-2 w-full rounded-md border-gray-300 text-sm" rows="2" placeholder="Deskripsi pekerjaan" />
                        </form>
                    </section>

                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">Poin Kedisiplinan</h3>
                        <div v-if="disciplineRecords.length" class="mt-4 divide-y divide-gray-200">
                            <div v-for="record in disciplineRecords" :key="record.id" class="py-3 flex justify-between gap-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ record.nama_lengkap }}</p>
                                    <p class="text-sm text-gray-500">{{ record.category }} · {{ record.occurred_at }}</p>
                                </div>
                                <span :class="record.type === 'pelanggaran' ? 'text-red-600' : 'text-green-600'" class="font-semibold">
                                    {{ record.points > 0 ? '+' : '' }}{{ record.points }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-gray-500">Belum ada catatan.</p>
                    </section>

                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">BKK dan Tracer Alumni</h3>
                        <div v-if="jobs.length" class="mt-4 space-y-3">
                            <div v-for="job in jobs" :key="job.id" class="border rounded-md p-3">
                                <p class="font-medium text-gray-900">{{ job.title }}</p>
                                <p class="text-sm text-gray-500">{{ job.company }} · {{ job.location || 'Lokasi fleksibel' }}</p>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-gray-500">Belum ada lowongan aktif.</p>
                        <p class="mt-4 text-sm text-gray-500">{{ alumni.length }} profil alumni tercatat.</p>
                    </section>

                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">Tambah Alat Inventaris</h3>
                        <form class="mt-4 grid gap-3 sm:grid-cols-2" @submit.prevent="submitForm(itemForm, 'inventory-items.store')">
                            <input v-model="itemForm.code" class="w-full rounded-md border-gray-300 text-sm" placeholder="Kode alat" required>
                            <input v-model="itemForm.name" class="w-full rounded-md border-gray-300 text-sm" placeholder="Nama alat" required>
                            <input v-model="itemForm.category" class="w-full rounded-md border-gray-300 text-sm" placeholder="Kategori" required>
                            <input v-model="itemForm.location" class="w-full rounded-md border-gray-300 text-sm" placeholder="Lokasi">
                            <input v-model="itemForm.quantity" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm" placeholder="Jumlah" required>
                            <select v-model="itemForm.condition" class="w-full rounded-md border-gray-300 text-sm"><option value="baik">Baik</option><option value="perlu_perbaikan">Perlu perbaikan</option><option value="rusak">Rusak</option></select>
                            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white sm:col-span-2" :disabled="itemForm.processing">Simpan alat</button>
                        </form>
                    </section>
                </div>

                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900">Peminjaman Aktif</h3>
                    <div v-if="loans.length" class="mt-4 divide-y divide-gray-200">
                        <div v-for="loan in loans" :key="loan.id" class="flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between">
                            <div><p class="font-medium text-gray-900">{{ loan.item_name }} · {{ loan.quantity }} unit</p><p class="text-sm text-gray-500">{{ loan.borrower_name }} · kembali {{ loan.due_at || 'tidak ditentukan' }}</p></div>
                            <button type="button" class="rounded-md border border-emerald-600 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50" @click="useForm({}).patch(route('inventory-loans.return', loan.id), { preserveScroll: true })">Tandai dikembalikan</button>
                        </div>
                    </div>
                    <p v-else class="mt-3 text-sm text-gray-500">Tidak ada peminjaman aktif.</p>
                </section>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">Catatan BK</h3>
                        <form class="mt-4 space-y-3" @submit.prevent="submitForm(disciplineForm, 'discipline-records.store')">
                            <select v-model="disciplineForm.siswa_id" class="w-full rounded-md border-gray-300 text-sm" required>
                                <option value="">Pilih siswa</option>
                                <option v-for="student in students" :key="student.id" :value="student.id">{{ student.nama_lengkap }}</option>
                            </select>
                            <select v-model="disciplineForm.type" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="pelanggaran">Pelanggaran</option>
                                <option value="prestasi">Prestasi</option>
                            </select>
                            <input v-model="disciplineForm.category" class="w-full rounded-md border-gray-300 text-sm" placeholder="Kategori" required>
                            <input v-model="disciplineForm.points" type="number" class="w-full rounded-md border-gray-300 text-sm" placeholder="Poin" required>
                            <input v-model="disciplineForm.occurred_at" type="date" class="w-full rounded-md border-gray-300 text-sm" required>
                            <textarea v-model="disciplineForm.notes" class="w-full rounded-md border-gray-300 text-sm" rows="2" placeholder="Catatan" />
                            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" :disabled="disciplineForm.processing">Simpan catatan</button>
                        </form>
                    </section>

                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">Penilaian UKK</h3>
                        <form class="mt-4 space-y-3" @submit.prevent="submitForm(assessmentForm, 'ukk.assessments.store')">
                            <select v-model="assessmentForm.scheme_id" class="w-full rounded-md border-gray-300 text-sm" required>
                                <option value="">Pilih skema</option>
                                <option v-for="scheme in schemes" :key="scheme.id" :value="scheme.id">{{ scheme.name }}</option>
                            </select>
                            <select v-model="assessmentForm.siswa_id" class="w-full rounded-md border-gray-300 text-sm" required>
                                <option value="">Pilih siswa</option>
                                <option v-for="student in students" :key="student.id" :value="student.id">{{ student.nama_lengkap }}</option>
                            </select>
                            <input v-model="assessmentForm.score" type="number" min="0" max="100" class="w-full rounded-md border-gray-300 text-sm" placeholder="Nilai 0-100">
                            <select v-model="assessmentForm.status" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="belum_dinilai">Belum dinilai</option>
                                <option value="kompeten">Kompeten</option>
                                <option value="belum_kompeten">Belum kompeten</option>
                            </select>
                            <textarea v-model="assessmentForm.notes" class="w-full rounded-md border-gray-300 text-sm" rows="2" placeholder="Catatan assessor" />
                            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" :disabled="assessmentForm.processing">Simpan UKK</button>
                        </form>
                    </section>

                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-gray-900">Peminjaman Inventaris</h3>
                        <form class="mt-4 space-y-3" @submit.prevent="submitForm(loanForm, 'inventory-loans.store')">
                            <select v-model="loanForm.inventory_item_id" class="w-full rounded-md border-gray-300 text-sm" required>
                                <option value="">Pilih alat</option>
                                <option v-for="item in inventory" :key="item.id" :value="item.id">{{ item.name }} ({{ item.quantity }} {{ item.unit }})</option>
                            </select>
                            <input v-model="loanForm.quantity" type="number" min="1" class="w-full rounded-md border-gray-300 text-sm" placeholder="Jumlah" required>
                            <input v-model="loanForm.borrowed_at" type="date" class="w-full rounded-md border-gray-300 text-sm" required>
                            <input v-model="loanForm.due_at" type="date" class="w-full rounded-md border-gray-300 text-sm" placeholder="Batas pengembalian">
                            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" :disabled="loanForm.processing">Catat peminjaman</button>
                        </form>
                    </section>
                </div>

                <section class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">Sinkronisasi Dapodik</h3>
                            <p class="mt-1 text-sm text-gray-500">Import CSV hanya memperbarui siswa dengan NISN yang sudah terdaftar.</p>
                        </div>
                        <a :href="route('dapodik.export.siswa')" class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Export siswa CSV
                        </a>
                    </div>
                    <form class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center" @submit.prevent="importDapodik">
                        <input type="file" accept=".csv,.txt" class="block w-full text-sm text-gray-500" @input="importForm.file = $event.target.files[0]" required>
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="importForm.processing">
                            Import CSV
                        </button>
                    </form>
                    <p v-if="importForm.errors.file" class="mt-2 text-sm text-red-600">{{ importForm.errors.file }}</p>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
