<?php

declare(strict_types = 1);

namespace App\Services;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final readonly class LogstashLogger
{
    /**
     * @param array<string, mixed> $config
     */
    public function __invoke(array $config): LoggerInterface
    {
        $handler = new SocketHandler("udp://{$config['host']}:{$config['port']}");
        $handler->setFormatter(new LogstashFormatter(config('app.name')));

        return new Logger('logstash.main', [$handler]);
    }
}
