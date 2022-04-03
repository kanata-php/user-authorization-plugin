<?php

namespace UserAuthorization;

class Message
{
    protected static $messages = [
        1 => 'A message was sent to the registered email for confirmation! Proceed with the link there.',
        2 => 'Email confirmed successfully!',
    ];

    public static function getMessage(int $code): ?string
    {
        if (!isset(self::$messages[$code])) {
            return null;
        }

        return self::$messages[$code];
    }
}