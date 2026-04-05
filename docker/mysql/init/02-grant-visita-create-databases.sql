-- Executado só na primeira inicialização do MySQL (volume de dados vazio).
-- Permite `php artisan tenants:provision` usando DB_USERNAME=visita como tenant_provision,
-- sem precisar de root na app. Em bases já existentes: corre manualmente o mesmo GRANT como root.
GRANT CREATE ON *.* TO 'visita'@'%';
FLUSH PRIVILEGES;
