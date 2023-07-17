#!/bin/bash

if [ -z "$1" ]; then
  echo "GitHub Fine-Grained Access Token is required."
  echo "- First argument is the token."
  exit 0
fi

token="$1"
project_path="$(pwd)/.."

cd $project_path
composer clear-cache
rm -f $project_path/composer.lock

php artisan down
rm -rf $project_path/vendor
rm -rf $project_path/node_modules
git pull https://oauth2:$token@github.com/AlanD20/filament-template.git main --rebase --force
composer install --optimize-autoloader --no-dev
yarn install && yarn clear && yarn build
yes | php artisan migrate
# chown :www-data -R storage
# chown :www-data -R bootstrap/cache
# chmod 775 -R storage
# chmod 775 -R bootstrap/cache
php artisan up
