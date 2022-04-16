<?php

namespace UserAuthorization\Http\Controllers;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Repositories\RegistrationRepository;
use UserAuthorization\Exceptions\RegistrationValidationException;
use UserAuthorization\Exceptions\TokenExpiredException;
use UserAuthorization\Exceptions\TokenUsedException;
use UserAuthorization\Http\Controllers\Traits\Errors;
use UserAuthorization\Http\Controllers\Traits\Messages;

class RegisterController
{
    use Errors, Messages;
    
    public function index(Request $request, Response $response)
    {
        $params = [];

        $error = $this->getErrors($request);
        if (null !== $error) {
            $params['error'] = $error;
        }

        $flash_message = get_flash_message($request);

        return view($response, 'auth::register', array_merge(
            $params,
            ['errors' => $this->processFormMessages($flash_message['errors'] ?? [])],
            get_form_session($request),
        ));
    }

    public function registrationHandler(Request $request, Response $response)
    {
        $error = false;

        try {
            $url = (new RegistrationRepository)->processRegistration($request, $response);
        } catch (RegistrationValidationException $e) {
            $error = true;
            logger()->error($e->getMessage());
            $fieldsMessage = json_decode($e->getMessage(), true);
            set_flash_message($request, array_merge(
                ['errors' => ['form' => 'Data Invalid for registration.']],
                $fieldsMessage
            ));
        } catch (Exception $e) {
            $error = true;
            logger()->error($e->getMessage());
            set_flash_message($request, ['errors' => ['form' => 'Unknown Error!']]);
        }

        if (!$error) {
            clear_form_session($request);
            return redirect($response, $url);
        }
        
        return back($request, $response);
    }

    public function emailConfirmation(Request $request, Response $response)
    {
        try {
            $url = (new RegistrationRepository)->confirmEmail($request, $response);
        } catch (TokenExpiredException $e) {
            logger()->error($e->getMessage());
            return redirect($response, route('auth-message', [], ['error' => 5]));
        } catch (TokenUsedException $e) {
            logger()->error($e->getMessage());
            return redirect($response, route('auth-message', [], ['error' => 6]));
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return redirect($response, route('auth-message', [], ['error' => 0]));
        }

        return redirect($response, $url);
    }

    public function authMessage(Request $request, Response $response)
    {
        $params = ['url_back' => previous_url($request, [])];

        $message = $this->getMessages($request);
        if (null !== $message) {
            $params['message'] = $message;
        }

        return view($response, 'auth::auth-message', $params);
    }
}
