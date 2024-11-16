<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Fortify\Http\Responses\LoginResponse;

class GoogleController extends Controller
{
    const GOOGLE_TYPE = 'google';

    public function handleGoogleRedirect()
    {
        return Socialite::driver(static::GOOGLE_TYPE)->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver(self::GOOGLE_TYPE)->stateless()->user();

            // Check if the user already exists using email
            $userExisted = User::where('email', $user->email)->first();

            if ($userExisted) {
                $userExisted->update([
                    'oauth_id' => Hash::make($user->id)
                ]);

                // Check if the user has enabled 2FA
                if ($userExisted->two_factor_secret) {
                    session(['login.id' => $userExisted->id]);
                    return redirect('/two-factor-challenge');
                }

                Auth::login($userExisted);
                return app(LoginResponse::class)->toResponse(request());
            } else {
                return redirect()->route('login')->with('error', 'Account not found. Please contact the Administrator.');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to login with Google.');
        }
    }
}
