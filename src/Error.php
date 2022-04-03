<?php

namespace UserAuthorization;

class Error
{
    protected static $errors = [
        0 => 'Unknown Error!',
        1 => 'User not found!',
        2 => 'Failed to authorize.',
        3 => 'Data Invalid for registration.',
        4 => 'Email not verified yet.',
        5 => 'Token expired!',
        6 => 'Token already used!',
    ];

    public static function getError(int $code): ?string
    {
        if (!isset(self::$errors[$code])) {
            return null;
        }

        return self::$errors[$code];
    }
}