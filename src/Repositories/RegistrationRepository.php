<?php

namespace UserAuthorization\Repositories;

use Kanata\Models\Traits\Validation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserAuthorization\Exceptions\RegistrationValidationException;
use UserAuthorization\Models\User;

class RegistrationRepository
{
    use Validation;

    /**
     * @throws RegistrationValidationException
     */
    public function validateRegistration(array $data)
    {
        $results = $this->validateFields([
            [
                'value' => array_get($data, 'name'),
                'rules' => [
                    new Required,
                    new Type('string'),
                    new NotBlank(),
                    new Length(['max' => 39]),
                ],
            ],
            [
                'value' => array_get($data, 'email'),
                'rules' => [
                    new Required,
                    new Type('string'),
                    new NotBlank(),
                    new Length(['max' => 79]),
                    new Email(),
                ],
            ],
            [
                'value' => array_get($data, 'password'),
                'rules' => [
                    new Required,
                    new Type('string'),
                    new NotBlank(),
                    new Length(['min' => 6, 'max' => 80]),
                ],
            ],
            [
                'value' => array_get($data, 'password_confirmation'),
                'rules' => [
                    new Required,
                    new Type('string'),
                    new NotBlank(),
                    new EqualTo(array_get($data, 'password')),
                ],
            ],
        ]);

        if (count($results) > 0) {
            throw new RegistrationValidationException(json_encode($results));
        }
    }

    /**
     * @throws RegistrationValidationException
     */
    public function processRegistration(Request $request, Response $response): string
    {
        $data = array_only(
            $request->getParsedBody(), ['name', 'email', 'password', 'password_confirmation']
        );

        // @throws RegistrationValidationException
        $this->validateRegistration($data);

        $user = User::create($data);

        if (null === $user) {
            throw new Exception('Failed to create user record.');
        }

        // TODO: dispatch email asynchronously when available
        

        return route('auth-message', ['message' => 1]);
    }
}
