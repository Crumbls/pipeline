<?php

namespace Crumbls\Pipeline\Events;

class PipelineStepCompleted
{
    public function __construct(
        public readonly int $step,
        public readonly mixed $result,
        public readonly array $state
    ) {}
}
