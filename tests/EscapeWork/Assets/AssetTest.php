<?php namespace EscapeWork\Assets;

use Mockery as m;

class AssetTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->app    = m::mock('Illuminate\Foundation\Application');
        $this->config = m::mock('Illuminate\Config\Repository');
        $this->cache  = m::mock('Illuminate\Cache\Repository');
    }

    public function test_v_with_local_environment()
    {
        $css   = 'assets/stylesheets/css/main.css';
        $asset = m::mock('EscapeWork\Assets\Asset[asset]', array($this->app, $this->config, $this->cache));

        $this->app->shouldReceive('environment')->once()->withNoArgs()->andReturn('local');
        $this->config->shouldReceive('get')->with('assets.environments')->andReturn(['prodution']);
        $asset->shouldReceive('asset')->once()->with($css)->andReturn('/' . $css);

        $this->assertEquals('/' . $css, $asset->v($css));
    }

    public function test_v_with_production_environment()
    {
        $css   = 'assets/stylesheets/css/main.css';
        $asset = m::mock('EscapeWork\Assets\Asset[replaceVersion,asset]', array($this->app, $this->config, $this->cache));

        $this->app->shouldReceive('environment')->once()->withNoArgs()->andReturn('production');
        $this->config->shouldReceive('get')->with('assets.environments')->andReturn(['production']);
        $asset->shouldReceive('replaceVersion')->once()->with($css)->andReturn('assets/stylesheets/dist/12345/main.css');
        $asset->shouldReceive('asset')->once()->with('assets/stylesheets/dist/12345/main.css')->andReturn('/assets/stylesheets/dist/12345/main.css');

        $this->assertEquals('/assets/stylesheets/dist/12345/main.css', $asset->v($css));
    }

    public function test_replace_version_with_existing_extension()
    {
        $dirs = array('origin_dir' => 'assets/stylesheets/css', 'dist_dir' => 'assets/stylesheets/dist');
        $this->cache->shouldReceive('get')->once()->with('laravel-asset-versioning.version')->andReturn('0.0.1');
        $this->config->shouldReceive('get')->once()->with('assets.types.css')->andReturn($dirs);

        $asset = new Asset($this->app, $this->config, $this->cache);
        $this->assertEquals('assets/stylesheets/dist/0.0.1/main.css', $asset->replaceVersion('assets/stylesheets/css/main.css'));
    }

    public function test_replace_version_with_non_existing_extension()
    {
        $this->cache->shouldReceive('get')->once()->with('laravel-asset-versioning.version')->andReturn('0.0.1');
        $this->config->shouldReceive('get')->once()->with('assets.types.css')->andReturn(null);

        $asset = new Asset($this->app, $this->config, $this->cache);
        $this->assertEquals('assets/stylesheets/css/main.css', $asset->replaceVersion('assets/stylesheets/css/main.css'));
    }

    public function test_replace_version_with_non_valid_origin_dir()
    {
        $this->cache->shouldReceive('get')->once()->with('laravel-asset-versioning.version')->andReturn('0.0.1');
        $this->config->shouldReceive('get')->once()->with('assets.types.css')->andReturn(array(
            'origin_dir' => 'assets/stylesheets/css', 
            'dist_dir'   => 'assets/stylesheets/dist', 
        ));

        $asset = new Asset($this->app, $this->config, $this->cache);
        $this->assertEquals(
            'packages/escapework/manager/assets/stylesheets/css/main.css', 
            $asset->replaceVersion('packages/escapework/manager/assets/stylesheets/css/main.css')
        );
    }

     public function test_path_method_in_local_environment()
    {
        $this->app->shouldReceive('environment')->andReturn('local');
        $this->config->shouldReceive('get')->once()->with('assets.types.html')->andReturn(array(
            'origin_dir' => 'templates'
        ));
        $asset = m::mock('EscapeWork\Assets\Asset[asset]', array($this->app, $this->config, $this->cache));
        $asset->shouldReceive('asset')->once()->with('templates')->andReturn('templates');
        $this->assertEquals(
            $asset->path('html'), 
            'templates'
        );
    }
    public function test_path_method_in_production_environment()
    {
        $this->app->shouldReceive('environment')->andReturn('production');
        $this->cache->shouldReceive('get')->once()->with('laravel-asset-versioning.version')->andReturn('12345');
        $this->config->shouldReceive('get')->once()->with('assets.types.html')->andReturn(array(
            'dist_dir' => 'templates/dist'
        ));
        $asset = m::mock('EscapeWork\Assets\Asset[asset]', array($this->app, $this->config, $this->cache));
        $asset->shouldReceive('asset')->once()->with('templates/dist')->andReturn('templates/dist');
        
        $this->assertEquals(
            $asset->path('html'), 
            'templates/dist/12345'
        );
    }

    public function tearDown()
    {
        m::close();
    }
}
