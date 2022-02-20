<?php

declare(strict_types=1);

namespace App\Bootstrap;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Semplice\Contracts\Container\IServiceLocator;
use Semplice\Contracts\Http\IHttpRunner;
use Semplice\Contracts\Routing\IRouteResolver;
use Semplice\Http\HttpServiceLocator;
use Semplice\Http\Middlewares\ContentLengthAwareMiddleware;
use Semplice\Http\Middlewares\ContentTypeAwareMiddleware;
use Semplice\Http\RequestHandlerPipeline;
use Semplice\Routing\OpenAPIRouteResolver;
use Semplice\Routing\RouteRequestHandler;

final class HttpKernel
{
    /**
     * Invoke RequestHandlerInterface and emit, then exit
     * @return never
     */
    public static function invoke(LoggerInterface $logger, ServerRequestInterface $request): never
    {
        $openapi = [
            'paths' => [
                '/' => [
                    'get' => [
                        'operationId' => 'Index',
                        'x-invoker' => \App\Index\IndexHandler::class,
                    ],
                ],
            ],
        ];

        /** @var IServiceLocator[] $service_locators */
        $service_locators = [
            new HttpServiceLocator(),
        ];

        $entrypoint = new Entrypoint($logger, $service_locators);

        $entrypoint->container->factory(IRouteResolver::class, fn () => new OpenAPIRouteResolver($openapi));
        /** @var class-string[] $handlers */
        $handlers = [
            ContentLengthAwareMiddleware::class,
            ContentTypeAwareMiddleware::class,

            // RouteRequestHandler must be replaced to last
            RouteRequestHandler::class,
        ];
        $pipeline = new RequestHandlerPipeline(
            $handlers,
            /** @param class-string $handler */
            fn (string $handler) => $entrypoint->container->get($handler),
        );
        $entrypoint->container->instance(RequestHandlerInterface::class, $pipeline);

        $entrypoint->container
            ->get(IHttpRunner::class)
            ->run($request);
    }
}
