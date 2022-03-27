<?php

namespace UserAuthorization\Http\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Error;
use UserAuthorization\Exceptions\AuthFailedException;
use UserAuthorization\Exceptions\UserNotFoundException;
use UserAuthorization\Http\Controllers\LoginController;
use UserAuthorization\Models\User;
use UserAuthorization\Repositories\LoginRepository;
use UserAuthorization\Services\AuthTable;
use UserAuthorization\Services\Cookies;

class LoginController
{
    public function index(Request $request, Response $response)
    {
        $params = [];

        $queryParams = $request->getQueryParams();

        $error = null;
        if (isset($queryParams['error'])) {
            $error = Error::getError(array_get($queryParams, 'error', 0));
        }

        if (null !== $error) {
            $params['error'] = $error;
        }

        return view($response, 'auth::login', $params);
    }

    public function loginHandler(Request $request, Response $response)
    {
        try {
            $url = (new LoginRepository)->processLogin($request, $response);
        } catch (UserNotFoundException $e) {
            logger()->debug($e->getMessage());
            return back($request, $response, ['error' => 1]);
        } catch (AuthFailedException $e) {
            logger()->debug($e->getMessage());
            return back($request, $response, ['error' => 2]);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return back($request, $response, ['error' => 0]);
        }

        return redirect($response, $url);
    }

    public function logoutHandler(Request $request, Response $response)
    {
        try {
            $url = (new LoginRepository)->processLogout($request, $response);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return back($request, $response, ['error' => 0]);
        }

        return redirect($response, $url);
    }
}