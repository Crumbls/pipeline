<?php

namespace Crumbls\Pipeline;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;

class PipelineServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('stateful-pipeline', function (Container $app) {
            return new StatefulPipeline($app);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/pipeline.php' => config_path('pipeline.php'),
        ], 'pipeline-config');
    }
}
