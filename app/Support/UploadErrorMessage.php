<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

final class UploadErrorMessage
{
    public static function forPhpUploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => self::iniSizeExceededMessage(),
            UPLOAD_ERR_FORM_SIZE => __('O arquivo excede o limite de 10 MB permitido neste formulário.'),
            UPLOAD_ERR_PARTIAL => __('O envio do arquivo ficou incompleto. Verifique a rede e tente novamente.'),
            UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_EXTENSION => __('O servidor não conseguiu salvar o arquivo. Tente de novo mais tarde ou entre em contato com o suporte.'),
            UPLOAD_ERR_NO_FILE => '',
            default => __('Não foi possível concluir o envio do arquivo. Tente novamente.'),
        };
    }

    /**
     * UPLOAD_ERR_INI_SIZE: o PHP recusou o arquivo antes da validação da app (ex.: arquivo de 3 MB com upload_max_filesize=2M).
     */
    private static function iniSizeExceededMessage(): string
    {
        $bytes = SymfonyUploadedFile::getMaxFilesize();
        $limit = self::formatBytesForHumans($bytes);

        return __('O servidor só aceita envios de até cerca de :limit por arquivo. Arquivos maiores são rejeitados antes do limite de 10 MB da aplicação. Reduza o arquivo ou peça ao suporte para aumentar o limite de envio no PHP.', [
            'limit' => $limit,
        ]);
    }

    /**
     * @param  int|float  $bytes
     */
    private static function formatBytesForHumans(int|float $bytes): string
    {
        if (! is_finite((float) $bytes) || $bytes <= 0) {
            return '?';
        }

        $mb = $bytes / 1048576;
        if ($mb >= 1) {
            return (string) (round($mb, $mb >= 10 ? 0 : 1)).' MB';
        }

        $kb = $bytes / 1024;
        if ($kb >= 1) {
            return (string) max(1, (int) round($kb)).' KB';
        }

        return (string) max(0, (int) $bytes).' B';
    }
}
