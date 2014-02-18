<?php namespace EscapeWork\Assets;

use Illuminate\Foundation\Application as App;
use Illuminate\Config\Repository as Config;

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

    public function __construct(App $app, Config $config)
    {
        $this->app    = $app;
        $this->config = $config;
    }

    public function v($path)
    {
        if ($this->app->environment() == 'local') {
            return $this->asset($path);
        }

        return $this->asset($this->replaceVersion($path));
    }

    public function replaceVersion($path)
    {
        $version   = $this->config->get('assets::version');
        $file      = explode('.', $path);
        $extension = $file[count($file) - 1];
        $type      = $this->config->get('assets::types.' . $extension);

        if (! $type) {
            return $path;
        }

        return str_replace($type['origin_dir'], $type['dist_dir'].'/' . $version, $path);
    }

    public function asset($path)
    {
        return asset($path);
    }
}
