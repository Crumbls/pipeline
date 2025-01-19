<?php

namespace Crumbls\Pipeline\Health;

use Crumbls\Pipeline\State\StateStoreInterface;

class PipelineHealthCheck
{
    public function __construct(
        private readonly StateStoreInterface $stateStore
    ) {}

    public function check(string $pipelineId): array
    {
        $state = $this->stateStore->load($pipelineId);

        if (!$state) {
            return ['status' => 'not_found'];
        }

        return [
            'status' => $this->determineStatus($state),
            'duration' => $this->calculateDuration($state),
            'progress' => $this->calculateProgress($state),
            'bottlenecks' => $this->identifyBottlenecks($state),
            'resourceUsage' => $this->getResourceMetrics($state),
            'lastUpdated' => $state['lastUpdated'] ?? null,
        ];
    }

    private function determineStatus(array $state): string
    {
        if (isset($state['error'])) {
            return 'failed';
        }

        if ($state['status'] === 'completed') {
            return 'completed';
        }

        if (empty($state['completedSteps'])) {
            return 'pending';
        }

        return 'in_progress';
    }

    private function calculateDuration(array $state): ?float
    {
        if (!isset($state['startedAt'])) {
            return null;
        }

        $end = $state['completedAt'] ?? now();
        return (new \DateTime($end))->getTimestamp() -
            (new \DateTime($state['startedAt']))->getTimestamp();
    }

    private function calculateProgress(array $state): float
    {
        if (empty($state['data'])) {
            return 0.0;
        }

        $completed = count(array_filter($state['data'], fn($step) =>
            $step['status'] === 'completed'
        ));

        return ($completed / count($state['data'])) * 100;
    }

    private function identifyBottlenecks(array $state): array
    {
        $bottlenecks = [];

        foreach ($state['data'] as $index => $step) {
            if ($step['status'] === 'completed' && isset($step['startedAt'], $step['completedAt'])) {
                $duration = (new \DateTime($step['completedAt']))->getTimestamp() -
                    (new \DateTime($step['startedAt']))->getTimestamp();

                if ($duration > ($state['config']['stepTimeout'] ?? 300)) {
                    $bottlenecks[] = [
                        'step' => $index,
                        'duration' => $duration,
                        'threshold' => $state['config']['stepTimeout'] ?? 300
                    ];
                }
            }
        }

        return $bottlenecks;
    }

    private function getResourceMetrics(array $state): array
    {
        return [
            'memoryPeak' => memory_get_peak_usage(true),
            'totalSteps' => count($state['data']),
            'completedSteps' => count($state['completedSteps']),
            'failedSteps' => count(array_filter($state['data'], fn($step) =>
                $step['status'] === 'failed'
            )),
        ];
    }
}
