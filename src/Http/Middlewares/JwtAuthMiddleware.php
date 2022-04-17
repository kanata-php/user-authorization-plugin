<?php

namespace UserAuthorization\Http\Middlewares;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Services\AuthSessionHelper;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use UserAuthorization\Services\JwtTokenHelper;

class JwtAuthMiddleware
{
    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return ResponseInterface
     */
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        /**
         * Action: jwt_auth_hook
         * Description: Filter auth verification.
         * Expected return: array
         * @param array
         */
        $auth = apply_filters('jwt_auth_hook', [JwtTokenHelper::getAuthorization($request)]);

        if (empty($auth)) {
            return json_response(new Response, 'unauthorized', 401, 'Unauthorized Procedure!');
        }

        return $handler->handle($request);
    }
}
