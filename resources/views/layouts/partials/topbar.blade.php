{{-- Barra superior: menu (mobile), indicador de ligação, tema, conta. --}}
<header class="sticky top-0 z-20 flex h-12 shrink-0 items-center justify-end gap-1 border-b border-slate-200/90 bg-white/95 px-2 backdrop-blur-md dark:border-slate-700 dark:bg-slate-900/95 sm:h-14 sm:justify-between sm:px-4">
    <div class="flex min-w-0 flex-1 items-center gap-0.5 sm:flex-none sm:gap-1">
        <button type="button"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:text-slate-300 dark:hover:bg-slate-800 dark:focus-visible:ring-offset-slate-900 lg:hidden"
                @click="sidebarOpen = true"
                x-bind:aria-expanded="sidebarOpen"
                aria-controls="app-sidebar"
                aria-label="{{ __('Abrir menu') }}">
            <x-heroicon-o-bars-3 class="h-6 w-6" aria-hidden="true" />
        </button>
        <button type="button"
                class="hidden items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:text-slate-300 dark:hover:bg-slate-800 dark:focus-visible:ring-offset-slate-900 lg:inline-flex"
                @click="sidebarDesktop = sidebarDesktop === 'expanded' ? 'collapsed' : 'expanded'"
                aria-controls="app-sidebar"
                x-bind:aria-expanded="sidebarDesktop === 'expanded'"
                x-bind:aria-label="sidebarDesktop === 'expanded' ? @js(__('Recolher menu (só ícones)')) : @js(__('Expandir menu'))"
                x-bind:title="sidebarDesktop === 'expanded' ? @js(__('Recolher menu (só ícones)')) : @js(__('Expandir menu'))">
            <x-heroicon-o-chevron-double-left class="h-6 w-6 shrink-0 transition-transform duration-200" x-bind:class="{ 'rotate-180': sidebarDesktop === 'collapsed' }" aria-hidden="true" />
        </button>
    </div>

    <div class="flex shrink-0 items-center gap-0.5 sm:gap-2">
        <span class="inline-flex p-2"
              :title="online ? '{{ __('Conectado à internet') }}' : '{{ __('Sem conexão à internet') }}'"
              x-cloak>
            <span class="relative flex h-2.5 w-2.5">
                <span x-show="online" class="absolute inline-flex h-full w-full rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                <span x-show="!online" class="absolute inline-flex h-full w-full rounded-full bg-amber-500"></span>
            </span>
        </span>

        <x-theme-toggle :floating="false" />

        <x-dropdown align="right" width="w-56">
            <x-slot name="trigger">
                <button type="button"
                        class="inline-flex max-w-[min(100vw-8rem,14rem)] items-center gap-2 rounded-lg py-1.5 pl-1 pr-1.5 text-left transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 dark:hover:bg-slate-800 sm:max-w-[16rem]">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-semibold text-white dark:bg-blue-500"
                          aria-hidden="true">
                        {{ strtoupper(mb_substr(auth()->user()->use_nome, 0, 1)) }}
                    </span>
                    <span class="min-w-0 truncate text-sm font-medium text-slate-900 dark:text-slate-100" title="{{ auth()->user()->use_nome }}">
                        {{ auth()->user()->use_nome }}
                    </span>
                    <x-heroicon-o-chevron-down class="h-4 w-4 shrink-0 text-slate-400" aria-hidden="true" />
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="border-b border-slate-100 px-4 py-3 dark:border-slate-600 sm:hidden">
                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ auth()->user()->use_nome }}</p>
                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->use_email }}</p>
                    <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ \App\Helpers\MsTerminologia::perfilLabelNav(auth()->user()->use_perfil) }}</p>
                </div>
                <div x-show="online" x-cloak>
                    <x-dropdown-link :href="route('profile.edit')" :class="request()->routeIs('profile.*') ? 'dropdown-link-active' : ''">
                        {{ __('Meu Perfil') }}
                    </x-dropdown-link>
                </div>
                <p x-show="!online" x-cloak class="px-4 py-2 text-xs text-slate-500 dark:text-slate-400">{{ __('Opções desabilitadas ao estar desconectado.') }}</p>
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
