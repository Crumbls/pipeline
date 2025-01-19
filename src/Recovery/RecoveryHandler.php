<?php

namespace Crumbls\Pipeline\Recovery;

class RecoveryHandler
{
    public function __construct(
        private readonly StateStoreInterface $stateStore
    ) {}

    public function handleFailure(
        string $pipelineId,
        \Throwable $exception,
        array $state
    ): void {
        // Store failure state
        $this->stateStore->save($pipelineId, array_merge($state, [
            'error' => [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'occurred_at' => now()->toIso8601String()
            ]
        ]));

        // Notify administrators
        event(new PipelineStepFailed(
            $state['currentStep'],
            $exception,
            $state
        ));
    }

    public function recover(string $pipelineId): ?array
    {
        return $this->stateStore->load($pipelineId);
    }

    public function canRecover(array $state): bool
    {
        return isset($state['error']) &&
            $state['data'][$state['currentStep']]['attempts'] <
            ($state['config']['retryAttempts'] ?? 3);
    }
}
