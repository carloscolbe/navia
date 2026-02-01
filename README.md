# 🌊 **N**avia

A modern Laravel admin panel featuring a role-based BREAD system (browse, read, edit, add and delete), built as an evolution of [Voyager](https://github.com/thedevdojo/voyager).

## ⚙️ Installation

### 1️⃣ Install Navia using Composer

Run the following command from your Laravel project root:

```bash
composer require carloscolmenarez/navia
```

### 2️⃣ Add the DB Credentials & APP_URL

Next make sure to create a new database and add your database credentials to your .env file:

```yaml
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

You will also want to update your website URL inside of the `APP_URL` variable inside the .env file:

```yaml
APP_URL=http://localhost:8000
```

### 3️⃣ Run The Installer

Lastly, we can install navia. You can do this either with or without dummy data.
The dummy data will include 1 admin account (if no users already exists), 1 demo page, 4 demo posts, 2 categories and 7 settings.

To install Navia without dummy simply run

```bash
php artisan navia:install
```

If you prefer installing it with dummy run

```bash
php artisan navia:install --with-dummy
```

And we're all good to go!

Start up a local development server with `php artisan serve` And, visit [http://localhost:8000/admin](http://localhost:8000/admin).

## 👽 Creating an Admin User

If you did go ahead with the dummy data, a user should have been created for you with the following login credentials:

>**email:** `admin@admin.com`
>**password:** `password`

NOTE: Please note that a dummy user is **only** created if there are no current users in your database.

If you did not go with the dummy user, you may wish to assign admin privileges to an existing user.
This can easily be done by running this command:

```bash
php artisan navia:admin your@email.com
```

If you did not install the dummy data and you wish to create a new admin user, you can pass the `--create` flag, like so:

```bash
php artisan navia:admin your@email.com --create
```

And you will be prompted for the user's name and password.

## 🙏 Acknowledgements

Navia builds upon the amazing work of the original [Voyager](https://github.com/thedevdojo/voyager) package.
All credit and appreciation go to the original creators for their contribution to the Laravel ecosystem.

## Enjoy! 🚤
