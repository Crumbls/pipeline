<?php

namespace Crumbls\Pipeline\Contracts;

/**
 * StatefulPipeInterface defines the contract for state-aware pipeline steps
 */
interface StatefulPipeInterface
{
    /**
     * Handle the pipeline step with state awareness
     *
     * @param mixed $passable
     * @param \Closure $next
     * @return mixed
     */
    public function handle($passable, \Closure $next);

    /**
     * Get the current state of this pipe
     *
     * @return array
     */
    public function getState(): array;

    /**
     * Check if this pipe can be executed in parallel
     *
     * @return bool
     */
    public function canRunInParallel(): bool;
}
