<?php

declare(strict_types=1);

/**
 * Gera lang/pt_BR.json (catálogo explícito) e lang/en.json a partir das chaves em resources/views
 * e dos mapas em scripts/i18n/en-*.php.
 */
$translations = array_merge(
    require __DIR__.'/i18n/en-1.php',
    require __DIR__.'/i18n/en-2.php',
    require __DIR__.'/i18n/en-3.php',
);

$base = dirname(__DIR__).'/resources/views';
$rii = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
);
$keys = [];

foreach ($rii as $file) {
    if (! str_ends_with($file->getPathname(), '.blade.php')) {
        continue;
    }
    $c = file_get_contents($file->getPathname());
    if ($c === false) {
        continue;
    }
    $len = strlen($c);
    $offset = 0;
    while (($start = strpos($c, '__(', $offset)) !== false) {
        $offset = $start + 3;
        $p = $offset;
        while ($p < $len && ctype_space($c[$p])) {
            $p++;
        }
        if ($p >= $len) {
            break;
        }
        $q = $c[$p];
        if ($q !== "'" && $q !== '"') {
            continue;
        }
        $p++;
        $buf = '';
        $esc = false;
        for (; $p < $len; $p++) {
            $ch = $c[$p];
            if ($esc) {
                $buf .= $ch;
                $esc = false;

                continue;
            }
            if ($ch === '\\') {
                $esc = true;

                continue;
            }
            if ($ch === $q) {
                break;
            }
            $buf .= $ch;
        }
        if (str_contains($buf, '$')) {
            continue;
        }
        $keys[$buf] = true;
    }
}

ksort($keys);
$ptBr = [];
$en = [];
$missing = [];

foreach (array_keys($keys) as $pt) {
    $ptBr[$pt] = $pt;
    if (isset($translations[$pt])) {
        $en[$pt] = $translations[$pt];
    } else {
        $missing[] = $pt;
        $en[$pt] = $pt;
    }
}

$langDir = dirname(__DIR__).'/lang';
if (! is_dir($langDir)) {
    mkdir($langDir, 0755, true);
}

file_put_contents(
    $langDir.'/pt_BR.json',
    json_encode($ptBr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n"
);
file_put_contents(
    $langDir.'/en.json',
    json_encode($en, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n"
);

if ($missing !== []) {
    fwrite(STDERR, 'Missing EN translations ('.count($missing)."):\n");
    foreach ($missing as $m) {
        fwrite(STDERR, json_encode($m, JSON_UNESCAPED_UNICODE)."\n");
    }
    exit(1);
}

echo 'Wrote lang/pt_BR.json and lang/en.json ('.count($keys).' keys, '.count($translations)." map entries).\n";
