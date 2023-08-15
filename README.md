# Filament Template

Filament template application

---

## Development

Clone the repository then run `init.sh` script in project directory. The script
takes first argument to name your project with kebab case.

```bash
./init.sh example-project
```

Then, run `deploy/install.sh` script or `development` script in `package.json`
to setup local environment.

## Deployment

- Fresh server? run server setup by running `deploy/setup.sh` script.

```bash
./setup.sh
```

There are two options to clone the project:

1. if you want to clone after each new commit, copy `deploy/install-storage.sh`,
   then run the script. It backs up storage files, then removes the project, and
   finally, clones the latest commit.

2. You can clone the repository first, then run `deploy/install.sh` script, it
   installs dependencies and necessary operations to prepare the project for
   serving via nginx.

- You can use either `deploy/clean-rebase.sh` or `deploy/only-rebase.sh` scripts
  to pull with force rebase.
- `deploy/clean-rebase.sh`: Removes vendor directories, lock files, and clears
  cache. Then, pulls the change with force rebase, and finally, install
  dependencies.
- `deploy/only-rebase.sh`: Pull the changes with force rebase. Nothing will be
  installed nor deleted.

### Database Setup

```bash
# Login to database
mysql -u root -p

# Change mysql's root user password
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'Your Password';

# Flush Cache
FLUSH PRIVILEGES;

# Create Database
CREATE DATABASE mydb;
```

### Scripts

- `fresh`: Migrate fresh the database, you may use `--seed` flag to include
  seeding.
- `clear`: Clear application cache and dump autoload.
- `development`: Install packages and setup local environment.
- `key:hex-gen`: Generate 256 bit random hex into `hex-key.txt` file.
- `key:get`: Generate a key based on given text.
- `key:server`: Uses `key:get` and `hex-key.txt` content to generate a password
  for server.
- `key:admin`: Uses `key:get` and `hex-key.txt` content to generate a password
  for admin.
- `key:aland`: Uses `key:get` and `hex-key.txt` content to generate a password
  for aland.

---

## License

This repository is under [GNU General Public License v3](/LICENSE).
