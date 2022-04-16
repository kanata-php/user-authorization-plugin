<?php

namespace UserAuthorization\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const TABLE_NAME = 'users';

    protected $table = self::TABLE_NAME;

    protected array $defaults = [];

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];

    // scope

    public function scopeByEmail($query, $email)
    {
        $query->where('email', $email);
    }
}
