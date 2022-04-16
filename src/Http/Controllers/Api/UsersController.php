<?php

namespace UserAuthorization\Http\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsersController
{
    public function index(Request $request, Response $response)
    {
        // TODO
        return json_response($response, '', 200, null, null, []);
    }
}