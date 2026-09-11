<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-[#f5f5f7]/80 backdrop-blur-md border-b border-black/[0.06] transition duration-300">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-8">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-bold tracking-tighter text-xl text-gray-900 hover:opacity-80 transition">
                        SIAKAD<span class="text-blue-600">.</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:flex items-center text-sm font-medium text-gray-600">
                    <a href="{{ route('dashboard') }}" class="hover:text-black transition {{ request()->routeIs('dashboard') ? 'text-black font-semibold' : '' }}">
                        Dashboard
                    </a>

                    @if(Auth::user()->role === 'tu')
                    <a href="{{ route('tu.siswa.index') }}" class="hover:text-black transition">Siswa</a>
                    <a href="{{ route('tu.guru.index') }}" class="hover:text-black transition">Guru</a>
                    <a href="{{ route('tu.rombel.index') }}" class="hover:text-black transition">Rombel</a>
                    <a href="{{ route('tu.mapel.index') }}" class="hover:text-black transition">Mapel</a>
                    @elseif(Auth::user()->role === 'guru')
                    <a href="{{ route('guru.jurnal.index') }}" class="hover:text-black transition">Jurnal</a>
                    <a href="{{ route('guru.nilai.index') }}" class="hover:text-black transition">Nilai</a>
                    @elseif(Auth::user()->role === 'siswa')
                    <a href="{{ route('siswa.raport.index') }}" class="hover:text-black transition">Rapor</a>
                    <a href="{{ route('siswa.absensi.index') }}" class="hover:text-black transition">Absensi</a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 bg-white/60 hover:bg-white text-xs font-semibold rounded-full border border-black/[0.05] shadow-sm text-gray-700 hover:text-black focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Akun') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>