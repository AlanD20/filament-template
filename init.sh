#!/bin/bash

if [ "$1" = "" ]; then
  echo "Project name in kebab case is required. i.e, example-project"
  exit 0
fi

find . -type f -exec sed -i "s/filament-template/$1/g" {} +
rm -rf .git init.sh
