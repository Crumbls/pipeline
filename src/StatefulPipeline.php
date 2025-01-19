<?php

/**
 * StatefulPipeline extends Laravel's base Pipeline to add state management
 * and job-based execution capabilities.
 */
class StatefulPipeline extends BasePipeline
{
    /**
     * Current state of the pipeline execution
     */
    /**
     * @var array<string, mixed> The current state of pipeline execution
     */
    protected array $state = [
        'currentStep' => 0,
        'completedSteps' => [],
        'status' => 'pending',
        'data' => [],
    ];

    /**
     * Pipeline configuration
     */
    protected array $config = [
        'retryAttempts' => 3,
        'timeout' => 3600,
        'allowParallel' => false,
    ];

    /**
     * Set the pipes through which the passable should be sent.
     * Overridden to support state-aware pipe definitions.
     *
     * @param array|mixed $pipes
     * @return $this
     */
    public function through($pipes)
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        // Initialize state for new pipes if not already set
        foreach ($this->pipes as $index => $pipe) {
            if (!isset($this->state['data'][$index])) {
                $this->state['data'][$index] = [
                    'status' => 'pending',
                    'attempts' => 0,
                    'startedAt' => null,
                    'completedAt' => null,
                    'error' => null,
                ];
            }
        }

        return $this;
    }

    /**
     * Run the pipeline as a job
     *
     * @param mixed $passable
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function dispatchAsPipeline($passable)
    {
        return PipelineJob::dispatch($this, $passable);
    }

    /**
     * Get the current state of the pipeline
     *
     * @return array
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * Set the pipeline state
     *
     * @param array $state
     * @return $this
     */
    public function setState(array $state): self
    {
        $this->state = array_merge($this->state, $state);
        return $this;
    }

    /**
     * Execute the pipeline with state management
     *
     * @param mixed $passable
     * @return mixed
     */
    public function thenReturn($passable)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            function ($passable) {
                return $passable;
            }
        );

        return $pipeline($passable);
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     * Overridden to add state management.
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                $pipeIndex = array_search($pipe, array_reverse($this->pipes));

                try {
                    // Update state before execution
                    $this->state['data'][$pipeIndex]['status'] = 'processing';
                    $this->state['data'][$pipeIndex]['startedAt'] = now();
                    $this->state['currentStep'] = $pipeIndex;

                    // Handle both Closure and class based pipes
                    $response = is_callable($pipe)
                        ? $pipe($passable, $stack)
                        : $this->handleClassBasedPipe($pipe, $passable, $stack);

                    // Update state after successful execution
                    $this->state['data'][$pipeIndex]['status'] = 'completed';
                    $this->state['data'][$pipeIndex]['completedAt'] = now();
                    $this->state['completedSteps'][] = $pipeIndex;

                    return $response;
                } catch (\Throwable $e) {
                    // Update state on failure
                    $this->state['data'][$pipeIndex]['status'] = 'failed';
                    $this->state['data'][$pipeIndex]['error'] = $e->getMessage();
                    $this->state['data'][$pipeIndex]['attempts']++;

                    if ($this->state['data'][$pipeIndex]['attempts'] >= $this->config['retryAttempts']) {
                        throw $e;
                    }

                    // Retry logic could be implemented here
                    return $stack($passable);
                }
            };
        };
    }
}
