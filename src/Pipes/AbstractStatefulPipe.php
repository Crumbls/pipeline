<?php

namespace Crumbls\Pipeline\Pipes;
/**
 * Example implementation of a stateful pipe
 */
abstract class AbstractStatefulPipe implements StatefulPipeInterface
{
    protected array $state = [
        'status' => 'pending',
        'progress' => 0,
        'metadata' => [],
    ];

    public function getState(): array
    {
        return $this->state;
    }

    public function canRunInParallel(): bool
    {
        return false;
    }

    protected function updateProgress(int $progress): void
    {
        $this->state['progress'] = $progress;
    }

    protected function setMetadata(string $key, $value): void
    {
        $this->state['metadata'][$key] = $value;
    }
}
