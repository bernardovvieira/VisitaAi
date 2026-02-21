@auth
<nav x-data="{ open: false, online: typeof navigator !== 'undefined' ? navigator.onLine : true }"
     x-init="
       function updateOnline(e) { online = e.detail.online; }
       document.addEventListener('visita-connection-change', updateOnline);
       window.addEventListener('visita-connection-change', updateOnline);
       $nextTick(() => { online = typeof window.visitaConnectionOnline !== 'undefined' ? window.visitaConnectionOnline : (typeof navigator !== 'undefined' ? navigator.onLine : true); });
       setInterval(() => { if (typeof window.visitaConnectionOnline === 'boolean' && online !== window.visitaConnectionOnline) online = window.visitaConnectionOnline; }, 400);
     "
     x-effect="if (typeof window.visitaConnectionOnline === 'boolean') online = window.visitaConnectionOnline"
     class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo + links -->
            <div class="flex items-center min-w-0">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
                <div class="shrink-0 flex items-center leading-none h-full">
                    <a :href="online ? '{{ route('dashboard') }}' : '{{ Auth::user()->isAgenteEndemias() ? route('agente.visitas.index') : (Auth::user()->isAgenteSaude() ? route('saude.visitas.index') : route('gestor.visitas.index')) }}'"
                       class="flex flex-col items-center">
                        <img src="{{ asset('images/visitaai_rembg.png') }}"
                            alt="Visita Aí Logo"
                            class="h-12 w-auto mb-[-9px] p-0 m-0 leading-none" />
                        <span class="text-[10px] font-semibold text-gray-800 dark:text-gray-200 leading-tight m-0 p-0 mb-1" style="font-family: 'Poppins', sans-serif;">
                            Visita Aí
                        </span>
                    </a>
                </div>
                <div class="hidden sm:flex sm:items-center sm:gap-6 sm:-my-px sm:ml-8">
                    <span x-show="online" x-cloak class="inline-flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('*dashboard')">
                            {{ __('Página Inicial') }}
                        </x-nav-link>
                    </span>

                    @if(Auth::user()->isGestor())
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('gestor.doencas.index')" :active="request()->routeIs('gestor.doencas.*')">{{ __('Doenças') }}</x-nav-link></span>
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('gestor.locais.index')" :active="request()->routeIs('gestor.locais.*')">{{ __('Locais') }}</x-nav-link></span>
                        <x-nav-link :href="route('gestor.visitas.index')" :active="request()->routeIs('gestor.visitas.*')">{{ __('Visitas') }}</x-nav-link>
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('gestor.relatorios.index')" :active="request()->routeIs('gestor.relatorios.*')">{{ __('Relatórios') }}</x-nav-link></span>
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('gestor.users.index')" :active="request()->routeIs('gestor.users.*')">{{ __('Usuários') }}</x-nav-link></span>
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('gestor.logs.index')" :active="request()->routeIs('gestor.logs.*')">{{ __('Auditoria') }}</x-nav-link></span>
                    @elseif(Auth::user()->isAgenteEndemias())
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('agente.doencas.index')" :active="request()->routeIs('agente.doencas.*')">{{ __('Doenças') }}</x-nav-link></span>
                        <x-nav-link :href="route('agente.locais.index')" :active="request()->routeIs('agente.locais.*')">{{ __('Locais') }}</x-nav-link>
                        <x-nav-link :href="route('agente.visitas.index')" :active="request()->routeIs('agente.visitas.*')">{{ __('Visitas') }}</x-nav-link>
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('agente.sincronizar')" :active="request()->routeIs('agente.sincronizar')">{{ __('Sincronizar') }}</x-nav-link></span>
                    @elseif(Auth::user()->isAgenteSaude())
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('saude.doencas.index')" :active="request()->routeIs('saude.doencas.*')">{{ __('Doenças') }}</x-nav-link></span>
                        <x-nav-link :href="route('saude.visitas.index')" :active="request()->routeIs('saude.visitas.*')">{{ __('Minhas Visitas') }}</x-nav-link>
                        <span x-show="online" x-cloak class="inline-flex"><x-nav-link :href="route('saude.sincronizar')" :active="request()->routeIs('saude.sincronizar')">{{ __('Sincronizar') }}</x-nav-link></span>
                    @endif
                </div>
            </div>

            <!-- Status conexão + Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 gap-3 shrink-0">
                <span class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 shrink-0"
                      :title="online ? 'Conectado à internet' : 'Sem conexão — visitas podem ser guardadas no dispositivo'">
                    <span class="relative flex h-2 w-2" x-cloak>
                        <span x-show="online" class="absolute inline-flex h-full w-full rounded-full bg-green-500 ring-2 ring-green-500/30"></span>
                        <span x-show="!online" class="absolute inline-flex h-full w-full rounded-full bg-amber-500 dark:bg-amber-400 ring-2 ring-amber-500/30"></span>
                    </span>
                    <span x-text="online ? 'Conectado' : 'Offline'" class="hidden md:inline"></span>
                </span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium
                            rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800
                            hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition min-w-0 max-w-[200px] sm:max-w-[240px]">
                            <div class="text-left min-w-0 truncate">
                                <div class="truncate" title="{{ Auth::user()->use_nome }}">{{ Auth::user()->use_nome }}</div>
                                <div class="text-xs font-normal text-gray-400 dark:text-gray-500 truncate">
                                    {{ \App\Helpers\MsTerminologia::perfilLabelNav(Auth::user()->use_perfil) }}
                                </div>
                            </div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 
                                             0 111.414 1.414l-4 4a1 1 
                                             0 01-1.414 0l-4-4a1 1 
                                             0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div x-show="online" x-cloak>
                            <x-dropdown-link :href="route('profile.edit')" :class="request()->routeIs('profile.*') ? 'dropdown-link-active' : ''">
                                {{ __('Meu Perfil') }}
                            </x-dropdown-link>
                        </div>
                        <p x-show="!online" x-cloak class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">Opções desabilitadas ao estar desconectado.</p>
                        <div x-show="online" x-cloak>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Sair do Sistema') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Status conexão (mobile) + Hamburger -->
            <div class="-mr-2 flex items-center gap-2 sm:hidden">
                <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400" :title="online ? 'Conectado' : 'Offline'">
                    <span class="relative flex h-2 w-2" x-cloak>
                        <span x-show="online" class="absolute inline-flex h-full w-full rounded-full bg-green-500"></span>
                        <span x-show="!online" class="absolute inline-flex h-full w-full rounded-full bg-amber-500"></span>
                    </span>
                </span>
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500
                               hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900
                               focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                              class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <div x-show="online" x-cloak>
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('*dashboard')">
                    {{ __('Página Inicial') }}
                </x-responsive-nav-link>
            </div>

            @if(Auth::user()->isGestor())
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('gestor.doencas.index')" :active="request()->routeIs('gestor.doencas.*')">{{ __('Doenças') }}</x-responsive-nav-link></div>
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('gestor.locais.index')" :active="request()->routeIs('gestor.locais.*')">{{ __('Locais') }}</x-responsive-nav-link></div>
                <x-responsive-nav-link :href="route('gestor.visitas.index')" :active="request()->routeIs('gestor.visitas.*')">{{ __('Visitas') }}</x-responsive-nav-link>
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('gestor.relatorios.index')" :active="request()->routeIs('gestor.relatorios.*')">{{ __('Relatórios') }}</x-responsive-nav-link></div>
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('gestor.users.index')" :active="request()->routeIs('gestor.users.*')">{{ __('Usuários') }}</x-responsive-nav-link></div>
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('gestor.logs.index')" :active="request()->routeIs('gestor.logs.*')">{{ __('Auditoria') }}</x-responsive-nav-link></div>
            @elseif(Auth::user()->isAgenteEndemias())
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('agente.doencas.index')" :active="request()->routeIs('agente.doencas.*')">{{ __('Doenças') }}</x-responsive-nav-link></div>
                <x-responsive-nav-link :href="route('agente.locais.index')" :active="request()->routeIs('agente.locais.*')">{{ __('Locais') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('agente.visitas.index')" :active="request()->routeIs('agente.visitas.*')">{{ __('Visitas') }}</x-responsive-nav-link>
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('agente.sincronizar')" :active="request()->routeIs('agente.sincronizar')">{{ __('Sincronizar') }}</x-responsive-nav-link></div>
            @elseif(Auth::user()->isAgenteSaude())
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('saude.doencas.index')" :active="request()->routeIs('saude.doencas.*')">{{ __('Doenças') }}</x-responsive-nav-link></div>
                <x-responsive-nav-link :href="route('saude.visitas.index')" :active="request()->routeIs('saude.visitas.*')">{{ __('Minhas Visitas') }}</x-responsive-nav-link>
                <div x-show="online" x-cloak><x-responsive-nav-link :href="route('saude.sincronizar')" :active="request()->routeIs('saude.sincronizar')">{{ __('Sincronizar') }}</x-responsive-nav-link></div>
            @endif
        </div>

        <!-- Mobile Settings -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->use_nome }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ \App\Models\User::perfilLabel(Auth::user()->use_perfil) }}
                </div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Auth::user()->use_email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <div x-show="online" x-cloak>
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">{{ __('Meu Perfil') }}</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                               onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Sair do Sistema') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
                <p x-show="!online" x-cloak class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">Opções desabilitadas ao estar desconectado.</p>
            </div>
        </div>
    </div>
</nav>
@endauth