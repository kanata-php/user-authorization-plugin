<?php

namespace UserAuthorization\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Exceptions\RegistrationValidationException;
use UserAuthorization\Repositories\RegistrationRepository;

class RegisterController
{
    public function index(Request $request, Response $response)
    {
        return view($response, 'auth::register');
    }

    public function registrationHandler(Request $request, Response $response)
    {
        try {
            $url = (new RegistrationRepository)->processRegistration($request, $response);
        } catch (RegistrationValidationException $e) {
            logger()->error($e->getMessage());
            return back($request, $response, [
                'error' => 3,
            ]);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return back($request, $response, ['error' => 0]);
        }

        return redirect($response, $url);
    }

    public function authMessage(Request $request, Response $response)
    {
        
    }
}
