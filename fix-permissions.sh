#!/bin/bash

echo "🔧 Corrigindo permissões das pastas storage e bootstrap/cache..."

chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Permissões corrigidas com sucesso."

# Executa o PHP-FPM normalmente após os ajustes
exec docker-php-entrypoint php-fpm
