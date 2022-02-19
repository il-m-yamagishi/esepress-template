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
use Semplice\Contracts\Routing\IRouteResolver;
use Semplice\Http\Middlewares\ContentLengthAwareMiddleware;
use Semplice\Http\Middlewares\ContentTypeAwareMiddleware;
use Semplice\Http\RequestHandlerPipeline;
use Semplice\Routing\OpenAPIRouteResolver;
use Semplice\Routing\RouteRequestHandler;
use Throwable;

final class HttpKernel
{
    public static function invoke(): never
    {
        $error_handler = new class () implements IHttpErrorHandler {
            public function handleError(ServerRequestInterface $request, Throwable $throwable): ResponseInterface
            {
                throw new \Exception('not implemented');
            }
        };

        $request = ServerRequestFactory::fromGlobals();

        /** @todo Container registration automation */
        $container = new Container();

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
        $openapi_route_resolver = new OpenAPIRouteResolver($openapi);
        $container->instance(IRouteResolver::class, $openapi_route_resolver);
        $handlers = [
            ContentLengthAwareMiddleware::class,
            ContentTypeAwareMiddleware::class,
            RouteRequestHandler::class,
        ];
        $pipeline = new RequestHandlerPipeline(
            $handlers,
            fn (string $handler) => $container->get($handler),
        );
        $container->instance(RequestHandlerInterface::class, $pipeline);
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
