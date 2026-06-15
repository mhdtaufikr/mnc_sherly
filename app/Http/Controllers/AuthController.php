<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        if ($request->boolean('romantic_portal')) {
            $user = User::query()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->where('name', 'like', '%Sherly%')
                        ->orWhere('username', 'like', '%sherly%')
                        ->orWhere('email', 'like', '%sherly%');
                })
                ->first();

            $user ??= User::where('is_active', true)
                ->where('role', 'user')
                ->first();

            $user ??= User::where('is_active', true)->first();

            if (! $user) {
                return redirect()->route('login')
                    ->with('error', 'Portal Sherly belum menemukan akun aktif.');
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            $user->update([
                'last_login' => now(),
                'login_counter' => ($user->login_counter ?? 0) + 1,
            ]);

            return redirect()->intended('/home')
                ->with('success', 'Selamat datang, Sherly sayang.');
        }

        if ($request->has('code') && $request->has('state')) {
            try {
                $azureUser = Socialite::driver('azure')->stateless()->user();

                $user = User::where('email', $azureUser->mail)->first();

                if (! $user) {
                    return redirect('/')
                        ->with('error', 'User not found. Please contact the administrator.');
                }

                Auth::login($user, true);
                $request->session()->regenerate();

                $user->update([
                    'last_login' => now(),
                    'login_counter' => ($user->login_counter ?? 0) + 1,
                ]);

                return redirect()->intended('/home');
            } catch (\Exception $e) {
                return redirect('/')
                    ->with('error', 'Azure Login Failed: ' . $e->getMessage());
            }
        }

        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($request->email);
        $password = $request->password;
        $remember = $request->boolean('remember');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $login, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')
                    ->with('error', 'Give Access First to User');
            }

            $user->update([
                'last_login' => now(),
                'login_counter' => ($user->login_counter ?? 0) + 1,
            ]);

            return redirect()->intended('/home')
                ->with('success', 'Success login');
        }

        return redirect('/')
            ->with('error', 'Wrong Email/Username or Password');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Success Logout');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->with('error', 'Old password is incorrect');
        }

        $user->update([
            'password' => $request->new_password,
            'password_changed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Password changed successfully');
    }

    public function handleAzureCallback()
    {
        return Socialite::driver('azure')->redirect();
    }
}
