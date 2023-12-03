#!/bin/bash

if [ "$1" = "" ]; then
  echo "Project name in kebab case is required. i.e, example-project"
  exit 0
fi

package_kebab_case=$1
# Shamelessly grabbed from: https://unix.stackexchange.com/a/196241
package_pascal_case=$(echo "$1" | sed -r 's/(^|-)([a-z])/\U\2/g')

# Replace kebab cases
find . -type f -exec sed -i "s/filament-template/$package_kebab_case/g" {} +

# Replace Pascal cases
find . -type f -exec sed -i "s/FilamentTemplate/$package_pascal_case/g" {} +

# Rename files
find . -type f -name "*FilamentTemplate*" -exec sh -c 'mv "$1" $(echo "$1" | sed "s/FilamentTemplate/$2/")' _ {} "$package_pascal_case" \;
find . -type f -name "*filament-template*" -exec sh -c 'mv "$1" $(echo "$1" | sed "s/filament-template/$2/")' _ {} "$package_kebab_case" \;

# Prepare current project
rm -rf .git init.sh

# Initialize git
git init --initial-branch main
