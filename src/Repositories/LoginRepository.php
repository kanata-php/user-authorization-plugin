<?php

namespace UserAuthorization\Repositories;

use Exception;
use Kanata\Services\Hash;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use UserAuthorization\Exceptions\AuthFailedException;
use UserAuthorization\Exceptions\EmailNotVerifiedException;
use UserAuthorization\Exceptions\UserNotFoundException;
use UserAuthorization\Models\User;
use UserAuthorization\Services\AuthTable;
use UserAuthorization\Services\SessionCookies;

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

        if (null === $user->email_verified_at) {
            throw new EmailNotVerifiedException('Login failed: User didn\'t verify email yet! (' . $input['email'] . ')');
        }

        if (!Hash::verify($input['password'], $user->password)) {
            throw new AuthFailedException('Login failed: Failed to authorize (' . $input['email'] . ')!');
        }

        $session_key = Uuid::uuid4()->toString();

        // save to cookies
        $currentCookie = SessionCookies::getSessionCookie($request);
        $currentCookie['user_id'] = $user->id;
        $currentCookie['session_key'] = $session_key;
        SessionCookies::setSessionCookie($response, $currentCookie);

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
        $currentCookie = SessionCookies::getSessionCookie($request);
        SessionCookies::expireSessionCookie($request, $response);
        
        AuthTable::getInstance()->delete($currentCookie['session_key']);

        return base_url() . '/login';
    }
}