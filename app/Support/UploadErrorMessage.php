<?php

namespace App\Support;

final class UploadErrorMessage
{
    public static function forPhpUploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => __('O ficheiro ultrapassa o tamanho máximo permitido pelo servidor. Reduza o tamanho ou contacte o suporte para rever o limite de envios.'),
            UPLOAD_ERR_FORM_SIZE => __('O ficheiro excede o limite de 10 MB permitido neste formulário.'),
            UPLOAD_ERR_PARTIAL => __('O envio do ficheiro ficou incompleto. Verifique a rede e tente novamente.'),
            UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_EXTENSION => __('O servidor não conseguiu guardar o ficheiro. Tente de novo mais tarde ou contacte o suporte.'),
            UPLOAD_ERR_NO_FILE => '',
            default => __('Não foi possível concluir o envio do ficheiro. Tente novamente.'),
        };
    }
}
