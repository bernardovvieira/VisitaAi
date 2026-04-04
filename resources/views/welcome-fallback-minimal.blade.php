{{-- Home autônoma sem @vite (último recurso): mesma composição visual da welcome principal. --}}
@php
    $ogTitle = config('app.name');
    $ogDescription = __('Visita Aí apoia a vigilância entomológica e o controle de vetores no município, alinhado às diretrizes do Ministério da Saúde. Profissionais registram visitas; moradores consultam o histórico do imóvel com o código único.');
    $ogImage = rtrim(config('app.url'), '/') . '/images/visitaai.png';
    $ogUrl = url()->current();
    $isHttps = str_starts_with(config('app.url'), 'https');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $ogTitle }}</title>
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    @if($isHttps)
    <meta property="og:image:secure_url" content="{{ $ogImage }}">
    @endif
    <meta property="og:site_name" content="Visita Aí">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <style>
        :root {
            --w-bg-1: #f1f5f9;
            --w-bg-2: #ffffff;
            --w-bg-3: rgba(239, 246, 255, 0.5);
            --w-title: #111827;
            --w-muted: #4b5563;
            --w-accent: #2563eb;
            --w-accent-bright: #3b82f6;
            --w-ms-border: rgba(191, 219, 254, 0.9);
            --w-ms-bg: rgba(239, 246, 255, 0.9);
            --w-ms-title: #172554;
            --w-ms-text: rgba(30, 58, 138, 0.92);
            --w-footer: #9ca3af;
            --w-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --w-bg-1: #020617;
                --w-bg-2: #0f172a;
                --w-bg-3: #020617;
                --w-title: #f9fafb;
                --w-muted: #9ca3af;
                --w-accent: #60a5fa;
                --w-accent-bright: #93c5fd;
                --w-ms-border: #1e3a5f;
                --w-ms-bg: rgba(23, 37, 84, 0.35);
                --w-ms-title: #dbeafe;
                --w-ms-text: rgba(191, 219, 254, 0.9);
                --w-footer: #6b7280;
                --w-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
            }
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: var(--w-title);
            background: linear-gradient(135deg, var(--w-bg-1) 0%, var(--w-bg-2) 45%, var(--w-bg-3) 100%);
            min-height: 100vh;
        }
        .w-shell {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
        }
        @media (min-width: 640px) {
            .w-shell { padding: 3rem 2rem; }
        }
        @media (min-width: 768px) {
            .w-shell { padding: 3rem 3rem; }
        }
        .w-grid {
            width: 100%;
            max-width: 80rem;
            display: grid;
            gap: 3rem;
            align-items: center;
        }
        @media (min-width: 1024px) {
            .w-grid { grid-template-columns: 1fr 1fr; gap: 3rem; }
        }
        .w-copy { max-width: 36rem; padding-left: 0; }
        @media (min-width: 768px) {
            .w-copy { padding-left: 1rem; }
        }
        .w-illu-mobile {
            display: flex;
            justify-content: center;
            margin-top: 0;
        }
        @media (min-width: 1024px) {
            .w-illu-mobile { display: none; }
        }
        .w-illu-mobile .welcome-inline-illu { max-width: 16rem; }
        .w-illu-desktop {
            display: none;
            justify-content: center;
        }
        @media (min-width: 1024px) {
            .w-illu-desktop { display: flex; }
        }
        .welcome-inline-illu { width: 100%; max-width: 28rem; }
        .welcome-inline-illu svg {
            width: 100%;
            height: auto;
            display: block;
            filter: drop-shadow(0 12px 32px rgba(37, 99, 235, 0.15));
        }
        @media (prefers-color-scheme: dark) {
            .welcome-inline-illu svg {
                filter: drop-shadow(0 12px 40px rgba(30, 64, 175, 0.25));
            }
        }
        h1 {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.2;
            margin: 0 0 1rem;
            letter-spacing: -0.02em;
        }
        h1 .w-name { color: var(--w-accent); }
        .w-loc {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1d4ed8;
            margin: 0 0 1rem;
        }
        @media (prefers-color-scheme: dark) {
            .w-loc { color: #93c5fd; }
        }
        .w-lead {
            margin: 0;
            font-size: 1rem;
            line-height: 1.65;
            color: var(--w-muted);
            max-width: 32rem;
        }
        .w-btns {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }
        @media (min-width: 640px) {
            .w-btns { flex-direction: row; flex-wrap: wrap; }
        }
        .w-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-size: 13px;
            font-weight: 600;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.15s, border-color 0.15s, transform 0.15s;
            box-shadow: var(--w-shadow);
        }
        .w-btn svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
        .w-btn-primary {
            background: #3b82f6 !important;
            color: #fff !important;
            border: none;
        }
        .w-btn-primary:hover {
            background: #2563eb !important;
            color: #fff !important;
        }
        .w-btn-secondary {
            background: var(--w-bg-2);
            color: #1e293b;
            border: 1px solid #cbd5e1;
        }
        .w-btn-secondary:hover { background: #f8fafc; }
        @media (prefers-color-scheme: dark) {
            .w-btn-secondary {
                background: #1e293b;
                color: #f1f5f9;
                border-color: #475569;
            }
            .w-btn-secondary:hover { background: #334155; }
        }
        .w-ms {
            margin-top: 1.5rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid var(--w-ms-border);
            background: var(--w-ms-bg);
        }
        .w-ms-title {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--w-ms-title);
            margin: 0;
        }
        .w-ms-title svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; color: var(--w-accent-bright); }
        .w-ms p { margin: 0.35rem 0 0; font-size: 0.75rem; line-height: 1.5; color: var(--w-ms-text); }
        .w-foot {
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: var(--w-footer);
        }
        .w-foot a { color: var(--w-muted); text-decoration: none; }
        .w-foot a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="w-shell">
    <div class="w-grid">
        <div class="w-illu-mobile">
            @include('partials.welcome-illustration-inline')
        </div>
        <div class="w-copy">
            <h1 id="anim-texto">
                {{ __('Bem-vindo(a) ao') }}
                <span class="w-name">{{ config('app.name', 'Visita Aí') }}</span>
            </h1>
            @isset($local)
                @if ($local)
                    <p class="w-loc">{{ $local->loc_cidade }}/{{ $local->loc_estado }}</p>
                @endif
            @endisset
            <p class="w-lead">
                {{ __('Plataforma municipal para registrar e acompanhar visitas de campo na luta contra dengue, chikungunya, Zika e outras arboviroses. Gestores e agentes trabalham no sistema; você, morador, pode verificar quando houve visita no seu endereço — com o código que o ACE ou ACS informou.') }}
            </p>
            <div class="w-btns" id="anim-botoes">
                <a href="{{ route('login') }}" class="w-btn w-btn-primary">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    {{ __('Acessar o sistema') }}
                </a>
                <a href="{{ route('consulta.index') }}" class="w-btn w-btn-secondary">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" /></svg>
                    {{ __('Verificar visitas no imóvel') }}
                </a>
            </div>
            <div class="w-ms" id="anim-conformidade">
                <p class="w-ms-title">
                    <svg fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    {{ __('Alinhado às diretrizes do Ministério da Saúde') }}
                </p>
                <p>{{ __('O Visita Aí segue a Lei nº 11.350/2006 (ACE e ACS), a diretriz de atuação integrada desses profissionais, as diretrizes nacionais de arboviroses urbanas e o Programa Nacional de Controle da Dengue (PNCD), como referência para o trabalho em campo e a transparência para a população.') }}</p>
            </div>
            <p class="w-foot" id="anim-footer">
                &copy; {{ date('Y') }} Visita Aí · {{ __('Desenvolvido por') }}
                <a href="https://bitwise.dev.br" target="_blank" rel="noopener noreferrer">Bitwise Technologies</a>
            </p>
        </div>
        <div class="w-illu-desktop" id="anim-ilustracao">
            @include('partials.welcome-illustration-inline')
        </div>
    </div>
</div>
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    if (window.innerWidth >= 768) {
        document.getElementById('anim-texto')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-botoes')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-conformidade')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-footer')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-ilustracao')?.setAttribute('data-aos', 'fade-left');
        AOS.init({ duration: 800, once: true });
    }
</script>
</body>
</html>
