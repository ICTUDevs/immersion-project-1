<?php

namespace App\Http\Controllers;

use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Fortify\Http\Responses\LoginResponse;

class GoogleController extends Controller
{
    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($request->credential);

        if ($payload) {
            $user = User::where('email', $payload['email'])->first();

            if ($user) {
                $user->update([
                    'oauth_id' => Hash::make($payload['sub'])
                ]);

                if ($user->two_factor_secret) {
                    session(['login.id' => $user->id]);
                    return route('two-factor.login');
                }

                Auth::login($user);
                return app(LoginResponse::class)->toResponse($request);
            } else {
                return redirect()->back()->with('error', 'Account not found. Please contact the Administrator.');
            }
        } else {
            return redirect()->back()->with('error', 'Failed to login with Google.');
        }
    }
}
