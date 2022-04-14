<?php

namespace UserAuthorization\Http\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use UserAuthorization\Repositories\LoginRepository;
use UserAuthorization\Exceptions\AuthFailedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Exceptions\UserNotFoundException;
use UserAuthorization\Exceptions\EmailNotVerifiedException;
use UserAuthorization\Http\Controllers\Traits\Errors;

class LoginController
{
    use Errors;

    public function index(Request $request, Response $response)
    {
        $params = [];

        $queryParams = $request->getQueryParams();

        $error = $this->getErrors($request);

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

        } catch (EmailNotVerifiedException $e) {

            logger()->debug($e->getMessage());
            return back($request, $response, ['error' => 4]);

        } catch (Exception $e) {

            logger()->error($e->getMessage());
            return back($request, $response, ['error' => 0]);

        }

        clear_form_session($request);

        return redirect($response, $url);
    }

    public function logoutHandler(Request $request, Response $response)
    {
        try {
            
            $url = (new LoginRepository)->processLogout($request);

        } catch (Exception $e) {

            logger()->error($e->getMessage());
            return back($request, $response, ['error' => 0]);

        }

        return redirect($response, $url);
    }
}