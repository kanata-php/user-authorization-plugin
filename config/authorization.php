<?php

return [
    'protected_routes' => [
        '/admin',
    ],
    'social-login' => false,
    'email-confirmation' => env('AUTHORIZATION_EMAIL_CONFIRMATION', false),
];
