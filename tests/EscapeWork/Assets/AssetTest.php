<?php namespace EscapeWork\Assets;

use Mockery as m;

class AssetTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->app    = m::mock('Illuminate\Foundation\Application');
        $this->config = m::mock('Illuminate\Config\Repository');
    }

    public function test_v_with_local_environment()
    {
        $css   = 'assets/stylesheets/css/main.css';
        $asset = m::mock('EscapeWork\Assets\Asset[asset]', array($this->app, $this->config));

        $this->app->shouldReceive('environment')->once()->withNoArgs()->andReturn('local');
        $asset->shouldReceive('asset')->once()->with($css)->andReturn('/' . $css);

        $this->assertEquals('/' . $css, $asset->v($css));
    }

    public function test_v_with_production_environment()
    {
        $css   = 'assets/stylesheets/css/main.css';
        $asset = m::mock('EscapeWork\Assets\Asset[replaceVersion,asset]', array($this->app, $this->config));

        $this->app->shouldReceive('environment')->once()->withNoArgs()->andReturn('production');
        $asset->shouldReceive('replaceVersion')->once()->with($css)->andReturn('assets/stylesheets/dist/12345/main.css');
        $asset->shouldReceive('asset')->once()->with('assets/stylesheets/dist/12345/main.css')->andReturn('/assets/stylesheets/dist/12345/main.css');

        $this->assertEquals('/assets/stylesheets/dist/12345/main.css', $asset->v($css));
    }

    public function test_replace_version_with_existing_extension()
    {
        $dirs = array('origin_dir' => 'assets/stylesheets/css', 'dist_dir' => 'assets/stylesheets/dist');
        $this->config->shouldReceive('get')->once()->with('laravel-asset-versioning::version')->andReturn('0.0.1');
        $this->config->shouldReceive('get')->once()->with('laravel-asset-versioning::types.css')->andReturn($dirs);

        $asset = new Asset($this->app, $this->config);
        $this->assertEquals('assets/stylesheets/dist/0.0.1/main.css', $asset->replaceVersion('assets/stylesheets/css/main.css'));
    }

    public function test_replace_version_with_non_existing_extension()
    {
        $this->config->shouldReceive('get')->once()->with('laravel-asset-versioning::version')->andReturn('0.0.1');
        $this->config->shouldReceive('get')->once()->with('laravel-asset-versioning::types.css')->andReturn(null);

        $asset = new Asset($this->app, $this->config);
        $this->assertEquals('assets/stylesheets/css/main.css', $asset->replaceVersion('assets/stylesheets/css/main.css'));
    }

    public function test_replace_version_with_non_valid_origin_dir()
    {
        $this->config->shouldReceive('get')->once()->with('laravel-asset-versioning::version')->andReturn('0.0.1');
        $this->config->shouldReceive('get')->once()->with('laravel-asset-versioning::types.css')->andReturn([
            'origin_dir' => 'assets/stylesheets/css', 
            'dist_dir'   => 'assets/stylesheets/dist', 
        ]);

        $asset = new Asset($this->app, $this->config);
        $this->assertEquals(
            'packages/escapework/manager/assets/stylesheets/css/main.css', 
            $asset->replaceVersion('packages/escapework/manager/assets/stylesheets/css/main.css')
        );
    }

    public function tearDown()
    {
        m::close();
    }
}
