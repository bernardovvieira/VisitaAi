<?php

namespace App\Support\Tenancy;

/**
 * Extrai o slug de tenant a partir do Host e dos domínios base configurados.
 */
class HostSlugResolver
{
    public function __construct(
        private readonly array $baseDomains,
        private readonly ?string $devSlug,
    ) {}

    /**
     * @return string|null slug ou null (apex / sem match / usar DB default)
     */
    public function resolve(string $host): ?string
    {
        $host = strtolower(trim($host));

        if ($host === '') {
            return null;
        }

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true) && $this->devSlug !== null && $this->devSlug !== '') {
            return strtolower(trim($this->devSlug));
        }

        $bases = $this->baseDomains;
        usort($bases, fn (string $a, string $b) => strlen($b) <=> strlen($a));

        foreach ($bases as $base) {
            $base = strtolower(trim($base));
            if ($base === '') {
                continue;
            }
            if ($host === $base) {
                return null;
            }
            $suffix = '.'.$base;
            if (! str_ends_with($host, $suffix)) {
                continue;
            }
            $sub = substr($host, 0, -strlen($suffix));
            if ($sub === '') {
                return null;
            }
            $parts = explode('.', $sub);

            return $parts[0] !== '' ? $parts[0] : null;
        }

        return null;
    }
}
