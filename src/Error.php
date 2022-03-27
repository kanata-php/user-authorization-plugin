<?php

namespace UserAuthorization;

class Error
{
    protected static $errors = [
        0 => 'Unknown Error!',
        1 => 'User not found!',
        2 => 'Failed to authorize.',
        3 => 'Data Invalid for registration.',
    ];

    public static function getError(int $code): ?string
    {
        if (!isset(self::$errors[$code])) {
            return null;
        }

        return self::$errors[$code];
    }
}