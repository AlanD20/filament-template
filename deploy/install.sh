# git clone https://oauth2:$token@github.com/AlanD20/akam-tech-hr.git

project_path="$(pwd)/.."

cd $project_path
composer clear-cache

cp .env.example .env
composer install --optimize-autoloader --no-dev
yarn install && yarn build
php artisan key:generate
php artisan storage:link
yes | php artisan migrate
yarn clear # Must be later, since we generate a new key
chown :www-data -R storage
chown :www-data -R bootstrap/cache
chmod 775 -R storage
chmod 775 -R bootstrap/cache
