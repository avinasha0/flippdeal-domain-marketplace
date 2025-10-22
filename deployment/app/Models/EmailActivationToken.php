<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailActivationToken extends Model
{
    protected $fillable = [
        'email',
        'token',
        'expires_at',
        'used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean'
    ];

    public static function generateToken($email)
    {
        // Delete any existing tokens for this email
        self::where('email', $email)->delete();
        
        // Generate a secure random token
        $token = Str::random(64);
        
        // Create new token with 24 hours expiry
        return self::create([
            'email' => $email,
            'token' => $token,
            'expires_at' => Carbon::now()->addHours(24)
        ]);
    }

    public static function verifyToken($email, $token)
    {
        $activationToken = self::where('email', $email)
            ->where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($activationToken) {
            $activationToken->update(['used' => true]);
            return true;
        }

        return false;
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
