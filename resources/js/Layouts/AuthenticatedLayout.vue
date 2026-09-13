<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import AppSidebar from '@/Components/AppSidebar.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const page = usePage();
const user = computed(() => page.props.auth.user);
const roles = computed(() => {
    const value = user.value?.roles ?? [];
    return Array.isArray(value) ? value : Object.values(value);
});
const hasRole = (roleName) => {
    return roles.value.includes(roleName);
};
const canManageMasterData = computed(() => hasRole('super-admin') || hasRole('tu'));
const canAccessTeaching = computed(() => hasRole('super-admin') || hasRole('guru'));
const canAccessSmkModules = computed(() => [
    'super-admin', 'tu', 'guru', 'guru-bk', 'wali-kelas', 'hubin', 'mentor-industri', 'bkk',
].some((role) => hasRole(role)));
const canAccessStudent = computed(() => hasRole('siswa'));
</script>

<template>
    <div>
        <AppSidebar />
        <div class="min-h-screen bg-gray-100 lg:pl-72">
            <nav class="sticky top-0 z-50 border-b border-gray-200/80 bg-white/95 shadow-sm backdrop-blur lg:hidden">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex min-h-16 items-center justify-between gap-4">
                        <div class="flex min-w-0 flex-1 items-center">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')" class="rounded-md p-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden min-w-0 flex-1 items-center gap-1 overflow-x-auto sm:-my-px sm:ms-8 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>

                                <template v-if="canManageMasterData">
                                    <div class="ml-2 flex shrink-0 items-center gap-1 border-l border-gray-200 pl-2">
                                        <span class="px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Master Data</span>
                                        <NavLink :href="route('jurusan.index')" :active="route().current('jurusan.*')">
                                            Jurusan
                                        </NavLink>
                                        <NavLink :href="route('tahun-ajaran.index')" :active="route().current('tahun-ajaran.*')">
                                            Tahun Ajaran
                                        </NavLink>
                                        <NavLink :href="route('guru.index')" :active="route().current('guru.*')">Guru</NavLink>
                                        <NavLink :href="route('rombel.index')" :active="route().current('rombel.*')">Rombel</NavLink>
                                        <NavLink :href="route('siswa.index')" :active="route().current('siswa.*')">Siswa</NavLink>
                                        <NavLink :href="route('mata-pelajaran.index')" :active="route().current('mata-pelajaran.*')">Mapel</NavLink>
                                        <NavLink :href="route('jadwal.index')" :active="route().current('jadwal.*')">Jadwal</NavLink>
                                        <NavLink :href="route('pkl.index')" :active="route().current('pkl.*')">PKL</NavLink>
                                    </div>
                                </template>

                                <template v-if="canAccessTeaching">
                                    <div class="ml-2 flex shrink-0 items-center gap-1 border-l border-gray-200 pl-2">
                                        <span class="px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Mengajar</span>
                                    <NavLink :href="route('jurnal.index')" :active="route().current('jurnal.*')">
                                        Jurnal & Presensi
                                    </NavLink>
                                    <NavLink :href="route('penilaian.index')" :active="route().current('penilaian.*')">
                                        Input Nilai (E-Rapor)
                                    </NavLink>
                                    </div>
                                </template>

                                <NavLink v-if="canAccessSmkModules"
                                    :href="route('modules.smk')" :active="route().current('modules.smk')">
                                    Modul SMK
                                </NavLink>

                                <template v-if="canAccessStudent">
                                    <NavLink :href="route('siswa.rapor')" :active="route().current('siswa.rapor')">
                                        E-Rapor Saya
                                    </NavLink>
                                    <NavLink :href="route('siswa.pkl.index')" :active="route().current('siswa.pkl.*')">
                                        PKL dan Logbook
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                {{ user.name }}

                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>

                        <template v-if="canManageMasterData">
                            <ResponsiveNavLink :href="route('jurusan.index')" :active="route().current('jurusan.*')">
                                Master Data (Jurusan)</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('tahun-ajaran.index')"
                                :active="route().current('tahun-ajaran.*')">Tahun Ajaran</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('guru.index')" :active="route().current('guru.*')">Data Guru
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('rombel.index')" :active="route().current('rombel.*')">Data
                                Kelas/Rombel</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('siswa.index')" :active="route().current('siswa.*')">Data
                                Siswa</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('mata-pelajaran.index')"
                                :active="route().current('mata-pelajaran.*')">Mata Pelajaran</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('jadwal.index')" :active="route().current('jadwal.*')">
                                Jadwal Pelajaran</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('pkl.index')" :active="route().current('pkl.*')">Data PKL
                            </ResponsiveNavLink>
                        </template>

                        <template v-if="canAccessTeaching">
                            <ResponsiveNavLink :href="route('jurnal.index')" :active="route().current('jurnal.*')">
                                Jurnal & Presensi</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('penilaian.index')"
                                :active="route().current('penilaian.*')">Input Nilai (E-Rapor)</ResponsiveNavLink>
                        </template>

                        <ResponsiveNavLink v-if="canAccessSmkModules"
                            :href="route('modules.smk')" :active="route().current('modules.smk')">
                            Modul SMK
                        </ResponsiveNavLink>

                        <template v-if="hasRole('siswa')">
                            <ResponsiveNavLink :href="route('siswa.rapor')" :active="route().current('siswa.rapor')">
                                E-Rapor Saya</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('siswa.pkl.index')" :active="route().current('siswa.pkl.*')">
                                PKL dan Logbook</ResponsiveNavLink>
                        </template>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">{{ user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
                        </div>

                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>