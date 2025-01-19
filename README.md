# Crumbls/Pipeline

A powerful, state-aware pipeline implementation for Laravel with job-based execution support. This package extends Laravel's pipeline system to add state management, job queuing, progress tracking, and advanced error recovery capabilities.

## Features

- 🔄 State tracking for each pipeline step
- 📊 Progress monitoring and metadata storage
- 🔌 Middleware support for cross-cutting concerns
- 💾 State persistence with multiple storage options
- 🔄 Configurable retry logic and error recovery
- ⚡ Support for parallel execution
- 📝 Comprehensive event system
- 🎯 Type-safe configuration
- 🚦 Rate limiting support
- 💪 Pipeline health monitoring
- 📦 Batch processing
- 🎨 Pipeline templates

## Installation

```bash
composer require crumbls/pipeline
```

The package will automatically register its service provider.

## Basic Usage

### Simple Pipeline

```php
use Crumbls\Pipeline\StatefulPipeline;

$result = app('stateful-pipeline')
    ->send($data)
    ->through([
        ProcessDataPipe::class,
        ValidateResultsPipe::class,
        SaveResultsPipe::class,
    ])
    ->thenReturn();
```

### Queued Pipeline

```php
$job = app('stateful-pipeline')
    ->send($data)
    ->through([
        ProcessDataPipe::class,
        ValidateResultsPipe::class,
        SaveResultsPipe::class,
    ])
    ->dispatchAsPipeline();
```

## Advanced Usage

### Rate Limited Pipes

```php
use Crumbls\Pipeline\RateLimit\RateLimitedPipe;

class ApiProcessingPipe extends RateLimitedPipe
{
    protected int $requestsPerMinute = 30;

    protected function process($passable): mixed
    {
        // Your API processing logic here
        return $processedData;
    }
}
```

### Health Monitoring

```php
use Crumbls\Pipeline\Health\PipelineHealthCheck;

$healthCheck = app(PipelineHealthCheck::class);
$health = $healthCheck->check($pipelineId);

// Health report includes:
[
    'status' => 'in_progress',
    'duration' => 45.2,
    'progress' => 75.0,
    'bottlenecks' => [
        ['step' => 2, 'duration' => 30.5, 'threshold' => 20]
    ],
    'resourceUsage' => [
        'memoryPeak' => 256000000,
        'totalSteps' => 4,
        'completedSteps' => 3,
        'failedSteps' => 0
    ]
]
```

### Batch Processing

```php
use Crumbls\Pipeline\StatefulPipeline;

$pipeline = app('stateful-pipeline')
    ->through([
        ProcessChunkPipe::class,
        ValidateChunkPipe::class
    ]);

$pipeline->batch([
    [$data1, $data2],
    [$data3, $data4]
])
->then(function ($batch) {
    Log::info('All chunks processed', [
        'total_chunks' => $batch->totalJobs,
        'failed_chunks' => $batch->failedJobs
    ]);
})
->catch(function ($batch, $exception) {
    Log::error('Batch processing failed', [
        'error' => $exception->getMessage()
    ]);
})
->dispatch();
```

### Using Templates

```php
use Crumbls\Pipeline\Templates\ImportDataTemplate;
use Crumbls\Pipeline\Config\PipelineConfig;

// Use default template configuration
$pipeline = (new ImportDataTemplate())->build();

// Or customize the configuration
$pipeline = (new ImportDataTemplate())->withConfig(
    new PipelineConfig(
        retryAttempts: 5,
        timeout: 7200,
        persistState: true,
        middleware: [
            LoggingMiddleware::class
        ]
    )
);

// Execute the pipeline
$result = $pipeline->send($data)->thenReturn();
```

### Creating Custom Templates

```php
use Crumbls\Pipeline\Templates\PipelineTemplate;

class DataProcessingTemplate extends PipelineTemplate
{
    protected function getPipes(): array
    {
        return [
            ValidateInputPipe::class,
            EnrichDataPipe::class,
            TransformDataPipe::class,
            ValidateOutputPipe::class,
            SaveResultsPipe::class
        ];
    }
    
    protected function getDefaultConfig(): PipelineConfig
    {
        return new PipelineConfig(
            retryAttempts: 3,
            timeout: 3600,
            persistState: true,
            middleware: [
                ValidationMiddleware::class,
                LoggingMiddleware::class
            ]
        );
    }
}
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@crumbls.com instead of using the issue tracker.

## Credits

- [Your Name](https://github.com/yourusername)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
