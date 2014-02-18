<?php namespace EscapeWork\Assets\Commands;

use Illuminate\Console\Command;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem as File;
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
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $file;

    /**
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Config $config, File $file)
    {
        parent::__construct();

        $this->config = $config;
        $this->file   = $file;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $types      = $this->config->get('assets::types');
        $oldVersion = $this->config->get('assets::version');
        $newVersion = Carbon::now()->timestamp;

        $this->updateConfigVersion($newVersion, $oldVersion);
        $this->deleteOldDirectories($types, $oldVersion);
        $this->createDistDirectories($types, $newVersion);
    }

    public function updateConfigVersion($newVersion, $oldVersion)
    {
        $configPath = app_path().'/config/packages/escapework/assets/config.php';

        if (! $this->file->exists($configPath)) {
            $this->call('config:publish', array('package' => 'escapework/assets'));
        }

        $configFile    = $this->file->get($configPath);
        $newConfigFile = str_replace($oldVersion, $newVersion, $configFile);

        $this->file->put($configPath, $newConfigFile);
    }

    public function deleteOldDirectories($types, $oldVersion)
    {
        foreach ($types as $type => $directories) {
            $dir = public_path().'/'.$directories['dist_dir'].'/'.$oldVersion;

            $this->file->deleteDirectory($dir);
        }
    }

    public function createDistDirectories($types, $version)
    {
        foreach ($types as $type => $directories) {
            $origin_dir = public_path().'/'.$directories['origin_dir'];
            $dist_dir   = public_path().'/'.$directories['dist_dir'].'/'.$version;

            $this->file->copyDirectory($origin_dir, $dist_dir);

            $this->info($type . ' dist dir ('.$dist_dir.') successfully created!');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}
