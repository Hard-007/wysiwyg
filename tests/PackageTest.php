<?php

namespace FlexWave\Wysiwyg\Tests;

use FlexWave\Wysiwyg\WysiwygServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class PackageTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [WysiwygServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('flexwave-wysiwyg.middleware', ['web']);
        $app['config']->set('flexwave-wysiwyg.image_resize.enabled', false);
    }

    public function test_package_registers_expected_routes(): void
    {
        $this->assertTrue(Route::has('flexwave-wysiwyg.upload'));
        $this->assertTrue(Route::has('flexwave-wysiwyg.upload.delete'));
    }

    public function test_editor_component_renders_with_package_defaults(): void
    {
        $html = Blade::render('<x-flexwave-editor name="content" placeholder="Write here" />');

        $this->assertStringContainsString('fw-wysiwyg-wrapper', $html);
        $this->assertStringContainsString('data-fw-editor=', $html);
        $this->assertStringContainsString('Write here', $html);
    }
}