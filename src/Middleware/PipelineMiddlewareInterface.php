<?php

namespace Crumbls\Pipeline\Middleware;

interface PipelineMiddlewareInterface
{
    public function before(mixed $passable, array $state): mixed;
    public function after(mixed $result, array $state): mixed;
}
