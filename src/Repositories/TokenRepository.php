<?php

namespace UserAuthorization\Repositories;

use Carbon\Carbon;
use Exception;
use Kanata\Models\Traits\Validation;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use UserAuthorization\Exceptions\SingleUseTokenGenerationValidationException;
use UserAuthorization\Exceptions\TokenDeleteValidationException;
use UserAuthorization\Exceptions\TokenGenerationValidationException;
use UserAuthorization\Models\Token;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Models\User;
use UserAuthorization\Rules\RecordDoesntExist;
use UserAuthorization\Rules\RecordExist;
use UserAuthorization\Services\AuthSessionHelper;
use UserAuthorization\Services\JwtTokenHelper;

class TokenRepository
{
    use Validation;

    /**
     * @throws TokenGenerationValidationException
     */
    public function validateTokenData(array $data)
    {
        $results = $this->validateFields([
            'name' => [
                'value' => array_get($data, 'name'),
                'rules' => [
                    new Required,
                    new Type('string'),
                    new NotBlank(),
                    new RecordDoesntExist(
                        [
                            'model' => Token::class,
                            'field' => 'name',
                        ],
                        'Token already exists with name!'
                    ),
                ],
            ],
            'aud' => [
                'value' => array_get($data, 'aud'),
                'rules' => [
                    new Type('string'),
                ],
            ],
            'aud_protocol' => [
                'value' => array_get($data, 'aud_protocol'),
                'rules' => [
                    new Type('string'),
                    new AtLeastOneOf([
                        new EqualTo('http://'),
                        new EqualTo('https://'),
                    ]),
                ],
            ],
            'expire_at' => [
                'value' => array_get($data, 'expire_at'),
                'rules' => [
                    new DateTime(),
                ],
            ],
            'user_id' => [
                'value' => array_get($data, 'user_id'),
                'rules' => [
                    new Required,
                    new Type('integer'),
                    new RecordExist(
                        [
                            'model' => User::class,
                            'field' => 'id',
                        ],
                        'User doesn\'t exist!'
                    ),
                ],
            ],
        ]);

        if (count($results) > 0) {
            throw new TokenGenerationValidationException(json_encode($results));
        }
    }

    /**
     * @throws TokenGenerationValidationException
     */
    public function validateDeleteTokenData(array $data)
    {
        $results = $this->validateFields([
            'id' => [
                'value' => array_get($data, 'id'),
                'rules' => [
                    new Required,
                    new RecordExist(
                        [
                            'model' => Token::class,
                            'field' => 'id',
                        ],
                        'Token doesn\'t exist!'
                    ),
                ],
            ],
        ]);

        if (count($results) > 0) {
            throw new TokenDeleteValidationException(json_encode($results));
        }
    }

    /**
     * @throws SingleUseTokenGenerationValidationException
     */
    public function validateSingleUseTokenData(array $data)
    {
        $results = $this->validateFields([
            'uses' => [
                'value' => array_get($data, 'uses'),
                'rules' => [
                    new Required,
                    new Type('integer'),
                    new EqualTo(1),
                ],
            ],
        ]);

        if (count($results) > 0) {
            throw new SingleUseTokenGenerationValidationException(json_encode($results));
        }
    }

    /**
     * @param Request $request
     * @return string
     * @throws TokenGenerationValidationException
     */
    public function create(Request $request): string
    {
        $data = array_only($request->getParsedBody(), ['name', 'aud', 'expire_at', 'aud_protocol']);
        if (!empty($data['expire_at'])) {
            $data['expire_at'] = Carbon::parse($data['expire_at']);
        }
        $data = array_filter($data);
        $data = array_merge(AuthSessionHelper::getAuthSession($request), $data);

        // @throws TokenGenerationValidationException
        $this->validateTokenData($data);

        $this->createToken($data);

        return route('api-tokens');
    }

    /**
     * @param Request $request
     * @return string
     * @throws TokenGenerationValidationException
     * @throws SingleUseTokenGenerationValidationException
     */
    public function createSingleUse(Request $request): Token
    {
        $token = JwtTokenHelper::getToken($request);
        if (null === $token) {
            throw new Exception('Failed retrieving token from the request.');
        }

        $data = [
            'name' => 'issued-' . Carbon::now()->timestamp . '-' . uniqid(),
            'user_id' => $token->user_id,
            'aud' => $token->aud,
            'aud_protocol' => $token->aud_protocol,
            'allowed_uses' => 1,
        ];

        // @throws TokenGenerationValidationException
        $this->validateTokenData($data);
        // @throws SingleUseTokenGenerationValidationException
        $this->validateSingleUseTokenData($data);

        return $this->createToken($data);
    }

    public function createToken(array $data): Token
    {
        $data['token'] = JwtTokenHelper::generateJwtToken($data);

        $token = Token::create($data);

        if (null === $token) {
            throw new Exception('Failed to create token record.');
        }

        return $token;
    }

    public function delete(Request $request): string
    {
        $data = array_only($request->getParsedBody(), ['id']);

        $this->validateDeleteTokenData($data);

        if (!Token::find($data['id'])->delete()) {
            throw new Exception('There was a problem while deleting Token.');
        }

        return route('api-tokens');
    }
}
