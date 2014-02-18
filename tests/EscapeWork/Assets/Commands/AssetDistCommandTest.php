<?php namespace EscapeWork\Assets\Commands;

use Mockery as m;

class AssetDistCommandTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->config = m::mock('Illuminate\Config\Repository');
        $this->file   = m::mock('Illuminate\Filesystem\Filesystem');
    }

    public function test_fire_method()
    {
        $oldVersion = 1; $types = array();

        $this->config->shouldReceive('get')->once()->with('assets::version')->andReturn($oldVersion);
        $this->config->shouldReceive('get')->once()->with('assets::types')->andReturn($types);

        $command = m::mock('EscapeWork\Assets\Commands\AssetDistCommand[updateConfigVersion,deleteOldDirectories,createDistDirectories]', array($this->config, $this->file));
        $command->shouldReceive('updateConfigVersion')->once(m::any(), $oldVersion);
        $command->shouldReceive('deleteOldDirectories')->once()->with($types, $oldVersion);
        $command->shouldReceive('createDistDirectories')->once($types, m::any());

        $command->fire();
    }

    public function test_update_config_version_with_existing_config()
    {
        $configPath = app_path().'/config/packages/escapework/assets/config.php';

        $command = new AssetDistCommand($this->config, $this->file);
        $command->updateConfigVersion(2, 1);

        $this->file->shouldReceive('exists')->once()->with($configPath)->andReturn(false);
        $this->file->shouldReceive('get')->once()->with($configPath)->andReturn('version=1');
        $this->file->shouldReceive('get')->once()->with($configPath, 'version=2');
    }

    public function tearDown()
    {
        m::close();
    }
}
