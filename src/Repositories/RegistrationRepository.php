<?php

namespace UserAuthorization\Repositories;

use Exception;
use Mail\Mail;
use Carbon\Carbon;
use Kanata\Helpers\Form;
use Ramsey\Uuid\Uuid;
use UserAuthorization\Models\User;
use Kanata\Models\Traits\Validation;
use UserAuthorization\Services\Hash;
use UserAuthorization\Models\EmailConfirmation;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\EqualTo;
use UserAuthorization\Exceptions\TokenUsedException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Exceptions\TokenExpiredException;
use UserAuthorization\Rules\RecordDoesntExist;
use UserAuthorization\Exceptions\RegistrationValidationException;

class RegistrationRepository
{
    use Validation;

    /**
     * @throws RegistrationValidationException
     */
    public function validateRegistration(array $data)
    {
        $results = $this->validateFields([
            'name' => [
                'value' => array_get($data, 'name'),
                'rules' => [
                    new Required,
                    new Type('string'),
                    new NotBlank(),
                    new Length(['max' => 39]),
                ],
            ],
            'email' => [
                'value' => array_get($data, 'email'),
                'rules' => [
                    new Required,
                    new Type('string'),
                    new NotBlank(),
                    new Length(['max' => 79]),
                    new Email(),
                    new RecordDoesntExist(
                        [
                            'model' => User::class,
                            'field' => 'email',
                        ],
                        'Email already registered!'
                    ),
                ],
            ],
            'password' => [
                'value' => array_get($data, 'password'),
                'rules' => [
                    new Required,
                    new Type('string'),
                    new NotBlank(),
                    new Length(['min' => 6, 'max' => 80]),
                ],
            ],
            'password_confirmation' => [
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

        $data = array_only($data, ['name', 'email', 'password']);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if (null === $user) {
            throw new Exception('Failed to create user record.');
        }

        // TODO: dispatch email asynchronously when available
        $mailPlugin = get_plugin('Mail');
        if (
            config('authorization.email-confirmation')
            && null !== $mailPlugin
            && $mailPlugin['active']
        ) {
            $this->sendEmailConfirmation($user, $request);
        } else {
            $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();
        }

        return route('auth-message', [], ['message' => 1]);
    }

    public function confirmEmail(Request $request, Response $response)
    {
        $code = $request->getQueryParams('code');

        $emailConfirmation = EmailConfirmation::where('token', $code)->notExpired()->first();

        if (null === $emailConfirmation) {
            throw new TokenExpiredException('Token is Expired!');
        }

        if ($emailConfirmation->used) {
            throw new TokenUsedException('Token already used');
        }

        $user = User::find($emailConfirmation->user_id);

        if (null === $user) {
            throw new Exception('User not found!');
        }

        $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
        $emailConfirmation->used = true;
        if ($emailConfirmation->save() && $user->save()) {
            return route('auth-message', [], ['message' => 2]);
        }

        throw new Exception('Couldn\'t confirm email!');
    }

    private function sendEmailConfirmation(User $user, Request $request): void
    {
        $emailConfirmation = EmailConfirmation::create([
            'user_id' => $user->id,
            'token' => Uuid::uuid4()->toString(),
            'expire_at' => Carbon::now()->addMinutes(30),
        ]);

        (new Mail(true))->to($user->email, $user->name)
            ->send(
                'Kanata - Email Confirmation',
                container()->view->render('auth::emails/email-confirmation', [
                    'name' => $user->name,
                    'email_confirmation_url' => route('email-confirmation', [], [
                        'code' => $emailConfirmation->token,
                    ], $request),
                ])
            );
    }
}
