<?php

namespace Murkrow\Otp\Traits;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * Generate a new OTP for the user and save it to the database
     * @param int $minExp
     * @param int $codeLength
     * @param bool $alphaNumeric
     * @return Otp
     */
    public function generateOtp(int $minExp = 30, int $codeLength = 6, bool $alphaNumeric=false): Otp
    {
        //Check how many otps user has
        $otpCount = $this->otps()->count();

        //If user has more than n otps, delete the oldest one
        if($otpCount > config('otp.max_otps_per_user')){
            $killedOtp = $this->otps()->orderBy('valid_until', 'asc')->first();
            Otp::where('user_id',$this->id)->where('otp', $killedOtp->otp)->delete();
        }

        //Create new otp
        $otp = new Otp();
        $otp->user_id = $this->id;
        $otp->otp = $this->generateSafeRandomCode($codeLength, !$alphaNumeric);
        $otp->valid_until = now()->addMinutes($minExp);
        $otp->save();
        return $otp;
    }


    /**
     * Validates the OTP for user and returns true if valid, false otherwise
     * DOES NOT remove the OTP from user valid codes
     * @param string $otp
     * @return bool
     */
    public function validateOtp(string $otp): bool
    {
        return $this->otps()->where('otp',$otp)->exists();
    }

    /**
     * Validates the OTP for user and returns true if valid, false otherwise
     * Automatically removes the otp from user's valid ones
     * @param string $otp
     * @return bool
     */
    public function validateAndRemoveOtp(string $otp):bool{

        //Find desired OTP for user
        $valid = $this->validateOtp($otp);

        //Delete if found
        if($valid)
            $this->otps()->where('otp', strtoupper($otp))->delete();

        return $valid;
    }


    /**
     * @param int $length
     * @param bool $alphaNumeric
     * @return string
     */
    private function generateSafeRandomCode(int $length = 6, bool $alphaNumeric = true): string
    {
        // Make sure the length is a positive integer
        $length = max(1, $length);

        // Define the character set based on the requested type
        $characters = ($alphaNumeric) ? '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' : '0123456789';

        // Get the character set length
        $characterSetLength = strlen($characters);

        // Initialize the random code
        $randomCode = '';

        // Generate random bytes and map them to the character set
        for ($i = 0; $i < $length; $i++) {
            $randomByte = ord(random_bytes(1));
            $randomCode .= $characters[$randomByte % $characterSetLength];
        }

        return $randomCode;
    }
}