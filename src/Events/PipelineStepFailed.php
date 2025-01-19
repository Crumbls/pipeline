<?php

namespace Crumbls\Pipeline\Events;

class PipelineStepFailed
{
    public function __construct(
        public readonly int $step,
        public readonly \Throwable $exception,
        public readonly array $state
    ) {}
}
