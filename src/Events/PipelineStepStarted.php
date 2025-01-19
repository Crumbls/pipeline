<?php

namespace Crumbls\Pipeline\Events;

class PipelineStepStarted
{
    public function __construct(
        public readonly int $step,
        public readonly mixed $payload,
        public readonly array $state
    ) {}
}
