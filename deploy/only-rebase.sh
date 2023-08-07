#!/bin/bash

if [ -z "$1" ]; then
  echo "GitHub Fine-Grained Access Token is required."
  echo "- First argument is the token."
  exit 0
fi

token="$1"
project_path="$(pwd)/.."

cd "$project_path" || exit 0

git pull "https://oauth2:$token@github.com/AlanD20/akam-tech-hr.git" main --rebase --force
