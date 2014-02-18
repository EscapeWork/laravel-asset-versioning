<?php namespace EscapeWork\Assets\Commands;

use Mockery as m;

class AssetDistCommandTest extends \PHPUnit_Framework_TestCase
{

    protected $config, $file, $app_path;

    public function setUp()
    {
        $this->config = m::mock('Illuminate\Config\Repository');
        $this->file   = m::mock('Illuminate\Filesystem\Filesystem');
        $this->paths  = array('app' => '/home', 'public' => '/home/public');
    }

    public function test_fire_method()
    {
        $oldVersion = 1; $types = array();

        $this->config->shouldReceive('get')->once()->with('assets::version')->andReturn($oldVersion);
        $this->config->shouldReceive('get')->once()->with('assets::types')->andReturn($types);

        $command = m::mock('EscapeWork\Assets\Commands\AssetDistCommand[updateConfigVersion,deleteOldDirectories,createDistDirectories]', array($this->config, $this->file, $this->paths));
        $command->shouldReceive('updateConfigVersion')->once(m::any(), $oldVersion);
        $command->shouldReceive('deleteOldDirectories')->once()->with($types, $oldVersion);
        $command->shouldReceive('createDistDirectories')->once($types, m::any());

        $command->fire();
    }

    public function test_update_config_version_with_published_config()
    {
        $configPath = $this->paths['app'] . '/config/packages/escapework/assets/config.php';

        $this->file->shouldReceive('exists')->once()->with($configPath)->andReturn(true);
        $this->file->shouldReceive('get')->once()->with($configPath)->andReturn('version=1');
        $this->file->shouldReceive('put')->once()->with($configPath, 'version=2');

        $command = new AssetDistCommand($this->config, $this->file, $this->paths);
        $command->updateConfigVersion(2, 1);
    }

    public function test_update_config_version_with_unpublished_config()
    {
        $configPath = $this->paths['app'] . '/config/packages/escapework/assets/config.php';

        $this->file->shouldReceive('exists')->once()->with($configPath)->andReturn(false);
        $this->file->shouldReceive('get')->once()->with($configPath)->andReturn('version=1');
        $this->file->shouldReceive('put')->once()->with($configPath, 'version=2');

        $command = m::mock('EscapeWork\Assets\Commands\AssetDistCommand[call]', array($this->config, $this->file, $this->paths));
        $command->shouldReceive('call')->once()->with('config:publish', array('package' => 'escapework/assets'));
        $command->updateConfigVersion(2, 1);
    }

    public function test_delete_old_directories()
    {
        $types = array(
            'css' => array('origin_dir' => 'assets/stylesheets/css', 'dist_dir' => 'assets/stylesheets/dist'),
            'js'  => array('origin_dir' => 'assets/javascripts/js', 'dist_dir' => 'assets/javascripts/dist'),
        );

        $baseDir = $this->paths['public'].'/';
        $this->file->shouldReceive('deleteDirectory')->once()->with($baseDir . $types['css']['dist_dir'] . '/1');
        $this->file->shouldReceive('deleteDirectory')->once()->with($baseDir . $types['js']['dist_dir'] . '/1');

        $command = new AssetDistCommand($this->config, $this->file, $this->paths);
        $command->deleteOldDirectories($types, 1);
    }

    public function test_create_dist_directories()
    {
        $types = array(
            'css' => array('origin_dir' => 'assets/stylesheets/css', 'dist_dir' => 'assets/stylesheets/dist'),
            'js'  => array('origin_dir' => 'assets/javascripts/js', 'dist_dir' => 'assets/javascripts/dist'),
        );

        $baseDir = $this->paths['public'].'/';

        $this->file->shouldReceive('copyDirectory')->once()->with(
            $baseDir . $types['css']['origin_dir'],
            $baseDir . $types['css']['dist_dir'] . '/2'
        );

        $this->file->shouldReceive('copyDirectory')->once()->with(
            $baseDir . $types['js']['origin_dir'],
            $baseDir . $types['js']['dist_dir'] . '/2'
        );

        $command = m::mock('EscapeWork\Assets\Commands\AssetDistCommand[info]', array($this->config, $this->file, $this->paths));
        $command->shouldReceive('info')->times(2);
        $command->createDistDirectories($types, 2);
    }

    public function tearDown()
    {
        m::close();
    }
}
