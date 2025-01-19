<?php

namespace Crumbls\Pipeline;

use Crumbls\Pipeline\State\StateStoreInterface;
use Crumbls\Pipeline\Stores\CacheStateStore;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;

class PipelineServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('stateful-pipeline', function (Container $app) {
            return new StatefulPipeline($app);
        });
        $this->app->bind(StateStoreInterface::class, CacheStateStore::class);

    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/pipeline.php' => config_path('pipeline.php'),
        ], 'pipeline-config');
    }
}
