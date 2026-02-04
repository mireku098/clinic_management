<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
            'terms' => ['accepted'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Registration Error',
                    'text' => 'Please correct the highlighted fields and try again.',
                    'showConfirmButton' => true,
                ]);
        }

        try {
            $roleId = Role::where('role_name', 'Administrator')->value('id') ?? 1;

            $user = User::create([
                'name' => $validator->validated()['name'],
                'email' => $validator->validated()['email'],
                'phone' => $validator->validated()['phone'] ?? null,
                'password' => Hash::make($validator->validated()['password']),
                'role_id' => $roleId,
                'status' => 'active',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withErrors(['general' => 'Unable to complete registration at the moment.'])
                ->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Registration Failed',
                    'text' => 'Something went wrong on our end. Please try again shortly.',
                    'showConfirmButton' => true,
                ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Registration Successful',
            'text' => 'Welcome aboard, ' . $user->name . '!',
        ]);

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $attempt = Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'status' => 'active',
        ], $request->boolean('remember'));

        if ($attempt) {
            $request->session()->regenerate();
            $request->session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Welcome Back',
                'text' => 'You are now signed in.',
            ]);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or inactive account.',
        ])->with('swal', [
            'icon' => 'error',
            'title' => 'Login Failed',
            'text' => 'Please check your email and password.',
            'showConfirmButton' => true,
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->flash('swal', [
            'icon' => 'info',
            'title' => 'Signed Out',
            'text' => 'You have been logged out safely.',
        ]);

        return redirect()->route('login');
    }
}
