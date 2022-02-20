<?php

declare(strict_types=1);

namespace App\Bootstrap;

use Psr\Log\LoggerInterface;

final class CLIKernel
{
    /**
     * Invoke CLI Command
     * @return int exit code
     */
    public static function invoke(LoggerInterface $logger): int
    {
        /** @var \Semplice\Contracts\Container\IServiceLocator[] $service_locators */
        $service_locators = [
        ];

        new Entrypoint($logger, $service_locators);

        return 0;
    }
}
