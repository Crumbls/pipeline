<?php

namespace Crumbls\Pipeline\Contracts;

interface StateStoreInterface
{
    /**
     * Save pipeline state
     *
     * @param string $pipelineId
     * @param array $state
     * @return void
     */
    public function save(string $pipelineId, array $state): void;

    /**
     * Load pipeline state
     *
     * @param string $pipelineId
     * @return array|null
     */
    public function load(string $pipelineId): ?array;

    /**
     * Clear pipeline state
     *
     * @param string $pipelineId
     * @return void
     */
    public function clear(string $pipelineId): void;
}
