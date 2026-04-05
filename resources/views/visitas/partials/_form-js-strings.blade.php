{{-- Strings para Alpine/JS nos formulários de visita (offline, busca de local, erros). --}}
<script>
    window.__visitaFormStrings = @json([
        'deviceLocalPending' => __('(Dispositivo) Local a sincronizar'),
        'codePrefix' => __('Cód. '),
        'sn' => __('S/N'),
        'deviceDraftPrefix' => __('(Dispositivo) '),
        'selectLocalBeforeSuggestions' => __('Selecione o local visitado antes de buscar sugestões.'),
        'errorWithStatus' => __('Erro :status. Tente novamente.'),
        'sessionExpired' => __('Sessão expirada. Recarregue a página.'),
        'invalidResponse' => __('Resposta inválida. Tente novamente.'),
        'couldNotLoadSuggestions' => __('Não foi possível carregar sugestões. Verifique a conexão e recarregue a página.'),
        'loadFailed' => __('Falha ao carregar.'),
        'selectLocalBeforeSave' => __('Selecione o local visitado antes de salvar.'),
        'offlineUnavailable' => __('Recurso de offline não disponível. Tente recarregar a página.'),
        'registerVisit' => __('Registrar visita'),
        'saveVisit' => __('Guardar visita'),
        'registeredNow' => __('registrada na hora'),
        'savedDevice' => __('guardada no dispositivo para enviar depois'),
        'withInternet' => __('Com internet'),
        'withoutInternet' => __('Sem internet'),
        'fetchDiseaseSuggestionsFailed' => __('Não foi possível carregar as sugestões.'),
    ]);
    window.__visitaFormatLocalLine = function (local) {
        var p = window.__visitaFormStrings;
        var num = local.loc_numero;
        var numPart = (num != null && num !== '') ? num : p.sn;
        return p.codePrefix + (local.loc_codigo_unico || '') + ' - ' + (local.loc_endereco || '') + ', ' + numPart + ' - ' + (local.loc_bairro || '') + ', ' + (local.loc_cidade || '') + '/' + (local.loc_estado || '');
    };
</script>
