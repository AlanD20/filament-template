#!/bin/bash

# Create git repo folder & set default git config
echo "=========================================="
echo 'Setting up git...'
echo "=========================================="

sudo git config --global init.defaultBranch main
sudo git config --global user.email "aland20@pm.me"
sudo git config --global user.name "AlanD20"

echo "=========================================="
echo 'git setup succeded'
echo "=========================================="

echo "=========================================="
echo 'Installing Nodejs/nvm!'
echo "=========================================="

cd ~ && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash

. ~/.nvm/nvm.sh
. ~/.bashrc

nvm install 16.19.0
nvm alias default 16.19.0
nvm use default
npm install npm@latest --location=global
npm install yarn pm2 --location=global

echo "=========================================="
echo 'Nodejs installed & set to v16.19.0!'
echo "=========================================="
