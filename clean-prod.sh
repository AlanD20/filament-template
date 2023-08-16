#!/bin/bash

self=$(basename "$0")
base=$PWD
app_dir="."
public_dir="public"
app_path="$base/$app_dir"
public_path="$base/$public_dir"

del=(
  deploy
  stubs
  tests
  .editorconfig
  .env.local
  .prettierignore
  *.md
  *.txt
  LICENSE
  pint.json
  database/seeders/Local*
)

for d in "${del[@]}"; do
  rm -rf "$d"
done

# Test cleanup
sed -i '10,21d' "$app_path"/package.json
sed -i '9s/",/"/g' "$app_path"/package.json

# Prettier cleanup
sed -i '25,49d' "$app_path"/package.json
sed -i '24s/\},/\}/g' "$app_path"/package.json

# Remove local seeders
sed -i '20,25d' "$app_path"/database/seeders/DatabaseSeeder.php
