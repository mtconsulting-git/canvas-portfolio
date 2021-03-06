## Introduction

Canvas is a fully open source package to extend your existing [Laravel](https://laravel.com) application and get  you
 up-and-running with a blog in just a few minutes. In addition to a distraction-free writing experience, you can
 view monthly trends on your content, get insights into reader traffic and more!

## Table of Contents

- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Roles & Permissions](#roles--permissions)
- [Features](#features)
- [Updates](#updates)
- [Contributing](#contributing)
- [License](#license)

## System Requirements

- PHP >= 7.3
- Laravel >= 6.0
- One of the [four supported databases](https://laravel.com/docs/8.x/database#introduction) by Laravel

## Installation   

You may use composer to install Canvas into your Laravel project:

```bash
composer require mtconsultingroup/canvas-portfolio
```

Publish the assets and primary configuration file using the `canvas:install` Artisan command:

```bash
php artisan canvas:install
```

Create a symbolic link to ensure file uploads are publicly accessible from the web using the `storage:link` Artisan command:

```bash
php artisan storage:link
```

## Configuration

After publishing Canvas's assets, a primary configuration file will be located at `config/canvas.php`. This file allows you to customize various aspects of how your application uses the package.

Canvas exposes its UI at `/canvas` by default. This can be changed by updating either the `path` or `domain` option:

```php
/*
|--------------------------------------------------------------------------
| Base Domain
|--------------------------------------------------------------------------
|
| This is the subdomain where Canvas will be accessible from. If the
| domain is set to null, Canvas will reside under the defined base
| path below. Otherwise, this will be used as the subdomain.
|
*/

'domain' => env('CANVAS_DOMAIN', null),

/*
|--------------------------------------------------------------------------
| Base Path
|--------------------------------------------------------------------------
|
| This is the URI where Canvas will be accessible from. If the path
| is set to null, Canvas will reside under the same path name as 
| the application. Otherwise, this is used as the base path.
|
*/

'path' => env('CANVAS_PATH_NAME', 'admin'),
```

Sometimes, you may want to apply custom roles or permissions when accessing Canvas. You can create and attach any
 additional middleware here:

```php
/*
|--------------------------------------------------------------------------
| Route Middleware
|--------------------------------------------------------------------------
|
| These middleware will be attached to every route in Canvas, giving you
| the chance to add your own middleware to this list or change any of
| the existing middleware. Or, you can simply stick with the list.
|
*/

'middleware' => [
    'web',
],
```

Canvas uses the storage disk for media uploads. You may configure the different filesystem options here:

```php
/*
|--------------------------------------------------------------------------
| Storage
|--------------------------------------------------------------------------
|
| This is the storage disk Canvas will use to put file uploads. You may
| use any of the disks defined in the config/filesystems.php file and
| you may also change the maximum upload size from its 3MB default.
|
*/

'storage_disk' => env('CANVAS_STORAGE_DISK', 'local'),

'storage_path' => env('CANVAS_STORAGE_PATH', 'public/canvas'),

'upload_filesize' => env('CANVAS_UPLOAD_FILESIZE', 3145728),
```

## Roles & Permissions

Canvas has 3 pre-defined roles:

- **Contributor** (Somebody who can write and manage their own posts but cannot publish them)
- **Editor** (Somebody who can publish and manage posts including the posts of other users)
- **Admin** (Somebody who can do everything and see everything)

When you install a fresh version of Canvas, you'll have a default admin user set up automatically. From there, you
 can perform any basic CRUD actions on users, as well as assign their various roles. 

## Features

> **Note:** The following features are completely optional, you are not required to use them.

### Frontend

Use the `canvas:ui` Artisan command to get the frontend APIs:

```bash
php artisan canvas:ui
```

### Unsplash Integration

**Want access to the entire [Unsplash](https://unsplash.com) library?** Set up a new application at [https://unsplash.com/oauth/applications](https://unsplash.com/oauth/applications), grab your access key, and update `config/canvas.php`:

```php
/*
|--------------------------------------------------------------------------
| Unsplash Integration
|--------------------------------------------------------------------------
|
| Visit https://unsplash.com/oauth/applications to create a new Unsplash
| app. Use the confidential Access Key given to you to integrate with
| the API. Note that demo apps are limited to 50 requests per hour.
|
*/

'unsplash' => [
    'access_key' => env('CANVAS_UNSPLASH_ACCESS_KEY'),
]
```

### Weekly Digest

**Want a weekly summary?** Canvas allows users to receive a weekly summary of their authored content. Once your application is [configured for sending mail](https://laravel.com/docs/master/mail), update `config/canvas.php`:

```php
/*
|--------------------------------------------------------------------------
| E-Mail Notifications
|--------------------------------------------------------------------------
|
| This option controls e-mail notifications that will be sent via the
| default application mail driver. A default option is provided to
| support the notification system as an opt-in feature.
|
|
*/

'mail' => [
    'enabled' => env('CANVAS_MAIL_ENABLED', false),
]
```
Since the weekly digest runs on [Laravel's Scheduler](https://laravel.com/docs/master/scheduling), you'll need to add the following cron entry to your server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Portfolio

**Want a portfolio integration?**

```php
/*
|--------------------------------------------------------------------------
| Portfolio
|--------------------------------------------------------------------------
|
| This option enables or disables the sections dedicated to the portfolio.
| By default it is disabled.
|
|
*/

'portfolio' => [
    'enabled' => env('CANVAS_PORTFOLIO_ENABLED', false),
]
```

### Multiple languages

**You can also edit your contents in multiple languages**

```php
/*
|--------------------------------------------------------------------------
| Enable languages
|--------------------------------------------------------------------------
|
| This option allows you to edit the contents in different languages
| (available languages: IT, EN, ES, FR, DE).
| By default only the Italian language is enabled, to add others write them
| in the .env file (example: CANVAS_LANGUAGES=it,en,de)
|
|
*/

'languages' => env('CANVAS_LANGUAGES', 'it')
```

### Automatic translation

```php
/*
|--------------------------------------------------------------------------
| Enable automatic translation
|--------------------------------------------------------------------------
|
| This option allows you to automatically translate all the contents in the
| Italian language into any of the available languages.
| To enable this function, just enter your google key in the .env file.
|
|
*/

'google_translate' => [
    'access_key' => env('CANVAS_GOOGLE_TRANSLATE_ACCESS_KEY'),
]
```

## Updates

Canvas follows [Semantic Versioning](https://semver.org) and increments versions as `MAJOR.MINOR.PATCH` numbers.
- Major versions **will** contain breaking changes, so follow the [upgrade guide](.github/UPGRADE.md) for a
 step-by-step breakdown
- Minor and patch versions should **never** contain breaking changes, so you can safely update the package by following the steps below:

You may update your Canvas installation using composer:

```bash
composer update
```

Run any new migrations using the `canvas:migrate` Artisan command:

```bash
php artisan canvas:migrate
```

Re-publish the assets using the `canvas:publish` Artisan command:

```bash
php artisan canvas:publish
```

To keep the assets up-to-date and avoid issues in future updates, you may add the `canvas:publish` command to the
 `post-update-cmd` scripts in your application's `composer.json` file:
 
```bash
{
    "scripts": {
        "post-update-cmd": [
            "@php artisan canvas:publish --ansi"
        ]
    }
}
```

## Contributing

Thank you for considering contributing to Canvas!

You can use the [contribution guide](.github/CONTRIBUTING.md) to assist you in manually setting up an
 environment on your own machine.

## License

Canvas is open-sourced software licensed under the [MIT license](license).
