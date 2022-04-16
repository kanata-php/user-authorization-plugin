<?php

namespace UserAuthorization\Http\Controllers;

use Exception;
use Kanata\Http\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Exceptions\TokenDeleteValidationException;
use UserAuthorization\Exceptions\TokenGenerationValidationException;
use UserAuthorization\Http\Controllers\Traits\Errors;
use UserAuthorization\Models\Token;
use UserAuthorization\Repositories\TokenRepository;
use UserAuthorization\Services\AuthSessionHelper;
use function view;
use function is_logged;

class AdminController extends Controller
{
    use Errors;

    public function index(Request $request, Response $response)
    {
        return view($response, 'auth::admin/dashboard', [
            'is_logged' => is_logged($request),
            'current_route' => current_route($request)->getName(),
        ]);
    }

    public function apiTokens(Request $request, Response $response)
    {
        $tokens = Token::where('user_id', AuthSessionHelper::getUserId($request))->get()->toArray();

        $params = [
            'is_logged' => is_logged($request),
            'current_route' => current_route($request)->getName(),
            'tokens' => json_encode($tokens),
        ];

        $error = $this->getErrors($request);
        if (null !== $error) {
            $params['error'] = $error;
        }

        $flash_message = get_flash_message($request);

        return view($response, 'auth::admin/api/index', array_merge(
            $params,
            ['errors' => $this->processFormMessages($flash_message['errors'] ?? [])],
            ['success' => $this->processFormMessages($flash_message['success'] ?? [])],
            get_form_session($request)
        ));
    }

    public function generateApiToken(Request $request, Response $response)
    {
        $error = false;

        try {
            $url = (new TokenRepository)->create($request);
        } catch (TokenGenerationValidationException $e) {
            $error = true;
            logger()->error($e->getMessage());
            $fieldsMessage = json_decode($e->getMessage(), true);
            set_flash_message($request, array_merge(
                ['errors' => ['form' => 'Data Invalid for token.']],
                $fieldsMessage
            ));
        } catch (Exception $e) {
            $error = true;
            logger()->error($e->getMessage());
            set_flash_message($request, ['errors' => ['form' => 'Unknown Error!']]);
        }

        if (!$error) {
            clear_form_session($request);
            set_flash_message($request, ['success' => ['form' => 'Token created successfully!']]);
            return redirect($response, $url);
        }

        return back($request, $response);
    }

    public function deleteApiToken(Request $request, Response $response)
    {
        $error = false;

        try {
            $url = (new TokenRepository)->delete($request);
        } catch (TokenDeleteValidationException $e) {
            $error = true;
            logger()->error($e->getMessage());
            set_flash_message($request, ['errors' => ['table-list' => 'Data Invalid for token.']]);
        } catch (Exception $e) {
            $error = true;
            logger()->error($e->getMessage());
            set_flash_message($request, ['errors' => ['table-list' => 'Failed to delete Token!']]);
        }

        if (!$error) {
            set_flash_message($request, ['success' => ['table-list' => 'Token deleted!']]);
            return redirect($response, $url);
        }

        return back($request, $response);
    }
}