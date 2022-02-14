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
        // 
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
        // MAIN COMMANDS
        $this->commands([MakeRequest::class, MakeResource::class,]);
    }
}
