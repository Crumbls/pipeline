<?php
namespace Crumbls\Pipeline\Templates;

use Crumbls\Pipeline\Config\PipelineConfig;

abstract class PipelineTemplate
{
    abstract protected function getPipes(): array;

    abstract protected function getDefaultConfig(): PipelineConfig;

    public function build(): StatefulPipeline
    {
        return app('stateful-pipeline')
            ->withConfig($this->getDefaultConfig())
            ->through($this->getPipes());
    }

    public function withConfig(PipelineConfig $config): StatefulPipeline
    {
        return $this->build()->withConfig($config);
    }
}
