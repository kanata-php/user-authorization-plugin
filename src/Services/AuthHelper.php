<?php

namespace UserAuthorization\Services;

use Exception;
use Kanata\Drivers\SessionTable;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Models\User;

class AuthHelper
{
    const AUTH_SESSION_KEY = 'auth-session';

    public static function createAuthSession(Request $request, User $user): void
    {
        $id = $request->session['id'];
        $sessionData = ['user_id' => $user->id];
        $sessionTable = SessionTable::getInstance();

        $previousData = $sessionTable->get($id);
        $sessionTable->set($id, array_merge($previousData, [self::AUTH_SESSION_KEY => $sessionData]));
    }

    public static function getAuthSession(Request $request): ?array
    {
        if (!self::hasAuthSession($request)) {
            return null;
        }

        $session = SessionTable::getInstance()->get($request->session['id']);
        return $session[self::AUTH_SESSION_KEY];
    }

    public static function clearAuthSession(Request &$request): void
    {
        $id = $request->session['id'];
        $sessionTable = SessionTable::getInstance();

        $session = $sessionTable->get($id);
        unset($session[self::AUTH_SESSION_KEY]);
        $sessionTable->set($id, $session);
    }

    public static function hasAuthSession(Request $request): bool
    {
        $id = $request->session['id'];
        $sessionTable = SessionTable::getInstance();
        $sessionData = $sessionTable->get($id);

        if (!isset($sessionData[self::AUTH_SESSION_KEY])) {
            return false;
        }

        return true;
    }
}
