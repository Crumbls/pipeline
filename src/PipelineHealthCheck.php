<?php

class PipelineHealthCheck
{
    public function check(string $pipelineId): array
    {
        $state = $this->stateStore->load($pipelineId);

        return [
            'status' => $this->determineStatus($state),
            'duration' => $this->calculateDuration($state),
            'progress' => $this->calculateProgress($state),
            'bottlenecks' => $this->identifyBottlenecks($state),
            'resourceUsage' => $this->getResourceMetrics($state)
        ];
    }
}
