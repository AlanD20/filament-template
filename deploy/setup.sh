echo "DO NOT USE ROOT TO RUN THIS SCRIPT!!!"

if [ "$(whoami)" == 'root' ]; then
  echo "Error: looks like you are root"
  exit 1
fi

read -r -p "y to continue, any key to cancel: "

if [ "${REPLY:0:1}" != 'y' ]; then
  exit 1
fi

base_path=$PWD
project_name=${2:-filament-template}
project_path="$PWD/$project_name"
github_user=${3:-aland20}

if [ -d "$project_path" ]; then

  # Make sure permissions are correct before copying...
  echo "- Changing uploaded files ownership..."
  sudo chown "$USER":www-data -R "$project_path/storage/app"

  echo "- Removing 'storage' backup..."
  rm -rf "$base_path/storage"
  mkdir "$base_path/storage"

  if [ -d "$project_path/storage/app/private" ]; then
    echo "- Copying uploaded files..."
    cp -R "$project_path/storage/app/private" "$base_path/storage/private"
  fi

  if [ -d "$project_path/storage/app/backups" ]; then
    echo "- Copying database backups..."
    cp -R "$project_path/storage/app/backups" "$base_path/storage/backups"
  fi
fi

if [ -f "$project_path/.env" ]; then
  echo "- Copying environment variable file..."
  mkdir -p "$base_path/storage"
  cp "$project_path/.env" "$HOME/.env.bak"
fi

echo "- Removing application directory..."
rm -rf "$project_path"

echo "- Cloning latest version..."
git clone --branch main --single-branch --depth 1 "git@github.com:$github_user/$project_name.git" "$project_name" &>/dev/null

if [ ! -d "$project_path" ]; then
  echo "Error: failed to clone repository"
  exit 1
fi

cd "$project_path" || exit 1

echo "- Recovering environment variable file..."
if [ -f "$HOME/.env.bak" ]; then
  cp "$HOME/.env.bak" "$project_path/.env"
elif [ -f "$base_path/storage/.env" ]; then
  cp "$base_path/storage/.env" "$project_path/.env"
else
  cp .env.example .env
fi

echo "- Installing PHP dependencies..."
composer clear-cache &>/dev/null
composer install --optimize-autoloader --no-dev &>/dev/null

echo "- Installing Javascript dependencies..."
yarn install &>/dev/null

echo "- Building frontend..."
yarn build &>/dev/null

echo "- Cleaning up Node Modules and installing without dev..."
rm -rf "$project_path/node_modules"
yarn install --production=true &>/dev/null

read -r -p "Would you like to regenerate application key? (y for yes, any key for no): "

if [ "${REPLY:0:1}" == "y" ]; then
  echo "- Generating key..."
  php artisan key:generate
fi

echo "- Linking storage..."
php artisan storage:link

echo "- Migrating Database..."
yes | php artisan migrate

if [ -d "$base_path/storage/private" ]; then
  echo "- Recovering uploaded files..."
  rm -rf "$project_path/storage/app/private"
  cp -R "$base_path/storage/private" "$project_path/storage/app/private"
fi

if [ -d "$base_path/storage/backups" ]; then
  echo "- Recovering database backups..."
  rm -rf "$project_path/storage/app/backups"
  cp -R "$base_path/storage/backups" "$project_path/storage/app/backups"
fi

echo "- Clearing cache..."
yarn clear &>/dev/null

echo "- Setting up correct permission..."
sudo chown :www-data -R "$project_path/storage"
sudo chown :www-data -R "$project_path/bootstrap/cache"
sudo chmod 775 -R "$project_path/storage"
sudo chmod 775 -R "$project_path/bootstrap/cache"

echo "- Clearing project..."
bash "$project_path/clean-prod.sh"
rm -rf "$project_path/.git"

echo "- Upgrade successful!"
