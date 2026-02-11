@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Confirme sua senha</h1>

    <div class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Área segura do aplicativo
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            Por favor, digite sua senha para continuar.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf
            @if(request('return_action'))
                <input type="hidden" name="return_action" value="{{ request('return_action') }}">
            @endif
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
                <input id="password"
                       type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       class="mt-1 block w-full max-w-xs rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-green-500 focus:border-green-500">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 rounded-md shadow transition">
                    Voltar ao painel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
