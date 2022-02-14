<?php

declare(strict_types=1);

use EsePress\Contracts\Http\IHttpErrorHandler;
use EsePress\Http\HttpRunner;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

require_once implode(DIRECTORY_SEPARATOR, [
    __DIR__,
    '..',
    'vendor',
    'autoload.php',
]);

$handler = new class () implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = '{"ok":true}';
        return (new Response())
            ->withHeader('Content-Length', strlen($result))
            ->withHeader('Content-Type', 'application/json; charset=UTF-8')
            ->withBody((new StreamFactory())->createStream($result));
    }
};

$error_handler = new class () implements IHttpErrorHandler {
    public function handleError(ServerRequestInterface $request, Throwable $throwable): ResponseInterface
    {
        throw new \Exception('not implemented');
    }
};

$request = ServerRequestFactory::fromGlobals();

(new HttpRunner($handler, $error_handler))->run($request);
