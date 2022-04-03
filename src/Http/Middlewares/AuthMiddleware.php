<?php

namespace UserAuthorization\Http\Middlewares;

use Kanata\Exceptions\UnauthorizedException;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthMiddleware
{
    /**
     * @param Request $request
     * @return Request
     * @throws UnauthorizedException
     */
    public function __invoke(Request $request): Request
    {
        // TODO

        return $request;
    }
}
