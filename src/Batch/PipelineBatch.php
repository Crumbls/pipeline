<?php

namespace Crumbls\Pipeline\Batch;

use Crumbls\Pipeline\StatefulPipeline;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;

class PipelineBatch
{
    private Collection $jobs;
    private ?Closure $then = null;
    private ?Closure $catch = null;
    private array $config;

    public function __construct(
        private readonly StatefulPipeline $pipeline,
        private readonly array $batches
    ) {
        $this->jobs = collect();
        $this->config = $pipeline->getConfig()->toArray();
    }

    public function then(Closure $callback): self
    {
        $this->then = $callback;
        return $this;
    }

    public function catch(Closure $callback): self
    {
        $this->catch = $callback;
        return $this;
    }

    public function dispatch(): Batch
    {
        foreach ($this->batches as $batch) {
            $this->jobs->push(
                $this->pipeline->clone()
                    ->send($batch)
                    ->dispatchAsPipeline()
            );
        }

        return Bus::batch($this->jobs)
            ->then($this->then)
            ->catch($this->catch)
            ->dispatch();
    }
}
