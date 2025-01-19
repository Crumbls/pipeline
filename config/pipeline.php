<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default State Store
    |--------------------------------------------------------------------------
    |
    | The default state store to use for persisting pipeline state.
    | Supported: "cache", "database"
    |
    */
    'state_store' => env('PIPELINE_STATE_STORE', 'cache'),

    /*
    |--------------------------------------------------------------------------
    | Database Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the database state store.
    |
    */
    'database' => [
        'connection' => env('PIPELINE_DB_CONNECTION', null),
        'table' => env('PIPELINE_STATE_TABLE', 'pipeline_states'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the cache state store.
    |
    */
    'cache' => [
        'store' => env('PIPELINE_CACHE_STORE', null),
        'prefix' => env('PIPELINE_CACHE_PREFIX', 'pipeline_state_'),
        'ttl' => env('PIPELINE_CACHE_TTL', 86400),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    |
    | Default queue settings for pipeline jobs.
    |
    */
    'queue' => [
        'connection' => env('PIPELINE_QUEUE_CONNECTION', null),
        'queue' => env('PIPELINE_QUEUE', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Pipeline Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration values for all pipelines.
    |
    */
    'defaults' => [
        'retry_attempts' => env('PIPELINE_RETRY_ATTEMPTS', 3),
        'timeout' => env('PIPELINE_TIMEOUT', 3600),
        'allow_parallel' => env('PIPELINE_ALLOW_PARALLEL', false),
        'persist_state' => env('PIPELINE_PERSIST_STATE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Global middleware that should be applied to all pipelines.
    |
    */
    'middleware' => [
        // \App\Pipeline\Middleware\LoggingMiddleware::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Checks
    |--------------------------------------------------------------------------
    |
    | Configuration for pipeline health checks.
    |
    */
    'health' => [
        'enabled' => env('PIPELINE_HEALTH_ENABLED', true),
        'step_timeout' => env('PIPELINE_STEP_TIMEOUT', 300),
        'metrics' => [
            'memory_threshold' => env('PIPELINE_MEMORY_THRESHOLD', 128 * 1024 * 1024), // 128MB
            'step_duration_threshold' => env('PIPELINE_STEP_DURATION_THRESHOLD', 60), // 60 seconds
        ],
        'alerts' => [
            'channels' => ['slack', 'email'],
            'notification_class' => \App\Notifications\PipelineHealthAlert::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Global rate limiting configuration for pipelines.
    |
    */
    'rate_limiting' => [
        'enabled' => env('PIPELINE_RATE_LIMITING_ENABLED', true),
        'default_requests_per_minute' => env('PIPELINE_RATE_LIMIT', 60),
        'decay_minutes' => env('PIPELINE_RATE_LIMIT_DECAY', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Processing
    |--------------------------------------------------------------------------
    |
    | Configuration for batch processing features.
    |
    */
    'batch' => [
        'allowed_failures' => env('PIPELINE_BATCH_ALLOWED_FAILURES', 0),
        'max_jobs' => env('PIPELINE_BATCH_MAX_JOBS', 100),
        'max_exceptions' => env('PIPELINE_BATCH_MAX_EXCEPTIONS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Pre-configured pipeline templates that can be used as starting points.
    |
    */
    'templates' => [
        'import' => [
            'class' => \App\Pipeline\Templates\ImportDataTemplate::class,
            'config' => [
                'retry_attempts' => 3,
                'timeout' => 3600,
                'persist_state' => true,
            ],
        ],
        'export' => [
            'class' => \App\Pipeline\Templates\ExportDataTemplate::class,
            'config' => [
                'retry_attempts' => 2,
                'timeout' => 1800,
                'persist_state' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    |
    | Configuration for pipeline events and listeners.
    |
    */
    'events' => [
        'listeners' => [
            \Crumbls\Pipeline\Events\PipelineStepStarted::class => [
                \App\Listeners\LogPipelineStep::class,
            ],
            \Crumbls\Pipeline\Events\PipelineStepCompleted::class => [
                \App\Listeners\TrackPipelineMetrics::class,
            ],
            \Crumbls\Pipeline\Events\PipelineStepFailed::class => [
                \App\Listeners\NotifyPipelineFailure::class,
            ],
        ],
    ],
];
