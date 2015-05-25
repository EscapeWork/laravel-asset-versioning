<?php namespace EscapeWork\Assets\Console;

use Mockery as m;

class AssetDistCommandTest extends \PHPUnit_Framework_TestCase
{

    protected $config, $linker, $app_path;

    public function setUp()
    {
        $this->config = m::mock('Illuminate\Config\Repository');
        $this->linker = m::mock('EscapeWork\Assets\SymLinker');
        $this->cache  = m::mock('Illuminate\Cache\Repository');
        $this->paths  = array('app' => '/home', 'public' => '/home/public');
    }

    public function test_fire_method()
    {
        $oldVersion = 1; $types = array();

        $this->config->shouldReceive('get')->once()->with('assets.types')->andReturn($types);
        $this->cache->shouldReceive('get')->once()->with('laravel-asset-versioning.version')->andReturn('12345');

        $command = m::mock('EscapeWork\Assets\Console\AssetDistCommand[updateConfigVersion,unlinkOldDirectories,createDistDirectories]', array($this->config, $this->linker, $this->cache, $this->paths));
        $command->shouldReceive('updateConfigVersion')->once(m::any(), $oldVersion);
        $command->shouldReceive('unlinkOldDirectories')->once()->with($types, '12345');
        $command->shouldReceive('createDistDirectories')->once($types, m::any());

        $command->fire();
    }

    public function test_update_config_version_with_published_config()
    {
        $this->cache->shouldReceive('forever')->once()->with('laravel-asset-versioning.version', 2);

        $command = new AssetDistCommand($this->config, $this->linker, $this->cache, $this->paths);
        $command->updateConfigVersion(2);
    }

    public function test_unlink_old_directories()
    {
        $types = array(
            'css' => array('origin_dir' => 'assets/stylesheets/css', 'dist_dir' => 'assets/stylesheets/dist'),
            'js'  => array('origin_dir' => 'assets/javascripts/js', 'dist_dir' => 'assets/javascripts/dist'),
        );

        $baseDir = $this->paths['public'].'/';
        $this->linker->shouldReceive('unlink')->once()->with($baseDir . $types['css']['dist_dir'] . '/12345');
        $this->linker->shouldReceive('unlink')->once()->with($baseDir . $types['js']['dist_dir'] . '/12345');

        $command = new AssetDistCommand($this->config, $this->linker, $this->cache, $this->paths);
        $command->unlinkOldDirectories($types, '12345');
    }

    public function test_create_dist_directories()
    {
        $types = array(
            'css' => array('origin_dir' => 'assets/stylesheets/css', 'dist_dir' => 'assets/stylesheets/dist'),
            'js'  => array('origin_dir' => 'assets/javascripts/js', 'dist_dir' => 'assets/javascripts/dist'),
        );

        $baseDir = $this->paths['public'].'/';

        $this->linker->shouldReceive('link')->once()->with(
            $baseDir . $types['css']['origin_dir'],
            $baseDir . $types['css']['dist_dir'] . '/2'
        );

        $this->linker->shouldReceive('link')->once()->with(
            $baseDir . $types['js']['origin_dir'],
            $baseDir . $types['js']['dist_dir'] . '/2'
        );

        $command = m::mock('EscapeWork\Assets\Console\AssetDistCommand[info]', array($this->config, $this->linker, $this->cache, $this->paths));
        $command->shouldReceive('info')->times(2);
        $command->createDistDirectories($types, 2);
    }

    public function tearDown()
    {
        m::close();
    }
}
