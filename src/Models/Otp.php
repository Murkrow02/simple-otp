<?php

namespace Murkrow\Otp\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Otp extends Model
{
    protected $table = 'user_otps';

    protected $fillable = [
        'code',
        'valid_until',
        'user_id',
        'tag'
    ];

    protected $casts = [
        'valid_until' => 'datetime',
    ];
}