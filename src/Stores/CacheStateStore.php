<?php

namespace Crumbls\Pipeline\Stores;

use Crumbls\Pipeline\Contracts\StateStoreInterface;
use Illuminate\Contracts\Cache\Repository as Cache;

class CacheStateStore implements StateStoreInterface
{
    public function __construct(
        private Cache $cache,
        private string $prefix = 'pipeline_state_'
    ) {}

    public function save(string $pipelineId, array $state): void
    {
        $this->cache->put(
            $this->prefix . $pipelineId,
            $state,
            now()->addDay()
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
