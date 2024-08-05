<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OTP;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function redirectToAuth()
    {
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    public function handleAuthCallback()
    {
        try {
            /** @var SocialiteUser $socialiteUser */
            $socialiteUser = Socialite::driver('google')->stateless()->user();
        } catch (ClientException $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }
        $pin = mt_rand(1000000, 9999999);
        $user = User::query()
            ->firstOrCreate(
                [
                    'email' => $socialiteUser->getEmail(),
                ],
                [
                    'email_verified_at' => now(),
                    'name' => $socialiteUser->getName(),
                    'google_id' => $socialiteUser->getId(),
                ]
            );

            OTP::query()
            ->firstOrCreate(
                [
                    'email' => $socialiteUser->getEmail(),
                ],
                [
                    'start_date' => now(),
                    'otp' => $pin,
                    'end_date' => now()->addMinute(30),
                ]
            );

        return response()->json([
            'user' => $user,
            'otp' => $pin,
            'access_token' => $user->createToken('google-token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function redirectToProvider()
    {
        // Generate a code verifier and code challenge for PKCE
        $codeVerifier = Str::random(64);
        $codeChallenge = $this->base64UrlEncode(hash('sha256', $codeVerifier, true));

        // Store the code verifier in the session
        session(['code_verifier' => $codeVerifier]);

        // Construct the TikTok authorization URL with PKCE parameters
        $redirectUrl = Socialite::driver('tiktok')
            ->stateless()
            ->with(['code_challenge' => $codeChallenge, 'code_challenge_method' => 'S256'])
            ->redirect()->getTargetUrl();

        return response()->json(['url' => $redirectUrl]);
    }

    private function base64UrlEncode($input)
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    public function handleProviderCallback()
    {
        try {
            // Retrieve the code verifier from the session
            $codeVerifier = session('code_verifier');
            session()->forget('code_verifier');

            // Fetch the user from TikTok
            $socialiteUser = Socialite::driver('tiktok')
                ->stateless()
                ->user();

            $pin = mt_rand(1000000, 9999999);
            $user = User::query()
                ->firstOrCreate(
                    [
                        'email' => $socialiteUser->getEmail(),
                    ],
                    [
                        'email_verified_at' => now(),
                        'name' => $socialiteUser->getName(),
                        'tiktok_id' => $socialiteUser->getId(),
                    ]
                );

            OTP::query()
                ->firstOrCreate(
                    [
                        'email' => $socialiteUser->getEmail(),
                    ],
                    [
                        'start_date' => now(),
                        'otp' => $pin,
                        'end_date' => now()->addMinute(30),
                    ]
                );

            return response()->json([
                'user' => $user,
                'otp' => $pin,
                'access_token' => $user->createToken('tiktok-token')->plainTextToken,
                'token_type' => 'Bearer',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }
    }

    // public function redirectToProvider()
    // {
    //     return response()->json([
    //         'url' => Socialite::driver('tiktok')->stateless()->redirect()->getTargetUrl(),
    //     ]);
    // }

    // public function handleProviderCallback()
    // {
    //     try {
    //         /** @var SocialiteUser $socialiteUser */
    //         $socialiteUser = Socialite::driver('tiktok')->stateless()->user();
    //     } catch (ClientException $e) {
    //         return response()->json(['error' => 'Invalid credentials provided.'], 422);
    //     }
    //     $pin = mt_rand(1000000, 9999999);
    //     $user = User::query()
    //         ->firstOrCreate(
    //             [
    //                 'email' => $socialiteUser->getEmail(),
    //             ],
    //             [
    //                 'email_verified_at' => now(),
    //                 'name' => $socialiteUser->getName(),
    //                 'google_id' => $socialiteUser->getId(),
    //             ]
    //         );

    //         OTP::query()
    //         ->firstOrCreate(
    //             [
    //                 'email' => $socialiteUser->getEmail(),
    //             ],
    //             [
    //                 'start_date' => now(),
    //                 'otp' => $pin,
    //                 'end_date' => now()->addMinute(30),
    //             ]
    //         );

    //     return response()->json([
    //         'user' => $user,
    //         'otp' => $pin,
    //         'access_token' => $user->createToken('tiktok-token')->plainTextToken,
    //         'token_type' => 'Bearer',
    //     ]);
    // }
}
