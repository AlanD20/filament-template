project_path="$PWD/.."

cd "$project_path" || exit 1

composer clear-cache

cp .env.example.local .env
composer install --optimize-autoloader --no-dev
pnpm install && pnpm build
yes | php artisan key:generate
yes | php artisan storage:link
yes | php artisan migrate
pnpm run clear # Must be later, since we generate a new key
pnpm run fresh --seed
