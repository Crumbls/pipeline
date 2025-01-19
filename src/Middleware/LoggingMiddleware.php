<?php

namespace Crumbls\Pipeline\Middleware;

class LoggingMiddleware implements PipelineMiddlewareInterface
{
    public function before(mixed $passable, array $state): mixed
    {
        logger()->info('Pipeline step starting', [
            'step' => $state['currentStep'],
            'payload' => $passable
        ]);
        return $passable;
    }

    public function after(mixed $result, array $state): mixed
    {
        logger()->info('Pipeline step completed', [
            'step' => $state['currentStep'],
            'result' => $result
        ]);
        return $result;
    }
}
