<?php 

namespace EscapeWork\Assets\Console;

use EscapeWork\Assets\SymLinker;
use Illuminate\Console\Command;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Config\Repository as Config;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Carbon\Carbon;

class AssetDistCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'asset:dist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a dist folder for your assets to avoid cache';

    /**
     * @var EscapeWork\Assets\SymLinker
     */
    protected $linker;

    /**
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var Illuminate\Cache\Repository
     */
    protected $cache;

    /**
     * @var array
     */
    protected $paths;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Config $config, SymLinker $linker, Cache $cache, $paths)
    {
        parent::__construct();

        $this->config = $config;
        $this->linker = $linker;
        $this->cache  = $cache;
        $this->paths  = $paths;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $types      = $this->config->get('assets.types');
        $oldVersion = $this->cache->get('laravel-asset-versioning.version');
        $version    = Carbon::now()->timestamp;

        $this->updateConfigVersion($version);
        $this->unlinkOldDirectories($types, $oldVersion);
        $this->createDistDirectories($types, $version);
    }

    public function updateConfigVersion($version)
    {
        $this->cache->forever('laravel-asset-versioning.version', $version);
    }

    public function unlinkOldDirectories($types, $oldVersion)
    {
        if (! $oldVersion) {
            return;
        }
        
        foreach ($types as $type => $directories) {
            if (isset($directories['origin_dir'])) {
                $directories = [$directories];
            }

            foreach ($directories as $directory) {
                $dir = $this->paths['public'] . '/' . $directory['dist_dir'] . '/' . $oldVersion;

                $this->linker->unlink($dir);
            }
        }
    }

    public function createDistDirectories($types, $version)
    {
        foreach ($types as $type => $directories) {
            if (isset($directories['origin_dir'])) {
                $directories = [$directories];
            }

            foreach ($directories as $directory) {
                $origin_dir = $this->paths['public'].'/'.$directory['origin_dir'];
                $dist_dir   = $this->paths['public'].'/'.$directory['dist_dir'].'/'.$version;

                $this->linker->link($origin_dir, $dist_dir);
                $this->info($type . ' dist dir ('.$dist_dir.') successfully created!');
            }
        }
    }
}
