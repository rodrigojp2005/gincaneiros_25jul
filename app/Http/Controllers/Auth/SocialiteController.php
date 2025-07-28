<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Debug: log dados do usuário Google
            \Log::info('Google user:', [
                'id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
            ]);

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                try {
                    $user = User::create([
                        'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                        'email' => $googleUser->getEmail(),
                        'password' => bcrypt(uniqid()), // senha aleatória
                        'email_verified_at' => now(), // marca como verificado
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao criar usuário Google: ' . $e->getMessage());
                    return redirect('/login')->withErrors(['msg' => 'Erro ao criar usuário Google: ' . $e->getMessage()]);
                }
            }

            Auth::login($user);
            if (Auth::check()) {
                return redirect()->intended('/');
            } else {
                return redirect('/login')->withErrors(['msg' => 'Falha ao autenticar usuário.']);
            }
        } catch (\Exception $e) {
            \Log::error('Erro Socialite: ' . $e->getMessage());
            return redirect('/login')->withErrors(['msg' => 'Erro ao autenticar com Google: ' . $e->getMessage()]);
        }
    }
}
