composer install
php bin/console doctrine:database:create
php bin/console make:migration 
php bin/console doctrine:migrations:migrate
chown -R www-data:www-data /var/www/calendario_subathonico/api/var/data.db 
chmod -R 775 /var/www/calendario_subathonico/api/var/data.db

http://localhost:8000/api/update e senha