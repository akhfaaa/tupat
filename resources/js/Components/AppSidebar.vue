<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const roles = computed(() => {
    const value = user.value?.roles ?? [];
    return Array.isArray(value) ? value : Object.values(value);
});
const hasRole = (role) => roles.value.includes(role);
const canManageMasterData = computed(() => hasRole('super-admin') || hasRole('tu'));
const canTeach = computed(() => hasRole('super-admin') || hasRole('guru'));
const canAccessModules = computed(() => [
    'super-admin', 'tu', 'guru', 'guru-bk', 'wali-kelas', 'hubin', 'mentor-industri', 'bkk',
].some((role) => hasRole(role)));
const canAccessStudent = computed(() => hasRole('siswa'));
const canAccessParent = computed(() => hasRole('orang-tua'));

const linkClass = (pattern) => route().current(pattern)
    ? 'flex items-center rounded-lg bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700'
    : 'flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50 hover:text-gray-900';
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-gray-200 bg-white lg:flex lg:flex-col">
        <div class="flex h-16 items-center border-b border-gray-100 px-6">
            <Link :href="route('dashboard')" class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">S</span>
                <span class="text-lg font-bold tracking-tight text-gray-900">SIAKAD<span class="text-indigo-600">.</span></span>
            </Link>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-5">
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">Navigasi</p>
            <nav class="mt-2 space-y-1">
                <Link :href="route('dashboard')" :class="linkClass('dashboard')">Dashboard</Link>
            </nav>

            <template v-if="canManageMasterData">
                <p class="mt-7 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">Master Data</p>
                <nav class="mt-2 space-y-1">
                    <Link :href="route('jurusan.index')" :class="linkClass('jurusan.*')">Jurusan</Link>
                    <Link :href="route('tahun-ajaran.index')" :class="linkClass('tahun-ajaran.*')">Tahun Ajaran</Link>
                    <Link :href="route('guru.index')" :class="linkClass('guru.*')">Data Guru</Link>
                    <Link :href="route('rombel.index')" :class="linkClass('rombel.*')">Rombel</Link>
                    <Link :href="route('siswa.index')" :class="linkClass('siswa.*')">Data Siswa</Link>
                    <Link :href="route('mata-pelajaran.index')" :class="linkClass('mata-pelajaran.*')">Mata Pelajaran</Link>
                    <Link :href="route('jadwal.index')" :class="linkClass('jadwal.*')">Jadwal Pelajaran</Link>
                    <Link :href="route('pkl.index')" :class="linkClass('pkl.*')">Data PKL</Link>
                </nav>
            </template>

            <template v-if="canTeach">
                <p class="mt-7 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">Mengajar</p>
                <nav class="mt-2 space-y-1">
                    <Link :href="route('jurnal.index')" :class="linkClass('jurnal.*')">Jurnal & Presensi</Link>
                    <Link :href="route('penilaian.index')" :class="linkClass('penilaian.*')">Input Nilai</Link>
                </nav>
            </template>

            <template v-if="canAccessModules">
                <p class="mt-7 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">Operasional SMK</p>
                <nav class="mt-2 space-y-1">
                    <Link :href="route('modules.smk')" :class="linkClass('modules.smk')">Modul SMK</Link>
                </nav>
            </template>

            <template v-if="canAccessStudent">
                <p class="mt-7 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">Area Siswa</p>
                <nav class="mt-2 space-y-1">
                    <Link :href="route('siswa.rapor')" :class="linkClass('siswa.rapor')">E-Rapor Saya</Link>
                    <Link :href="route('siswa.pkl.index')" :class="linkClass('siswa.pkl.*')">PKL & Logbook</Link>
                </nav>
            </template>

            <template v-if="canAccessParent">
                <p class="mt-7 px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">Area Orang Tua</p>
                <nav class="mt-2 space-y-1">
                    <Link :href="route('parent.dashboard')" :class="linkClass('parent.dashboard')">Pantauan Anak</Link>
                </nav>
            </template>
        </div>

        <div class="border-t border-gray-100 p-4">
            <Link :href="route('profile.edit')" class="block rounded-lg px-3 py-2 hover:bg-gray-50">
                <p class="truncate text-sm font-semibold text-gray-900">{{ user.name }}</p>
                <p class="truncate text-xs text-gray-500">{{ user.email }}</p>
            </Link>
        </div>
    </aside>
</template>
