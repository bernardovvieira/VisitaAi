<?php

$envBool = static function (mixed $value, bool $default): bool {
    if ($value === null || $value === '') {
        return $default;
    }
    if (is_bool($value)) {
        return $value;
    }

    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
};

return [

    /*
    |--------------------------------------------------------------------------
    | Registro público (self-service)
    |--------------------------------------------------------------------------
    | false: rotas GET/POST /register respondem 404; link some da tela de login.
    | Use em produção municipal quando só o gestor cria contas.
    */

    'open_registration' => $envBool(env('APP_OPEN_REGISTRATION'), true),

];
