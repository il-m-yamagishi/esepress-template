<?php

declare(strict_types=1);

namespace App\Core;

use Semplice\Container\Container;
use Semplice\Contracts\Http\IHttpErrorHandler;
use Semplice\Contracts\Http\IHttpRunner;
use Semplice\Http\HttpServiceLocator;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class HttpKernel
{
    public static function invoke(): never
    {
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

        /** @todo Container registration automation */
        $container = new Container();
        $container->instance(RequestHandlerInterface::class, $handler);
        $container->instance(IHttpErrorHandler::class, $error_handler);
        $container->instance(ServerRequestInterface::class, $request);

        $locator_list = [
            new HttpServiceLocator(),
        ];

        foreach ($locator_list as $locator) {
            foreach ($locator->getStaticBindings() as $abstract => $concrete) {
                $container->bind($abstract, $concrete);
            }
            $locator($container);
        }

        $container->get(IHttpRunner::class)->run($request);
    }
}
