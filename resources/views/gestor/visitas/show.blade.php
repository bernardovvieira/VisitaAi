@extends('layouts.app')

@section('og_title', config('app.brand') . ' · ' . __('Detalhes da visita'))
@section('og_description', __('Informações completas da visita registrada no sistema.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Visitas'), 'url' => route('gestor.visitas.index')], ['label' => __('Visualizar')]]" />

    <x-page-header :eyebrow="__('Detalhe do registro')" :title="__('Detalhes da visita')">
        <x-slot name="lead">
            <p>{{ __('Informações completas da visita registrada no sistema.') }}</p>
        </x-slot>
    </x-page-header>

    @include('visitas.partials._visita-show-sections', ['visita' => $visita])
</div>

{{-- Mapa --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>.leaflet-marker-icon.custom-pin { background: none !important; border: none !important; }</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const lat = parseFloat("{{ $visita->local->loc_latitude }}") || -28.7;
    const lng = parseFloat("{{ $visita->local->loc_longitude }}") || -52.3;
    const map = L.map('map').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const pinSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="40"><path fill="#2563eb" stroke="#fff" stroke-width="1.5" d="M12 0C7.31 0 3.5 3.81 3.5 8.5c0 5.25 8.5 15.5 8.5 15.5s8.5-10.25 8.5-15.5C20.5 3.81 16.69 0 12 0z"/><circle fill="#fff" cx="12" cy="8.5" r="2.8"/></svg>';
    const pinIcon = L.divIcon({ className: 'custom-pin', html: pinSvg, iconSize: [28, 40], iconAnchor: [14, 40], popupAnchor: [0, -40] });
    L.marker([lat, lng], { icon: pinIcon }).addTo(map);
    setTimeout(() => map.invalidateSize(), 100);
});
</script>
@endsection
