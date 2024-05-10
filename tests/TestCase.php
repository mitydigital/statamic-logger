<?php

namespace MityDigital\StatamicLogger\Tests;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use MityDigital\StatamicLogger\ServiceProvider;
use Statamic\Facades\Site;
use Statamic\Statamic;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected $shouldFakeVersion = true;

    protected string $addonServiceProvider = ServiceProvider::class;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

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

    public function getTempDirectory($suffix = ''): string
    {
        return __DIR__.'/TestSupport/'.($suffix == '' ? '' : '/'.$suffix);
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configs = [
            'assets',
            'cp',
            'forms',
            'static_caching',
            'stache',
            'system',
            'users',
        ];

        foreach ($configs as $config) {
            $app['config']->set(
                "statamic.$config",
                require (__DIR__."/../vendor/statamic/cms/config/{$config}.php")
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

        Statamic::booted(function () {
            // configure to be an AU site
            Site::setSites([
                'default' => [
                    'name' => config('app.name'),
                    'locale' => 'en_AU',
                    'url' => '/',
                ],
            ]);
        });
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->getTempDirectory());

        parent::tearDown();
    }
}
