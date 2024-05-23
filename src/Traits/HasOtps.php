<?php

namespace Murkrow\Otp\Traits;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Murkrow\Otp\Models\Otp;

/**
 * @method hasMany(string $class)
 */
trait HasOtps
{
    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    /**
     * Validates the OTP for user and returns true if valid, false otherwise
     * DOES NOT remove the OTP from user valid codes
     * @param string $code
     * @param string|null $tag
     * @return bool
     */
    public function validateOtp(string $code, string $tag = null): bool
    {
        $otp = $this
            ->otps()
            ->where('code',$code)
            ->where('valid_until','>',now());

        if($tag)
            $otp->where('tag',$tag);

        return $otp->exists();
    }

    /**
     * Validates the OTP for user and returns true if valid, false otherwise
     * Automatically removes the otp from user's valid ones
     * @param string $otp
     * @param string|null $tag
     * @return bool
     */
    public function validateAndRemoveOtp(string $otp, string $tag = null): bool
    {

        //Find desired OTP for user
        $valid = $this->validateOtp($otp, $tag);

        //Delete if found
        if($valid)
            $this->otps()->where('code', strtoupper($otp))->delete();

        return $valid;
    }



}