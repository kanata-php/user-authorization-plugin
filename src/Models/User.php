<?php

namespace UserAuthorization\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const TABLE_NAME = 'users';

    /** @var string */
    protected $name = self::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected array $defaults = [];

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];
}
