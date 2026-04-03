{{-- Sidebar: secções, branding centralizado, copyright dinâmico. Requer Alpine no #authenticated-shell. --}}
@php
    $u = auth()->user();
    $logoHrefOnline = route('dashboard');
    $logoHrefOffline = $u->isAgenteEndemias()
        ? route('agente.visitas.index')
        : ($u->isAgenteSaude() ? route('saude.visitas.index') : route('gestor.visitas.index'));
    $yearEnd = (int) now()->format('Y');
    $copyrightYears = $yearEnd <= 2025 ? '2025' : '2025–'.$yearEnd;
@endphp

<aside id="app-sidebar" role="navigation" aria-label="{{ __('Menu principal') }}"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-800/80 bg-slate-950 text-slate-200 shadow-2xl transition-transform duration-200 ease-out lg:static lg:z-30 lg:w-60 lg:shrink-0 lg:shadow-none"
       x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <div class="relative flex h-[4.5rem] shrink-0 items-center justify-center border-b border-slate-800/80 px-4">
        <a :href="online ? @js($logoHrefOnline) : @js($logoHrefOffline)"
           @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="flex flex-col items-center gap-1.5 rounded-lg px-2 py-1 text-center outline-none ring-blue-500/40 focus-visible:ring-2">
            <img src="{{ asset('images/visitaai_rembg.png') }}"
                 alt="{{ config('app.name', 'Visita Aí') }}"
                 class="h-8 w-auto" width="32" height="32" />
            <span class="text-sm font-semibold leading-none tracking-tight text-white">Visita Aí</span>
        </a>
        <button type="button"
                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950 lg:hidden"
                @click="sidebarOpen = false"
                aria-label="{{ __('Fechar menu') }}">
            <x-heroicon-o-x-mark class="h-5 w-5" aria-hidden="true" />
        </button>
    </div>

    <nav class="flex-1 space-y-0.5 overflow-y-auto px-2 py-3" aria-label="{{ __('Navegação') }}">
        @if ($u->isGestor())
            <x-sidebar-nav-section :label="__('Geral')" :first="true" />
            <x-sidebar-link :href="route('dashboard')"
                            :active="request()->routeIs('*dashboard')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <x-heroicon-o-home class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                <span class="truncate">{{ __('Página Inicial') }}</span>
            </x-sidebar-link>
            <x-sidebar-link :href="route('gestor.visitas.index')"
                            :active="request()->routeIs('gestor.visitas.*')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                <span class="truncate">{{ __('Visitas') }}</span>
            </x-sidebar-link>

            <x-sidebar-nav-section :label="__('Cadastro e campo')" />
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.doencas.index')"
                                :active="request()->routeIs('gestor.doencas.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-beaker class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ __('Doenças') }}</span>
                </x-sidebar-link>
            </div>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.locais.index')"
                                :active="request()->routeIs('gestor.locais.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-map-pin class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ __('Locais') }}</span>
                </x-sidebar-link>
            </div>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.indicadores.ocupantes')"
                                :active="request()->routeIs('gestor.indicadores.ocupantes*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-chart-bar class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ config('visitaai_municipio.indicadores.menu', __('Indicadores municipais')) }}</span>
                </x-sidebar-link>
            </div>

            <x-sidebar-nav-section :label="__('Gestão municipal')" />
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.relatorios.index')"
                                :active="request()->routeIs('gestor.relatorios.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-document-text class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ __('Relatórios') }}</span>
                </x-sidebar-link>
            </div>
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('gestor.logs.index')"
                                :active="request()->routeIs('gestor.logs.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-clipboard-document-check class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ __('Auditoria') }}</span>
                </x-sidebar-link>
            </div>
        @elseif ($u->isAgenteEndemias())
            <x-sidebar-nav-section :label="__('Geral')" :first="true" />
            <x-sidebar-link :href="route('dashboard')"
                            :active="request()->routeIs('*dashboard')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <x-heroicon-o-home class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                <span class="truncate">{{ __('Página Inicial') }}</span>
            </x-sidebar-link>

            <x-sidebar-nav-section :label="__('Campo')" />
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('agente.doencas.index')"
                                :active="request()->routeIs('agente.doencas.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-beaker class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ __('Doenças') }}</span>
                </x-sidebar-link>
            </div>
            <x-sidebar-link :href="route('agente.locais.index')"
                            :active="request()->routeIs('agente.locais.*')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <x-heroicon-o-map-pin class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                <span class="truncate">{{ __('Locais') }}</span>
            </x-sidebar-link>
            <x-sidebar-link :href="route('agente.visitas.index')"
                            :active="request()->routeIs('agente.visitas.*')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                <span class="truncate">{{ __('Visitas') }}</span>
            </x-sidebar-link>

            <x-sidebar-nav-section :label="__('Sincronização')" />
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('agente.sincronizar')"
                                :active="request()->routeIs('agente.sincronizar')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-arrow-path class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ __('Sincronizar') }}</span>
                </x-sidebar-link>
            </div>
        @elseif ($u->isAgenteSaude())
            <x-sidebar-nav-section :label="__('Geral')" :first="true" />
            <x-sidebar-link :href="route('dashboard')"
                            :active="request()->routeIs('*dashboard')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <x-heroicon-o-home class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                <span class="truncate">{{ __('Página Inicial') }}</span>
            </x-sidebar-link>

            <x-sidebar-nav-section :label="__('LIRAa')" />
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('saude.doencas.index')"
                                :active="request()->routeIs('saude.doencas.*')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-beaker class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ __('Doenças') }}</span>
                </x-sidebar-link>
            </div>
            <x-sidebar-link :href="route('saude.visitas.index')"
                            :active="request()->routeIs('saude.visitas.*')"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                <span class="truncate">{{ __('Minhas Visitas') }}</span>
            </x-sidebar-link>

            <x-sidebar-nav-section :label="__('Sincronização')" />
            <div x-show="online" x-cloak>
                <x-sidebar-link :href="route('saude.sincronizar')"
                                :active="request()->routeIs('saude.sincronizar')"
                                @click="if (window.innerWidth < 1024) sidebarOpen = false">
                    <x-heroicon-o-arrow-path class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                    <span class="truncate">{{ __('Sincronizar') }}</span>
                </x-sidebar-link>
            </div>
        @endif
    </nav>

    <div class="shrink-0 border-t border-slate-800/80 px-3 py-3 text-center">
        <p class="text-[10px] leading-relaxed text-slate-500">
            © {{ $copyrightYears }} Visita Aí · Bitwise Technologies
        </p>
        <p class="mt-1 text-[10px] leading-relaxed text-slate-500/90">
            {{ __('Todos os direitos reservados.') }}
        </p>
    </div>
</aside>
