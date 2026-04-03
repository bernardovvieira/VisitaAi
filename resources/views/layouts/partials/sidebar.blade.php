{{-- Sidebar ERP: requer Alpine no ancestral (#authenticated-shell): sidebarOpen, online --}}
@php
    $u = auth()->user();
    $logoHrefOnline = route('dashboard');
    $logoHrefOffline = $u->isAgenteEndemias()
        ? route('agente.visitas.index')
        : ($u->isAgenteSaude() ? route('saude.visitas.index') : route('gestor.visitas.index'));
@endphp

<aside id="app-sidebar" role="navigation" aria-label="{{ __('Menu principal') }}"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-800/80 bg-slate-950 text-slate-200 shadow-2xl transition-transform duration-200 ease-out lg:static lg:z-30 lg:w-60 lg:shrink-0 lg:shadow-none"
       x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-slate-800/80 px-4">
        <a :href="online ? @js($logoHrefOnline) : @js($logoHrefOffline)"
           @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="flex min-w-0 flex-1 items-center gap-2 rounded-lg outline-none ring-emerald-500/40 focus-visible:ring-2">
            <img src="{{ asset('images/visitaai_rembg.png') }}"
                 alt=""
                 class="h-9 w-auto shrink-0" width="36" height="36" />
            <span class="truncate text-sm font-semibold tracking-tight text-white">{{ config('app.name', 'Visita Aí') }}</span>
        </a>
        <button type="button"
                class="rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950 lg:hidden"
                @click="sidebarOpen = false"
                aria-label="{{ __('Fechar menu') }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-2 py-4" aria-label="{{ __('Navegação') }}">
        <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('Acesso rápido') }}</p>

        <x-sidebar-link :href="route('dashboard')"
                        :active="request()->routeIs('*dashboard')"
                        @click="if (window.innerWidth < 1024) sidebarOpen = false">
            <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="truncate">{{ __('Página Inicial') }}</span>
        </x-sidebar-link>

        @if ($u->isGestor())
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.pendentes')"
                                :active="request()->routeIs('gestor.pendentes')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="truncate">{{ __('Cadastros pendentes') }}</span>
                </x-sidebar-link>
            </div>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.doencas.index')"
                                :active="request()->routeIs('gestor.doencas.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <span class="truncate">{{ __('Doenças') }}</span>
                </x-sidebar-link>
            </div>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.locais.index')"
                                :active="request()->routeIs('gestor.locais.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="truncate">{{ __('Locais') }}</span>
                </x-sidebar-link>
            </div>
            <x-sidebar-link :href="route('gestor.visitas.index')"
                            :active="request()->routeIs('gestor.visitas.*')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="truncate">{{ __('Visitas') }}</span>
            </x-sidebar-link>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.indicadores.ocupantes')"
                                :active="request()->routeIs('gestor.indicadores.ocupantes*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="truncate">{{ config('visitaai_municipio.indicadores.menu', __('Indicadores municipais')) }}</span>
                </x-sidebar-link>
            </div>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.relatorios.index')"
                                :active="request()->routeIs('gestor.relatorios.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="truncate">{{ __('Relatórios') }}</span>
                </x-sidebar-link>
            </div>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.users.index')"
                                :active="request()->routeIs('gestor.users.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="truncate">{{ __('Usuários') }}</span>
                </x-sidebar-link>
            </div>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.logs.index')"
                                :active="request()->routeIs('gestor.logs.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="truncate">{{ __('Auditoria') }}</span>
                </x-sidebar-link>
            </div>
        @elseif ($u->isAgenteEndemias())
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('agente.doencas.index')"
                                :active="request()->routeIs('agente.doencas.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <span class="truncate">{{ __('Doenças') }}</span>
                </x-sidebar-link>
            </div>
            <x-sidebar-link :href="route('agente.locais.index')"
                            :active="request()->routeIs('agente.locais.*')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ __('Locais') }}</span>
            </x-sidebar-link>
            <x-sidebar-link :href="route('agente.visitas.index')"
                            :active="request()->routeIs('agente.visitas.*')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="truncate">{{ __('Visitas') }}</span>
            </x-sidebar-link>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('agente.sincronizar')"
                                :active="request()->routeIs('agente.sincronizar')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="truncate">{{ __('Sincronizar') }}</span>
                </x-sidebar-link>
            </div>
        @elseif ($u->isAgenteSaude())
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('saude.doencas.index')"
                                :active="request()->routeIs('saude.doencas.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <span class="truncate">{{ __('Doenças') }}</span>
                </x-sidebar-link>
            </div>
            <x-sidebar-link :href="route('saude.visitas.index')"
                            :active="request()->routeIs('saude.visitas.*')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="truncate">{{ __('Minhas Visitas') }}</span>
            </x-sidebar-link>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('saude.sincronizar')"
                                :active="request()->routeIs('saude.sincronizar')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="truncate">{{ __('Sincronizar') }}</span>
                </x-sidebar-link>
            </div>
        @endif
    </nav>

    <div class="shrink-0 border-t border-slate-800/80 px-4 py-3">
        <p class="text-[10px] leading-tight text-slate-500">
            {{ __('Vigilância entomológica e controle vetorial') }}
        </p>
    </div>
</aside>
