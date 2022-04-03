<?php

namespace UserAuthorization\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmailConfirmation extends Model
{
    const TABLE_NAME = 'email_confirmations';

    /** @var string */
    protected $name = self::TABLE_NAME;
    protected $table = self::TABLE_NAME;

    protected array $defaults = [];

    protected $fillable = [
        'user_id',
        'token',
        'expire_at',
        'used',
    ];

    public function scopeNotExpired($query)
    {
        $query->where('expire_at', '<', Carbon::now()->format('Y-m-f H:i:s'));
    }
}