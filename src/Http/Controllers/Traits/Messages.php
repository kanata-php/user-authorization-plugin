<?php

namespace UserAuthorization\Http\Controllers\Traits;

use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Message;

trait Messages
{
    public function getMessages(Request $request): ?string
    {
        $queryParams = $request->getQueryParams();

        $error = null;
        if (isset($queryParams['message'])) {
            $error = Message::getMessage(array_get($queryParams, 'message', 0));
        }

        return $error;
    }
}
