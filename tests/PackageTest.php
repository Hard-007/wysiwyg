<?php

namespace FlexWave\Wysiwyg\Tests;

use FlexWave\Wysiwyg\WysiwygServiceProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;

class PackageTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [WysiwygServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('flexwave-wysiwyg.upload.disk', 'testing');
        $app['config']->set('flexwave-wysiwyg.middleware', ['web']);
        $app['config']->set('flexwave-wysiwyg.image_resize.enabled', false);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('testing');
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

    public function test_upload_and_delete_flow_works_with_package_routes(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $uploadResponse = $this->postJson(route('flexwave-wysiwyg.upload'), ['file' => $file]);

        $uploadResponse->assertOk();
        $uploadResponse->assertJson(['success' => true]);

        $path = $uploadResponse->json('path');

        $this->assertNotEmpty($path);
        Storage::disk('testing')->assertExists($path);

        $deleteResponse = $this->deleteJson(route('flexwave-wysiwyg.upload.delete'), [
            'path' => $path,
        ]);

        $deleteResponse->assertOk();
        $deleteResponse->assertJson(['success' => true]);
        Storage::disk('testing')->assertMissing($path);
    }
}