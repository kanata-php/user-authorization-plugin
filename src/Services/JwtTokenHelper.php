<?php

namespace UserAuthorization\Services;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenHelper
{
    const HS256_ALGORITHM = 'HS256';

    /**
     * @param array $data
     * @return string
     */
    public static function generateJwtToken(array $data): string
    {
        $key = $data['name'];
        $payload = [
            "iat" => Carbon::now()->timestamp,
            "user_id" => array_get($data, 'user_id'),
        ];

        if (isset($data['aud_protocol']) && isset($data['aud'])) {
            $payload['aud'] = $data['aud_protocol'] . $data['aud'];
        }

        return JWT::encode($payload, $key, self::HS256_ALGORITHM);
    }

    /**
     * @param string $token
     * @param string $name
     * @return array
     */
    public static function decodeJwtToken(string $token, string $name): array
    {
        $decoded = JWT::decode($token, new Key($name, self::HS256_ALGORITHM));

        return (array) $decoded;
    }
}