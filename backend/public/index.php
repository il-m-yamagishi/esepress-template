<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$logger = (new App\Bootstrap\LoggerFactory())->createLogger('Semplice HTTP');
$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();

App\Bootstrap\HttpKernel::invoke($logger, $request);
