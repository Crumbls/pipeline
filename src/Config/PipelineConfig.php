<?php

namespace Crumbls\Pipeline\Config;

class PipelineConfig
{
    public function __construct(
        public readonly int $retryAttempts = 3,
        public readonly int $timeout = 3600,
        public readonly bool $allowParallel = false,
        public readonly bool $persistState = true,
        public readonly string $queueConnection = 'default',
        public readonly ?string $queueName = null,
        public readonly array $middleware = [],
        public readonly array $errorHandlers = []
    ) {}

    public static function fromArray(array $config): self
    {
        return new self(
            retryAttempts: $config['retryAttempts'] ?? 3,
            timeout: $config['timeout'] ?? 3600,
            allowParallel: $config['allowParallel'] ?? false,
            persistState: $config['persistState'] ?? true,
            queueConnection: $config['queueConnection'] ?? 'default',
            queueName: $config['queueName'] ?? null,
            middleware: $config['middleware'] ?? [],
            errorHandlers: $config['errorHandlers'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'retryAttempts' => $this->retryAttempts,
            'timeout' => $this->timeout,
            'allowParallel' => $this->allowParallel,
            'persistState' => $this->persistState,
            'queueConnection' => $this->queueConnection,
            'queueName' => $this->queueName,
            'middleware' => $this->middleware,
            'errorHandlers' => $this->errorHandlers
        ];
    }
}
