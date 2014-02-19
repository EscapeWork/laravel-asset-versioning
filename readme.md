# Laravel - Assets Versioning 

[![Build Status](https://travis-ci.org/EscapeWork/laravel-asset-versioning.png)](http://travis-ci.org/EscapeWork/laravel-asset-versioning) [![Latest Stable Version](https://poser.pugx.org/escapework/laravel-asset-versioning/v/stable.png)](https://packagist.org/packages/escapework/laravel-asset-versioning) [![Total Downloads](https://poser.pugx.org/escapework/laravel-asset-versioning/downloads.png)](https://packagist.org/packages/escapework/laravel-asset-versioning)

Ever had a problem with cache in your assets? This package may help you.

## Installation

Just edit your `composer.json` file. It might look something like:

```json
    "require": {
        "laravel/framework": "4.1.*",
        "escapework/laravel-asset-versioning": "0.1.*"
    }
```

Then, run the composer update command:

```bash
$ composer update
```

After that, you just need to add the service provider to your app service providers. Open the file `app/config/app.php` and add this line:

```php
    'EscapeWork\Assets\AssetsServiceProvider'
```

Publish the configurations running the following command:

```bash
$ php artisan config:publish escapework/assets
```

Make sure your Laravel app is recognizing your local environment in the `bootstrap/start.php` file.

```php
$env = $app->detectEnvironment(array(
    'local' => array('your-machine-name'),
));
```

We us

## Usage

Instead of using the `asset` helper, you will need to use the `Asset:v` method.

Imagine that your layout template you have the following lines:

```html
<link rel="stylesheet" href="{{ Asset::v('assets/stylesheets/css/main.css') }}" />
<script src="{{ Asset::v('assets/javascripts/js/main.js') }}"></script>
```

In your **local** environment, nothing changes. But in **production**, you just need to run the following command every time you need to update your assets version:

```bash
$ php artisan asset:dist
```

And your layout will be rendered as this:

```html
<link rel="stylesheet" href="{{ Asset::v('assets/stylesheets/dist/1392745827/main.css') }}" />
<script src="{{ Asset::v('assets/javascripts/dist/1392745827/main.js') }}"></script>
```

The version is the timestamp when you performed the `asset:dist` command.

This package knows what folder you need by the file extension, which is the array key in the config file.

## Configurations

Of course you can configure the folders you need. Just edit the `app/config/packages/escapework/assets/config.php` file, in the `types` array.

```php
    'types' => array(

        'css' => array(
            'origin_dir' => 'your-custom-css-dir/css',
            'dist_dir'   => 'your-custom-css-dir/dist',
        ),

        'js' => array(
            'origin_dir' => 'your-custom-js-dir/js',
            'dist_dir'   => 'your-custom-js-dir/dist',
        ),

        'jpg' => array(
            'origin_dir' => 'assets/images',
            'dist_dir'   => 'assets/images/dist',
        ),

    ),
```

You also can add more folders by adding more items in the array.

### Next steps

* Add a method to enable múltiple extensions in the same folder: Example: `jpg|png|gif' => array(...)`;
* Your ideia here.

### Unit tests

To run the PHPUnit unit tests, clone this repository, install the dependencies with `composer install --dev` and run `vendor/bin/phpunit`.

## License

#### The MIT License (MIT)

Copyright (c) 2013 Escape Criativação LTDA

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.


