##### 0.4

* Added the option to enable HTTP2 Server Push links in all assets that you add.

##### 0.3

* Added a `Asset::path()` method to know the dist path for some extension (Backported to `0.2` and `0.1`);
* Now it created a symlink instead copying directories. [#3](https://github.com/EscapeWork/laravel-asset-versioning/issues/3);
* Added a `environments` option to configure which environments should version. [#4](https://github.com/EscapeWork/laravel-asset-versioning/issues/4);

##### 0.2

* Ready for Laravel 5.

##### 0.1.4

* Added a new check to verify if the asset path starts with the origin_dir config;