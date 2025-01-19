<?php
namespace Crumbls\Pipeline\Templates;

use Crumbls\Pipeline\Config\PipelineConfig;

class ImportDataTemplate extends PipelineTemplate
{
    protected function getPipes(): array
    {
        return [
            ValidateDataPipe::class,
            TransformDataPipe::class,
            ImportDataPipe::class,
            CleanupPipe::class
        ];
    }

    protected function getDefaultConfig(): PipelineConfig
    {
        return new PipelineConfig(
            retryAttempts: 3,
            timeout: 3600,
            persistState: true
        );
    }
}
