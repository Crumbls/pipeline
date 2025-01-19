<?php

namespace Crumbls\Pipeline\Middleware;

class ValidationMiddleware implements PipelineMiddlewareInterface
{
    public function before(mixed $passable, array $state): mixed
    {
        if (!$this->validatePassable($passable)) {
            throw new \InvalidArgumentException('Invalid pipeline input');
        }
        return $passable;
    }

    public function after(mixed $result, array $state): mixed
    {
        if (!$this->validateResult($result)) {
            throw new \RuntimeException('Invalid pipeline output');
        }
        return $result;
    }

    private function validatePassable(mixed $passable): bool
    {
        // Custom validation logic
        return true;
    }

    private function validateResult(mixed $result): bool
    {
        // Custom validation logic
        return true;
    }
}
