if [ -z "$1" ]; then
  echo "Token is required."
  exit 0
fi

token=$1
base_path=$(pwd)
project_name=${2:-filament-template}
project_path="$(pwd)/$project_name"

rm -rf $base_path/storage
mkdir $base_path/storage

if [ -d "$project_path/storage/app/private" ]; then
  rm -rf $base_path/storage/app/private
  cp -R $project_path/storage/app/private $base_path/storage/private
fi

if [ -d "$project_path/storage/app/backups" ]; then
  rm -rf $base_path/storage/app/backups
  cp -R $project_path/storage/app/backups $base_path/storage/backups
fi

rm -rf $project_path
git clone --branch main --single-branch --depth 1 https://oauth2:$token@github.com/AlanD20/filament-template.git $project_name

cd $project_path
cp .env.example .env

if [ -f "$HOME/.env.bak" ]; then
  cp $HOME/.env.bak $project_path/.env
fi

composer clear-cache
composer install --optimize-autoloader --no-dev
yarn install && yarn clear && yarn build
php artisan key:generate
php artisan storage:link
yes | php artisan migrate

if [ -d "$base_path/storage/private" ]; then
  rm -rf $project_path/storage/app/private
  cp -R $base_path/storage/private $project_path/storage/app/private
fi

if [ -d "$base_path/storage/backups" ]; then
  rm -rf $project_path/storage/app/backups
  cp -R $base_path/storage/backups $project_path/storage/app/backups
fi

sudo chown :www-data -R $project_path/storage
sudo chown :www-data -R $project_path/bootstrap/cache
sudo chmod 775 -R $project_path/storage
sudo chmod 775 -R $project_path/bootstrap/cache

bash $project_path/clean-prod.sh

rm -rf $project_path/.git
