<?php

namespace Xpress7\BoxMaker;

use Illuminate\Support\ServiceProvider;
use Xpress7\BoxMaker\Commands\CreateBox;

class BoxMakerServiceProvider extends ServiceProvider
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
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateBox::class,
            ]);
        }
    }
}
