<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace App\Bootstrap;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Semplice\Contracts\Bootstrap\ILoggerFactory;

class LoggerFactory implements ILoggerFactory
{
    /**
     * {@inheritDoc}
     */
    public function createLogger(string $name): LoggerInterface
    {
        return new Logger($name);
    }
}
