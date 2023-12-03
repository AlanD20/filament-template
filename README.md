# filament-template

Filament template application

---

## Getting Started

Clone this boilerplate repository then run `./init.sh` shell script.

```bash
cd filament-template && ./init.sh <project-name>
```

To trigger GitHub Workflow formatter job, use `!format` in a commit message.

## Development

Make sure you've installed `pnpm`

```bash
npm install pnpm@latest --location=global
```

Then run `deploy/install.sh` bash script from the root of the project directory

```bash
./deploy/install.sh
```

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
