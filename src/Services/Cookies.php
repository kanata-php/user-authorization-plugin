<?php

namespace UserAuthorization\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Carbon\Carbon;

class Cookies
{
    const SESSION_SKELETON = [
        'user_id' => null,
        'session_key' => null,
    ];

    public static function getSessionKey(): string
    {
        return slug(config('app.app-name'));
    }

    public static function setSessionCookie(Response|Request &$serverObject, array $data = []): void
    {
        if (empty($data)) {
            $serverObject = $serverObject->withHeader(
                'Set-Cookie',
                Cookies::getSessionKey() . '=' . Cookies::encodeSessionData(Cookies::SESSION_SKELETON)
            );
            return;
        }

        $serverObject = $serverObject->withHeader(
            'Set-Cookie',
            Cookies::getSessionKey() . '=' . Cookies::encodeSessionData($data)
        );
    }

    public static function expireCookie(Request $request, Response &$response): void
    {
        if (!isset($request->getCookieParams()[self::getSessionKey()])) {
            return;
        }

        $response = $response->withHeader(
            'Set-Cookie',
            self::getExpiredCookie($request)
        );
    }

    public static function getExpiredCookie(Request $request): string
    {
        $data = self::getSessionCookie($request);
        $expireHour = Carbon::now()->subHour(1)->format('D, j F Y H:i:s T');
        return Cookies::getSessionKey() . '=' . Cookies::encodeSessionData($data) . '; Expires=' . $expireHour;
    }

    public static function getSessionCookie(Response|Request $serverObject): array
    {
        $cookies = $serverObject->getCookieParams();

        if (!isset($cookies[self::getSessionKey()])) {
            return [];
        }

        return self::parseSessionData($cookies[self::getSessionKey()]);
    }

    public static function encodeSessionData(array $data): string
    {
        return base64_encode(json_encode($data));
    }

    public static function parseSessionData(string $data): array
    {
        $eplodedData = explode(' ', $data);

        if (count($eplodedData) > 0) {
            $data = current($eplodedData);
        }

        return json_decode(base64_decode($data), true);
    }
}