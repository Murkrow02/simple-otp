<?php

namespace Murkrow\Otp\Builders;


use Exception;
use Murkrow\Otp\Models\Otp;
use Illuminate\Support\Facades\DB;

class OtpBuilder
{
    private Otp $otp;
    private int $length = 6;
    private $user;

    private bool $alphaNumeric = false;

    public function __construct()
    {
        $this->otp = new Otp();
        $this->expiresInMinutes(30);
    }

    public function expiresInMinutes(int $minutes): OtpBuilder
    {
        $this->otp->valid_until = now()->addMinutes($minutes);
        return $this;
    }

    public function tag(string $tag): OtpBuilder
    {
        $this->otp->tag = $tag;
        return $this;
    }

    public function forUser($user): OtpBuilder
    {
        $this->otp->user_id = $user->id;
        $this->user = $user;
        return $this;
    }

    public function alphaNumeric(bool $alphaNumeric = true): OtpBuilder
    {
        $this->alphaNumeric = $alphaNumeric;
        return $this;
    }

    public function length(int $length): OtpBuilder
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @throws Exception if user_id is not set
     */
    public function create(): Otp
    {
        // Check if user_id is set
        if(!$this->otp->user_id)
            throw new Exception('User ID is required to create OTP');

        DB::beginTransaction();

        //Check how many otps user has
        $otpCount = $this->user->otps()->count();

        //If user has more than n otps, delete the oldest one
        if($otpCount >= config('otp.max_otps_per_user')){
            // Remove all otps for user which are out of the limit
            $this->user->otps()
                ->orderBy('created_at', 'asc')
                ->limit($otpCount - config('otp.max_otps_per_user') + 1)
                ->delete();
        }

        // Generate a random code
        $this->otp->code = $this->generateSafeRandomCode($this->length, $this->alphaNumeric);

        $this->otp->save();

        DB::commit();

        return $this->otp;
    }

    /**
     * @param int $length
     * @param bool $alphaNumeric
     * @return string
     * @throws RandomException
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