<?php

namespace UserAuthorization\Services;

use UserAuthorization\Services\Interfaces\AuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class Auth implements AuthInterface
{
    const USER_ID = 'user_id';
    const SESSION_KEY = 'session_key';
}
