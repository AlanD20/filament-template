# git clone https://oauth2:$token@github.com/AlanD20/filament-template.git

project_path="$(pwd)/.."

cd $project_path
composer clear-cache

cp .env.example .env
composer install --optimize-autoloader --no-dev
yarn install && yarn clear && yarn build
php artisan key:generate
php artisan storage:link
yes | php artisan migrate
chown :www-data -R storage
chown :www-data -R bootstrap/cache
chmod 775 -R storage
chmod 775 -R bootstrap/cache
