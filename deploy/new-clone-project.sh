if [ -z "$1" ]; then
  echo "Token is required."
  exit 0
fi

echo "DO NOT USE ROOT TO RUN THIS SCRIPT!!!"

read -r -p "y to continue, any key to cancel: "

if [ "${REPLY:0:1}" != 'y' ]; then
  exit 1
fi

token=$1
base_path=$(pwd)
project_name=${2:-akam-tech-hr}
project_path="$(pwd)/$project_name"

if [ -d "$project_path" ]; then

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
  cp "$project_path/.env" "$base_path/storage/.env"
fi

echo "- Removing application directory..."
rm -rf "$project_path"

echo "- Cloning latest version..."
git clone --branch main --single-branch --depth 1 "https://oauth2:$token@github.com/AlanD20/akam-tech-hr.git" "$project_name" &>/dev/null

cd "$project_path"

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
