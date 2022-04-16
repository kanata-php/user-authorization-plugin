<?php

namespace UserAuthorization\Http\Middlewares;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Services\AuthSessionHelper;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;

class AuthMiddleware
{
    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return ResponseInterface
     */
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        if (!AuthSessionHelper::hasAuthSession($request)) {
            return redirect(new Response, route('login'));
        }

        return $handler->handle($request);
    }
}
