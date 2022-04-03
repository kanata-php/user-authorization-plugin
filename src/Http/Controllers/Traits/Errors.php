<?php

namespace UserAuthorization\Http\Controllers\Traits;

use UserAuthorization\Error;
use Psr\Http\Message\ServerRequestInterface as Request;

trait Errors
{
    /** @var array Errors that must be hidden. */
    protected $hidden_errors = [
        'password_confirmation' => 'Password and its confirmation must match.',
    ];

    /**
     * Get coded errors from request.
     *
     * @param Request $request
     * @return string|null
     */
    public function getErrors(Request $request): ?string
    {
        $queryParams = $request->getQueryParams();

        $error = null;
        if (isset($queryParams['error'])) {
            $error = Error::getError(array_get($queryParams, 'error', 0));
        }

        return $error;
    }

    /**
     * Process form errors.
     *
     * @param Request $request
     * @param array $fields
     * @return array
     */
    public function processFormMessages(array $fields): array
    {
        foreach ($fields as $key => $item) {
            if (in_array($key, array_keys($this->hidden_errors))) {
                $fields[$key] = $this->hidden_errors[$key];
            }
        }
        return $fields;
    }
}