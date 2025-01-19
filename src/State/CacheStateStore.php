<?php

namespace Crumbls\Pipeline\State;

class CacheStateStore implements StateStoreInterface
{
    public function __construct(
        private \Illuminate\Contracts\Cache\Repository $cache,
        private string $prefix = 'pipeline_state_'
    ) {}

    public function save(string $pipelineId, array $state): void
    {
        $this->cache->put(
            $this->prefix . $pipelineId,
            $state,
            now()->addHours(24)
        );
    }

    public function load(string $pipelineId): ?array
    {
        return $this->cache->get($this->prefix . $pipelineId);
    }

    public function clear(string $pipelineId): void
    {
        $this->cache->forget($this->prefix . $pipelineId);
    }
}
