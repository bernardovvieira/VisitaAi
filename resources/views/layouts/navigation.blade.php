@auth
<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo + Dashboard -->
            <div class="flex">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
                <div class="shrink-0 flex items-center leading-none h-full">
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center">
                        <img src="{{ asset('images/visitaai_rembg.png') }}"
                            alt="Visita Aí Logo"
                            class="h-12 w-auto mb-[-9px] p-0 m-0 leading-none" />
                        <span class="text-[10px] font-semibold text-gray-800 dark:text-gray-200 leading-tight m-0 p-0 mb-1" style="font-family: 'Poppins', sans-serif;">
                            Visita Aí
                        </span>
                    </a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('*dashboard')">
                        {{ __('Página Inicial') }}
                    </x-nav-link>

                    @if(Auth::user()->isGestor())
                        <x-nav-link :href="route('gestor.doencas.index')" :active="request()->routeIs('gestor.doencas.*')">
                            {{ __('Doenças') }}
                        </x-nav-link>
                        <x-nav-link :href="route('gestor.locais.index')" :active="request()->routeIs('gestor.locais.*')">
                            {{ __('Locais') }}
                        </x-nav-link>
                        <x-nav-link :href="route('gestor.visitas.index')" :active="request()->routeIs('gestor.visitas.*')">
                            {{ __('Visitas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('gestor.relatorios.index')" :active="request()->routeIs('gestor.relatorios.*')">
                            {{ __('Relatórios') }}
                        </x-nav-link>
                        <x-nav-link :href="route('gestor.users.index')" :active="request()->routeIs('gestor.users.*')">
                            {{ __('Usuários') }}
                        </x-nav-link>
                        <x-nav-link :href="route('gestor.logs.index')" :active="request()->routeIs('gestor.logs.*')">
                            {{ __('Logs') }}
                        </x-nav-link>
                    @elseif(Auth::user()->isAgenteEndemias())
                        <x-nav-link :href="route('agente.doencas.index')" :active="request()->routeIs('agente.doencas.*')">
                            {{ __('Doenças') }}
                        </x-nav-link>
                        <x-nav-link :href="route('agente.locais.index')" :active="request()->routeIs('agente.locais.*')">
                            {{ __('Locais') }}
                        </x-nav-link>
                        <x-nav-link :href="route('agente.visitas.index')" :active="request()->routeIs('agente.visitas.*')">
                            {{ __('Visitas') }}
                        </x-nav-link>
                    @elseif(Auth::user()->isAgenteSaude())
                        <x-nav-link :href="route('saude.doencas.index')" :active="request()->routeIs('saude.doencas.*')">
                            {{ __('Doenças') }}
                        </x-nav-link>
                        <x-nav-link :href="route('saude.visitas.index')" :active="request()->routeIs('saude.visitas.*')">
                            {{ __('Minhas Visitas') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium
                            rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800
                            hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition">
                            <div>{{ Auth::user()->use_nome }}</div>
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
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Meu Perfil') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Sair do Sistema') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
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
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('*dashboard')">
                {{ __('Página Inicial') }}
            </x-responsive-nav-link>

            @if(Auth::user()->isGestor())
                <x-responsive-nav-link :href="route('gestor.doencas.index')" :active="request()->routeIs('gestor.doencas.*')">
                    {{ __('Doenças') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('gestor.locais.index')" :active="request()->routeIs('gestor.locais.*')">
                    {{ __('Locais') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('gestor.visitas.index')" :active="request()->routeIs('gestor.visitas.*')">
                    {{ __('Visitas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('gestor.relatorios.index')" :active="request()->routeIs('gestor.relatorios.*')">
                    {{ __('Relatórios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('gestor.users.index')" :active="request()->routeIs('gestor.users.*')">
                    {{ __('Usuários') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('gestor.logs.index')" :active="request()->routeIs('gestor.logs.*')">
                    {{ __('Logs') }}
                </x-responsive-nav-link>
            @elseif(Auth::user()->isAgenteEndemias())
                <x-responsive-nav-link :href="route('agente.doencas.index')" :active="request()->routeIs('agente.doencas.*')">
                    {{ __('Doenças') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('agente.locais.index')" :active="request()->routeIs('agente.locais.*')">
                    {{ __('Locais') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('agente.visitas.index')" :active="request()->routeIs('agente.visitas.*')">
                    {{ __('Visitas') }}
                </x-responsive-nav-link>
            @elseif(Auth::user()->isAgenteSaude())
                <x-responsive-nav-link :href="route('saude.doencas.index')" :active="request()->routeIs('saude.doencas.*')">
                    {{ __('Doenças') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('saude.visitas.index')" :active="request()->routeIs('saude.visitas.*')">
                    {{ __('Minhas Visitas') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Mobile Settings -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->use_nome }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->use_email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Meu Perfil') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Sair do Sistema') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
@endauth