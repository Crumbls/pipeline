<?php

namespace Crumbls\Pipeline\RateLimit;

use Illuminate\Support\Facades\RateLimiter;
use Crumbls\Pipeline\Pipes\AbstractStatefulPipe;
use Closure;

abstract class RateLimitedPipe extends AbstractStatefulPipe
{
    protected int $requestsPerMinute = 60;
    protected string $rateLimiterKey;

    public function __construct(string $rateLimiterKey = null)
    {
        $this->rateLimiterKey = $rateLimiterKey ?? static::class;
    }

    public function handle($passable, Closure $next)
    {
        return RateLimiter::attempt(
            'pipeline-' . $this->rateLimiterKey,
            $this->requestsPerMinute,
            function() use ($passable, $next) {
                $this->updateProgress(0);
                $result = $this->process($passable);
                $this->updateProgress(100);
                return $next($result);
            },
            60
        );
    }

    abstract protected function process($passable): mixed;
}
