@props([
    'warningAutodismiss' => false,
])

@if(session('success'))
    <x-alert type="success" :message="session('success')" />
    @isset($afterSuccess)
        {{ $afterSuccess }}
    @endisset
@endif
@if(session('status') === 'two-factor-authentication-enabled')
    <x-alert type="success" :message="__('Autenticação em dois fatores (2FA) ativada com sucesso. Na próxima sessão você precisará informar o código do aplicativo autenticador.')" />
@elseif(session('status') === 'verification-link-sent')
    <x-alert type="success" :message="__('Um novo link de verificação foi enviado para o e-mail informado.')" />
@elseif(session('status'))
    <x-alert type="success" :message="session('status')" />
@endif
@if(session('warning'))
    <x-alert type="warning" :message="session('warning')" :autodismiss="$warningAutodismiss" />
@endif
@if(session('error'))
    <x-alert type="error" :title="__('Erro')" :message="session('error')" />
@endif
@if(session('info'))
    <x-alert type="info" :message="session('info')" />
@endif
