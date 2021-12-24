<?php

namespace Pharaonic\Laravel\Jsonable;

use Pharaonic\Laravel\Jsonable\Commands\MakeRequest;
use Pharaonic\Laravel\Jsonable\Commands\MakeResource;
use Illuminate\Support\ServiceProvider;

class JsonableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Config
        // $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'modulator');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Console
        if ($this->app->runningInConsole())
            $this->console();

        // Register a binding with the container.
        $this->app->bind('json', function () {
            return new Json();
        });
    }

    /**
     * Publish Config + Register Commands
     *
     * @return void
     */
    public function console()
    {
        // // Config
        // $this->publishes([
        //     __DIR__ . '/../config/config.php' => config_path('modulator.php'),
        // ], ['config', 'pharaonic', 'modulator']);

        // MAIN COMMANDS
        $this->commands([MakeRequest::class, MakeResource::class,]);
    }
}
