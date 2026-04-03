{{-- Barra superior: utilizador, estado da ligação, tema, sair — requer Alpine no #authenticated-shell --}}
<header class="sticky top-0 z-20 flex h-14 shrink-0 items-center justify-between gap-3 border-b border-gray-200 bg-white/95 px-3 shadow-sm backdrop-blur-md dark:border-gray-700 dark:bg-gray-900/95 sm:px-4">
    <div class="flex min-w-0 items-center gap-2">
        <button type="button"
                class="inline-flex items-center justify-center rounded-lg p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/60 focus-visible:ring-offset-2 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus-visible:ring-offset-gray-900 lg:hidden"
                @click="sidebarOpen = true"
                aria-expanded="false"
                x-bind:aria-expanded="sidebarOpen"
                aria-controls="app-sidebar"
                aria-label="{{ __('Abrir menu') }}">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="hidden min-w-0 sm:block">
            <p class="truncate text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Área autenticada') }}</p>
            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ config('app.name', 'Visita Aí') }}</p>
        </div>
    </div>

    <div class="flex shrink-0 items-center gap-1.5 sm:gap-3">
        <span class="hidden items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 md:inline-flex"
              :title="online ? 'Conectado à internet' : '{{ __('Sem conexão') }}'"
              x-cloak>
            <span class="relative flex h-2 w-2">
                <span x-show="online" class="absolute inline-flex h-full w-full rounded-full bg-emerald-500 ring-2 ring-emerald-500/30"></span>
                <span x-show="!online" class="absolute inline-flex h-full w-full rounded-full bg-amber-500 ring-2 ring-amber-500/30"></span>
            </span>
            <span x-text="online ? '{{ __('Conectado') }}' : '{{ __('Offline') }}'"></span>
        </span>
        <span class="inline-flex items-center md:hidden" :title="online ? 'Online' : 'Offline'" x-cloak>
            <span class="relative flex h-2 w-2">
                <span x-show="online" class="absolute inline-flex h-full w-full rounded-full bg-emerald-500"></span>
                <span x-show="!online" class="absolute inline-flex h-full w-full rounded-full bg-amber-500"></span>
            </span>
        </span>

        <x-theme-toggle :floating="false" />

        <x-dropdown align="right" width="w-56">
            <x-slot name="trigger">
                <button type="button"
                        class="inline-flex max-w-[200px] items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 py-1.5 pl-2 pr-2 text-left text-sm font-medium text-gray-800 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700 sm:max-w-[240px] sm:pl-3 sm:pr-2">
                    <span class="hidden h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white sm:inline-flex"
                          aria-hidden="true">
                        {{ strtoupper(mb_substr(auth()->user()->use_nome, 0, 1)) }}
                    </span>
                    <span class="min-w-0 flex-1 truncate">
                        <span class="block truncate" title="{{ auth()->user()->use_nome }}">{{ auth()->user()->use_nome }}</span>
                        <span class="block truncate text-xs font-normal text-gray-500 dark:text-gray-400">
                            {{ \App\Helpers\MsTerminologia::perfilLabelNav(auth()->user()->use_perfil) }}
                        </span>
                    </span>
                    <svg class="h-4 w-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-600 sm:hidden">
                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ auth()->user()->use_nome }}</p>
                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->use_email }}</p>
                </div>
                <div x-show="online" x-cloak>
                    <x-dropdown-link :href="route('profile.edit')" :class="request()->routeIs('profile.*') ? 'dropdown-link-active' : ''">
                        {{ __('Meu Perfil') }}
                    </x-dropdown-link>
                </div>
                <p x-show="!online" x-cloak class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Opções desabilitadas ao estar desconectado.') }}</p>
                <div x-show="online" x-cloak>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Sair do Sistema') }}
                        </x-dropdown-link>
                    </form>
                </div>
            </x-slot>
        </x-dropdown>
    </div>
</header>
