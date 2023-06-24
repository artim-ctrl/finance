<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

final class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        BaseRequest::HEADER_X_FORWARDED_FOR |
        BaseRequest::HEADER_X_FORWARDED_HOST |
        BaseRequest::HEADER_X_FORWARDED_PORT |
        BaseRequest::HEADER_X_FORWARDED_PROTO |
        BaseRequest::HEADER_X_FORWARDED_AWS_ELB;
}
