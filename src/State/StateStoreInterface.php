<?php

namespace Crumbls\Pipeline\State;

interface StateStoreInterface
{
    public function save(string $pipelineId, array $state): void;
    public function load(string $pipelineId): ?array;
    public function clear(string $pipelineId): void;
}
