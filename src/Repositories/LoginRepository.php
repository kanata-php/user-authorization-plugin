<?php

namespace UserAuthorization\Repositories;

use Exception;
use UserAuthorization\Services\Hash;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use UserAuthorization\Exceptions\AuthFailedException;
use UserAuthorization\Exceptions\UserNotFoundException;
use UserAuthorization\Models\User;
use UserAuthorization\Services\AuthTable;
use UserAuthorization\Services\Cookies;

class LoginRepository
{
    /**
     * @param Request $request
     * @param Response $response
     * @return string
     * @throws UserNotFoundException
     * @throws AuthFailedException
     * @throws Exception
     */
    public function processLogin(Request &$request, Response &$response): string
    {
        $input = $request->getParsedBody();
        $user = User::where('email', $input['email'])->first();

        if (null === $user) {
            throw new UserNotFoundException('Login failed: User not found (' . $input['email'] . ')!');
        }

        if (!Hash::verify($input['password'], $user->password)) {
            throw new AuthFailedException('Login failed: Failed to authorize (' . $input['email'] . ')!');
        }

        $session_key = Uuid::uuid4()->toString();

        // save to cookies
        $currentCookie = Cookies::getSessionCookie($request);
        $currentCookie['user_id'] = $user->id;
        $currentCookie['session_key'] = $session_key;
        Cookies::setSessionCookie($response, $currentCookie);

        // save to auth table
        AuthTable::getInstance()->store($session_key, ['user_id' => $user->id]);

        return base_url() . '/admin';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return string
     * @throws Exception
     */
    public function processLogout(Request &$request, Response &$response): string
    {
        $currentCookie = Cookies::getSessionCookie($request);
        Cookies::expireCookie($request, $response);
        
        AuthTable::getInstance()->delete($currentCookie['session_key']);

        return base_url() . '/login';
    }
}