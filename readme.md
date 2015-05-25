# Laravel - Assets Versioning

[![Build Status](https://travis-ci.org/EscapeWork/laravel-asset-versioning.png)](http://travis-ci.org/EscapeWork/laravel-asset-versioning) [![Latest Stable Version](https://poser.pugx.org/escapework/laravel-asset-versioning/v/stable.png)](https://packagist.org/packages/escapework/laravel-asset-versioning) [![Total Downloads](https://poser.pugx.org/escapework/laravel-asset-versioning/downloads.png)](https://packagist.org/packages/escapework/laravel-asset-versioning)

Have you ever had a problem with cache in your assets? This package may help you.

## Installation

Via Composer:

```
$ composer require escapework/laravel-asset-versioning:"0.3.*"
```

For Laravel 4.2<, use the `0.1.*` version. **PS:** Please note that no all features are available in `0.1` version.

Then, run the composer update command:

```bash
$ composer update
```

After that, you just need to add the following service provider to your app service providers. Open the file `app/config/app.php` and add this line:

```php
'EscapeWork\Assets\AssetsServiceProvider'
```

Publish the configurations running the following command:

```bash
$ php artisan vendor:publish
```

Make sure your Laravel app recognizes your local environment.

## Usage

Instead of using the `asset` helper, you will need to use the `Asset:v` method.

Imagine that your layout template has the following lines:

```html
<link rel="stylesheet" href="{{ Asset::v('assets/stylesheets/css/main.css') }}" />
<script src="{{ Asset::v('assets/javascripts/js/main.js') }}"></script>
```

In your **local** environment, nothing changes. But in **production**, you just need to run the following command every time you need to update your assets' version:

```bash
$ php artisan asset:dist
```

And your layout will be rendered as this:

```html
<link rel="stylesheet" href="/assets/stylesheets/dist/1392745827/main.css" />
<script src="/assets/javascripts/dist/1392745827/main.js"></script>
```

The version is the timestamp when you performed the `asset:dist` command.

This package knows which folder you need by the file extension, which is the array key in the config file.

You also can get only the path for some extension:

```
{{ Asset::path('css') }} <!-- /assets/stylesheets/dist/1392745827 -->
```

## Configurations

Of course you can configure the folders you need. Just edit the `config/assets.php` file, in the `types` array.

```php
'types' => [
    'css' => [
        'origin_dir' => 'your-custom-css-dir/css',
        'dist_dir'   => 'your-custom-css-dir/dist',
    ],

    'js' => [
        'origin_dir' => 'your-custom-js-dir/js',
        'dist_dir'   => 'your-custom-js-dir/dist',
    ],

    'jpg' => [
        'origin_dir' => 'assets/images',
        'dist_dir'   => 'assets/images/dist',
    ],
],
```

You also can add more folders by adding more items into the array.

Also, you can configure in which environments the assets are gonna be versioned.

```php
'environments' => ['production'],
```

### Next steps

* Add a method to enable multiple extensions in the same folder: Example: `jpg|png|gif' => array(...)`;
* Your ideia here.

### Changelog

##### 0.2

* Ready for Laravel 5.

##### 0.1.4

* Added a new check to verify if the asset path starts with the origin_dir config;

### Unit tests

To run the PHPUnit unit tests, clone this repository, install the dependencies with `composer install --dev` and run `vendor/bin/phpunit`.

## License

See the [License](https://github.com/EscapeWork/laravel-asset-versioning/blob/master/LICENSE) file.