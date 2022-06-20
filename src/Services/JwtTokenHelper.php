<?php

namespace UserAuthorization\Services;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Models\Token;

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

    /**
     * Method used to consume jwt token.
     *
     * @param Request $request
     * @return array
     */
    public static function getAuthorization(Request $request): array
    {
        $tokenRecord = self::getToken($request);

        if (null === $tokenRecord) {
            return [];
        }

        if (
            null !== $tokenRecord->allowed_uses
            && $tokenRecord->uses >= $tokenRecord->allowed_uses
        ) {
            $tokenRecord->delete();
            return [];
        }

        // increment uses
        $tokenRecord->uses = $tokenRecord->uses + 1;
        $tokenRecord->save();

        // TODO: restrict by domain

        return self::decodeJwtToken($tokenRecord->token, $tokenRecord->name);
    }

    public static function getToken(Request $request): ?Token
    {
        if (!$request->hasHeader('Authorization')) {
            return null;
        }

        $authorization = $request->getHeader('Authorization');
        $authorization = current($authorization);
        $authorization = explode(' ', $authorization);

        if ($authorization[0] !== 'Bearer') {
            return null;
        }

        $token = $authorization[1];

        return Token::byToken($token)->first();
    }
}