{{-- Página mínima sem layouts/app (sem @vite / componentes). Último recurso se a home e o fallback completo falharem. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Visita Aí') }}</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 36rem; margin: 3rem auto; padding: 0 1.25rem; line-height: 1.55; color: #0f172a; background: #f8fafc; }
        h1 { font-size: 1.375rem; font-weight: 800; line-height: 1.25; }
        p { margin-top: 1rem; font-size: 0.9375rem; color: #475569; }
        .actions { margin-top: 1.75rem; display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .actions a { display: inline-block; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 600; text-decoration: none; }
        .primary { background: #2563eb; color: #fff; }
        .primary:hover { background: #1d4ed8; color: #fff; }
        .secondary { background: #fff; color: #1e293b; border: 1px solid #cbd5e1; }
        .secondary:hover { background: #f1f5f9; }
        footer { margin-top: 2.5rem; font-size: 0.6875rem; color: #94a3b8; }
        footer a { color: #64748b; }
    </style>
</head>
<body>
    <h1>{{ __('Bem-vindo(a) ao') }} {{ config('app.name', 'Visita Aí') }}</h1>
    <p>{{ __('Plataforma municipal para registrar e acompanhar visitas de campo na luta contra dengue, chikungunya, Zika e outras arboviroses. Gestores e agentes trabalham no sistema; você, morador, pode verificar quando houve visita no seu endereço — com o código que o ACE ou ACS informou.') }}</p>
    <div class="actions">
        <a class="primary" href="{{ route('login') }}">{{ __('Acessar o sistema') }}</a>
        <a class="secondary" href="{{ route('consulta.index') }}">{{ __('Verificar visitas no imóvel') }}</a>
    </div>
    <footer>
        &copy; {{ date('Y') }} Visita Aí · {{ __('Desenvolvido por') }}
        <a href="https://bitwise.dev.br" target="_blank" rel="noopener noreferrer">Bitwise Technologies</a>
    </footer>
</body>
</html>
