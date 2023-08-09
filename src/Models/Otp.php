<?php

namespace Murkrow\Otp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * App\Models\Otp
 *
 * @property int $id
 * @property string $otp
 * @property \Illuminate\Support\Carbon $valid_until
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp query()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereValidUntil($value)
 * @mixin \Eloquent
 */
class Otp extends Model
{
    protected $table = 'user_otps';

    protected $fillable = [
        'otp',
        'valid_until',
        'user_id'
    ];

    protected $casts = [
        'valid_until' => 'datetime',
    ];
}