<?php

namespace UserAuthorization\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    const TABLE_NAME = 'tokens';

    protected $table = self::TABLE_NAME;

    protected array $defaults = [];

    protected $fillable = [
        'name',
        'user_id',
        'expire_at',
        'aud',
        'token',
        'aud_protocol',
    ];

    // scopes

    public function scopeByToken($query, string $token)
    {
        $query->where('token', $token);
    }
}
