#!/bin/bash

self=$(basename "$0")
base=$PWD
app_dir=${1:-laravel}
public_dir=${2:-public_html}
app_path="$base/$app_dir"
public_path="$base/$public_dir"

mkdir "$app_path"

mv -t "$app_path" "$(\ls | grep -v -E "($self|$app_dir)")"
mv -t "$app_path" .env* .git* .editor* .prettier*
mv "$app_path"/public "$public_path"

sed -i "32a\
  \ \ \ \ \ \ \ \ app()->usePublicPath(\base_path('../$public_dir'));" "$app_path"/app/Providers/AppServiceProvider.php

# Remove Old public path
sed -i "/'links' => \[/{n;d}" "$app_path"/config/filesystems.php

sed -i "/'links' => \[/a\
\ \ \ \ \ \ \ \ base_path('../$public_dir/uploads') => storage_path('app/public')," "$app_path"/config/filesystems.php

sed -i "/laravel({/a\
\ \ \ \ \ \ publicDirectory: '../$public_dir'," "$app_path"/vite.config.js

# Add app dir to public index.php
sed -i "s/vendor/$app_dir\/vendor/g" "$public_path"/index.php
sed -i "s/bootstrap/$app_dir\/bootstrap/g" "$public_path"/index.php
