<?php

declare(strict_types=1);

namespace App\Index;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexHandler
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return (new ResponseFactory())->createResponse(200, 'OK')
            ->withBody((new StreamFactory())->createStream('{"ok":true}'));
    }
}
