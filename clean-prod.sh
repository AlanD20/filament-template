#!/bin/bash

self=$(basename "$0")
base=$PWD
app_dir="."
public_dir="public"
app_path="$base/$app_dir"
public_path="$base/$public_dir"

del=(
  .github
  deploy
  stubs
  tests
  .editorconfig
  .env.local*
  .git*
  .prettierignore
  *.md
  *.txt
  LICENSE
  database/
  app/Console
  resources/css
  resources/fonts
  resources/js
  phpunit*
  *.json
  *lock*
  *.config.js
)

for d in "${del[@]}"; do
  rm -rf "$d"
done

# Test cleanup
# sed -i '10,20d' "$app_path"/package.json
# sed -i '9s/",/"/g' "$app_path"/package.json

# Prettier cleanup
# sed -i '56,80d' "$app_path"/package.json
# sed -i '55s/\},/\}/g' "$app_path"/package.json

# Remove local seeders
# sed -i '22,30d' "$app_path"/database/seeders/DatabaseSeeder.php

cat <<'EOF' >composer.json
{
  "autoload": {
    "psr-4": {
      "App\\": "app"
    },
    "files": [
      "app/Helpers/globals.php"
    ]
  }
}
EOF
