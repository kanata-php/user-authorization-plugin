<?php

namespace UserAuthorization\Http\Controllers\Api;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Exceptions\SingleUseTokenGenerationValidationException;
use UserAuthorization\Exceptions\TokenGenerationValidationException;
use UserAuthorization\Repositories\TokenRepository;

class UsersController
{
    public function index(Request $request, Response $response)
    {
        // TODO
        return json_response($response, '', 200, null, null, []);
    }

    public function generateSingleUserToken(Request $request, Response $response)
    {
        try {
            $token = (new TokenRepository)->createSingleUse($request);
        } catch (SingleUseTokenGenerationValidationException | TokenGenerationValidationException $e) {
            logger()->error($e->getMessage());
            $fieldsMessage = json_decode($e->getMessage(), true);
            return json_response($response, 'error', 422, null, $fieldsMessage);
        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return json_response($response, 'error', 500, null, ['Unknown Error!']);
        }

        return json_response($response, '', 200, null, null, [
            'token' => $token->token,
        ]);
    }
}