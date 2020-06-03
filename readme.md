# Laravel - Assets Versioning

<p align="center">
<a href="https://packagist.org/packages/escapework/laravel-asset-versioning"><img src="https://poser.pugx.org/escapework/laravel-asset-versioning/v/stable.png" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/escapework/laravel-asset-versioning"><img src="https://poser.pugx.org/escapework/laravel-asset-versioning/downloads.png" alt="Downloads"></a>
<a href="https://travis-ci.org/EscapeWork/laravel-asset-versioning"><img src="https://travis-ci.org/EscapeWork/laravel-asset-versioning.png" alt="Build Status"></a>
<a href="https://github.com/EscapeWork/laravel-asset-versioning"><img src="https://img.shields.io/packagist/l/orchestra/testbench.svg?style=flat" alt="License MIT"></a>
</p>

**Disclaimer:** This package is not really maintained anymore, we recommend you use the Laravel Mix versioning instead.

Have you ever had a problem with cache in your assets? This package may help you.

## Version Compatibility

 Laravel           | Laravel Assets Versioning
:------------------|:------------------------
 6.x/7.x/8.x       | 0.8.x
 5.5+              | 0.7.x
 5.4.x             | 0.6.x
 5.3.x             | 0.5.x
 5.2.x             | 0.4.x
 5.1.x             | 0.3.x
 5.0.x             | 0.2.x
 4.2.x             | 0.1.x

## Installation

Via Composer:

```
$ composer require escapework/laravel-asset-versioning:"0.7.*"
```

And publish the configurations running the following command:

```bash
$ php artisan vendor:publish --provider="EscapeWork\Assets\AssetsServiceProvider"
```

## Usage

Instead of using the `asset` helper, you will need to use the `Asset:v` method.

Imagine that your layout template has the following lines:

```html
<link rel="stylesheet" href="{{ Asset::v('assets/stylesheets/css/main.css') }}">
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

### HTTP2 Server Push

You can also enable the HTTP2 Server Push header for all assets used with this package.

For that, you need to add the `HTTP2ServerPush` to the middlewares of your application.

```php
protected $middleware = [
    \EscapeWork\Assets\Middleware\HTTP2ServerPush::class,
];
```

And that's it, your response will come with the `Link` HTTP header.

If you want to add some assets that are not versioned, you can use this method:

```php
Asset::addHTTP2Link('/assets/fonts/robotto.woff', 'font');
Asset::addHTTP2Link('/assets/css/home.css', 'css');
Asset::addHTTP2Link('/assets/js/home.js', 'js');
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

### Changelog

See [Changelog](https://github.com/EscapeWork/laravel-asset-versioning/blob/master/changelog.md).

### Unit tests

Just run `vendor/bin/phpunit`.

## License

See the [License](https://github.com/EscapeWork/laravel-asset-versioning/blob/master/LICENSE) file.
