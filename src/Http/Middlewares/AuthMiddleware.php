<?php

namespace UserAuthorization\Http\Middlewares;

use Kanata\Exceptions\UnauthorizedException;
use Kanata\Http\Middlewares\Interfaces\HttpMiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Services\Auth;
use UserAuthorization\Services\AuthTable;
use UserAuthorization\Services\Cookies;

class AuthMiddleware
{
    /**
     * @param Request $request
     * @return Request
     * @throws UnauthorizedException
     */
    public function __invoke(Request $request): Request
    {
        $request->authorized = false;

        $currentCookie = Cookies::getSessionCookie($request);

        $currentTableRecord = null;
        if (isset($currentCookie[Auth::SESSION_KEY])) {
            $currentTableRecord = AuthTable::getInstance()->get($currentCookie[Auth::SESSION_KEY]);
        }

        if (!empty($currentCookie) && $currentTableRecord !== null) {
            $request->authorized = true;
            $request->{Auth::SESSION_KEY} = $currentCookie[Auth::SESSION_KEY];
            $request->{Auth::USER_ID} = $currentCookie[Auth::USER_ID];
        }

        if (!in_array($request->getUri()->getPath(), config('authorization.protected_routes'))) {
            return $request;
        }

        if (!$request->authorized) {
            throw new UnauthorizedException();
        }

        return $request;
    }
}
