<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        // $provider bisa berisi 'google' atau 'facebook'
        try {
            return Socialite::driver($provider)->stateless()->redirect();
        } catch (Exception $e) {
            return redirect('/login')->withErrors(['msg' => "Gagal mengarahkan ke $provider: " . $e->getMessage()]);
        }
    }

    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
            
            // Validasi: Pastikan email tersedia dari respons Facebook
            if (!$user->getEmail()) {
                return redirect('/login')->withErrors(['msg' => 'Email tidak ditemukan dari akun Facebook Anda.']);
            }

            // 1. Cari user di database berdasarkan email
            $authUser = User::where('email', $user->getEmail())->first();

            // 2. Jika email ditemukan di database
            if ($authUser) {
                
                // Update data social_id dan social_type agar sinkron
                $authUser->update([
                    'social_id'   => $user->getId(),
                    'social_type' => $provider,
                ]);

                // Login-kan user
                Auth::login($authUser);
                
                return redirect('/dashboard');

            } else {
                // 3. Jika email TIDAK ditemukan, tolak akses (sesuai permintaan kamu sebelumnya)
                return redirect('/login')->withErrors([
                    'msg' => 'Email (' . $user->getEmail() . ') belum terdaftar di sistem. Silakan hubungi Admin.'
                ]);
            }

        } catch (Exception $e) {
            return redirect('/login')->withErrors(['msg' => 'Terjadi kesalahan saat login: ' . $e->getMessage()]);
        }
    }
}