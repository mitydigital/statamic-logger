<?php

namespace MityDigital\StatamicLogger;

use Illuminate\Routing\Router;
use MityDigital\StatamicLogger\Console\Commands\ListSubscribedEventsCommand;
use MityDigital\StatamicLogger\Http\CP\Controllers\StatamicLoggerController;
use MityDigital\StatamicLogger\Subscribers\StatamicLoggerEventSubscriber;
use MityDigital\StatamicLogger\Support\LoggerFormatter;
use MityDigital\StatamicLogger\Support\StatamicLogger;
use Statamic\Facades\Utility;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        ListSubscribedEventsCommand::class,
    ];

    protected $subscribe = [
        StatamicLoggerEventSubscriber::class,
    ];

    protected $vite = [
        'input' => [
            'resources/js/cp.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function boot()
    {
        // we need config now, not in bootAddon
        // this is because bootEvent is called first, which will set up the subscriber - but we need
        // the config to be able to do that correctly
        $this->mergeConfigFrom(__DIR__.'/../config/statamic-logger.php', 'statamic-logger');

        parent::boot();
    }

    public function bootAddon()
    {
        //
        // set up the facade
        //
        $this->app->bind('StatamicLogger', function () {
            return new StatamicLogger();
        });

        //
        // set up the Utility
        //
        $this->configureUtility();

        //
        // configure the logger
        //
        $this->configureLogger();

        //
        // publish views
        //
        $this->publishes([
            __DIR__.'/../resources/views/listeners' => resource_path('views/vendor/statamic-logger/listeners'),
        ], 'statamic-logger-listener-views');
    }

    protected function configureUtility($utility = 'statamic-logger')
    {
        if (Facades\StatamicLogger::enabled()) {
            Utility::extend(fn () => Utility::register($utility)
                ->title(__('statamic-logger::utility.title'))
                ->navTitle(__('statamic-logger::utility.nav_title'))
                ->icon(file_get_contents($this->getAddon()->directory().'resources/icons/logger.svg'))
                ->description(__('statamic-logger::utility.description'))
                ->docsUrl('https://docs.mity.com.au')
                ->routes(function (Router $router) {
                    $router->get('/', [StatamicLoggerController::class, 'show'])
                        ->name('show');
                    $router->get('/download/{date}', [StatamicLoggerController::class, 'download'])
                        ->name('download')
                        ->where(['date' => '\d{4}-\d{2}-\d{2}']);
                })
            );
        }
    }

    protected function configureLogger($channel = 'statamic-logger')
    {
        if (Facades\StatamicLogger::enabled()) {
            // do we have a custom path?
            $path = Facades\StatamicLogger::getStoragePath();

            // get the filename
            $filename = Facades\StatamicLogger::getStorageFilename().'.log';

            // configure and create the logging channel
            config()->set('logging.channels.'.$channel, [
                'driver' => 'daily',
                'path' => storage_path($path.DIRECTORY_SEPARATOR.$filename),
                'level' => 'debug',
                'days' => config('statamic-logger.storage.retention', 7),
                'tap' => [LoggerFormatter::class],
            ]);
        }
    }
}
