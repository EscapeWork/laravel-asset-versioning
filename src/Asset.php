<?php

namespace EscapeWork\Assets;

use Illuminate\Cache\Repository as Cache;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application as App;
use Illuminate\Support\Collection;

class Asset
{
    /**
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var Illuminate\Cache\Repository
     */
    protected $cache;

    /**
     * Links for using in HTTP2 server push
     * @var array
     */
    protected $links;

    /**
     * @var array
     */
    protected $http2Types = [
        'css'  => 'style',
        'js'   => 'script',
        'font' => 'font',
    ];

    public function __construct(App $app, Config $config, Cache $cache)
    {
        $this->app    = $app;
        $this->config = $config;
        $this->cache  = $cache;
        $this->links  = new Collection;
    }

    public function v($path)
    {
        if (! in_array($this->app->environment(), $this->config->get('assets.environments'))) {
            return $this->asset($path);
        }

        return $this->asset($this->replaceVersion($path));
    }

    public function path($extension)
    {
        $type = $this->config->get('assets.types.' . $extension);

        if ($this->app->environment() == 'local') {
            return $this->asset($type['origin_dir']);
        }

        return $this->asset($type['dist_dir']) . '/' . $this->cache->get('laravel-asset-versioning.version');
    }

    public function replaceVersion($path)
    {
        $version    = $this->cache->get('laravel-asset-versioning.version');
        $file      = explode('.', $path);
        $extension = $file[count($file) - 1];
        $types     = $this->config->get('assets.types.' . $extension);

        if (isset($types['origin_dir'])) {
            $types = [$types];
        }

        foreach ((array) $types as $type) {
            if (! $type) {
                continue;
            }

            if (! preg_match("#^\/?" . $type['origin_dir'] . "#", $path)) {
                continue;
            }

            $resource = str_replace($type['origin_dir'], $type['dist_dir'].'/' . $version, $path);
            $this->addHTTP2Link($resource, $extension);

            return $resource;
        }

        return $path;
    }

    public function asset($path)
    {
        return asset($path);
    }

    public function generateHTTP2Links()
    {
        return $this->links->map(function($link) {
            return '<'.$this->asset($link['resource']).'>; rel=preload; as='.$this->http2Type($link['type']);
        })->implode(',');
    }

    protected function http2Type($type)
    {
        if (array_key_exists($type, $this->http2Types)) {
            return $this->http2Types[$type];
        }

        return 'image';
    }

    public function hasHTTP2Links()
    {
        return $this->links->count() > 0;
    }

    public function addHTTP2Link($resource, $type)
    {
        $this->links[] = ['resource' => $resource, 'type' => $type];
    }
}
