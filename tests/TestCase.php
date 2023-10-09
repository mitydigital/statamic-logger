<?php

namespace MityDigital\StatamicLogger\Tests;

use Facades\Statamic\Version;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use MityDigital\StatamicLogger\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Console\Processes\Composer;
use Statamic\Extend\Manifest;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;

abstract class TestCase extends OrchestraTestCase
{
    protected $shouldFakeVersion = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        if ($this->shouldFakeVersion) {
            Version::shouldReceive('get')
                ->andReturn(Composer::create(__DIR__.'/../')->installedVersion(Statamic::PACKAGE));
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(Manifest::class)->manifest = [
            'mitydigital/statamic-logger' => [
                'id' => 'mitydigital/statamic-logger',
                'namespace' => 'MityDigital\\StatamicLogger',
            ],
        ];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configs = [
            'assets',
            'cp',
            'forms',
            'static_caching',
            'sites',
            'stache',
            'system',
            'users',
        ];

        foreach ($configs as $config) {
            $app['config']->set(
                "statamic.$config",
                require(__DIR__."/../vendor/statamic/cms/config/{$config}.php")
            );
        }

        $app['config']->set('app.key', 'base64:'.base64_encode(
            Encrypter::generateKey($app['config']['app.cipher'])
        ));

        $app['config']->set('filesystems.disks.assets', [
            'driver' => 'local',
            'root' => $this->getTempDirectory('/content/assets'),
            'url' => '/assets',
            'visibility' => 'public',
        ]);

        $app['config']->set('auth.providers.users.driver', 'statamic');
        $app['config']->set('statamic.users.repository', 'file');

        // assets and asset containers
        $app['config']->set('statamic.stache.stores.asset-containers.directory',
            $this->getTempDirectory('/content/assets'));

        // collections, entries and collection trees
        $app['config']->set('statamic.stache.stores.collections.directory',
            $this->getTempDirectory('/content/collections'));
        $app['config']->set('statamic.stache.stores.collection-trees.directory',
            $this->getTempDirectory('/content/collection-trees'));
        $app['config']->set('statamic.stache.stores.entries.directory',
            $this->getTempDirectory('/content/collections'));

        // taxonomies and terms
        $app['config']->set('statamic.stache.stores.taxonomies.directory',
            $this->getTempDirectory('/content/taxonomies'));
        $app['config']->set('statamic.stache.stores.terms.directory',
            $this->getTempDirectory('/content/terms'));

        // navigation and nav-trees
        $app['config']->set('statamic.stache.stores.navigation.directory',
            $this->getTempDirectory('/content/nav'));
        $app['config']->set('statamic.stache.stores.nav-trees.directory',
            $this->getTempDirectory('/content/nav'));

        // globals
        $app['config']->set('statamic.stache.stores.globals.directory',
            $this->getTempDirectory('/content/globals'));
        $app['config']->set('statamic.stache.stores.global-variables.directory',
            $this->getTempDirectory('/content/globals'));

        // users
        $app['config']->set('statamic.stache.stores.users.directory',
            $this->getTempDirectory('/content/users'));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->getTempDirectory());

        parent::tearDown();
    }

    public function getTempDirectory($suffix = ''): string
    {
        return __DIR__.'/TestSupport/'.($suffix == '' ? '' : '/'.$suffix);
    }
}
