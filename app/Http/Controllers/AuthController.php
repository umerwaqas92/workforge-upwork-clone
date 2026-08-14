<?php

namespace App\Http\Controllers;

use App\Models\ClientProfile;
use App\Models\FreelancerProfile;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:client,freelancer'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'country' => $request->country ?? 'United States',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create initial wallet
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0.00,
            'escrow_balance' => 0.00,
            'currency' => 'USD',
        ]);

        // Create profile based on role
        if ($user->isFreelancer()) {
            FreelancerProfile::create([
                'user_id' => $user->id,
                'title' => 'Freelance Professional',
                'bio' => 'Welcome to my profile. I am ready to collaborate on challenging projects.',
                'hourly_rate' => 30.00,
                'experience_level' => 'intermediate',
                'availability' => 'available_now',
            ]);
        } elseif ($user->isClient()) {
            ClientProfile::create([
                'user_id' => $user->id,
                'company_name' => $user->name . "'s Team",
                'industry' => 'Technology',
                'about' => 'Hiring independent professionals on WorkForge.',
                'payment_verified' => false,
            ]);
        }

        // Send Welcome Transactional Email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\MarketplaceNotificationMail(
                    subject: '🎉 Welcome to WorkForge Marketplace!',
                    greeting: 'Welcome aboard, ' . $user->name . '!',
                    mainMessage: $user->isFreelancer()
                        ? 'Your freelancer account is now active. Complete your portfolio and skills profile to start receiving job invitations and submitting proposals.'
                        : 'Your client account is now active. You can now post your first project and receive bids from top-tier talent worldwide.',
                    actionUrl: route('dashboard'),
                    actionText: $user->isFreelancer() ? 'Complete Your Profile' : 'Post a Project Now',
                    details: [
                        'Account Role' => ucfirst($user->role),
                        'Status' => 'Active & Verified',
                        'Email' => $user->email,
                    ]
                )
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Welcome email failed: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created successfully! Welcome to the marketplace.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'You have been logged out.');
    }

    // Quick demo login helper
    public function quickLogin($role)
    {
        $email = match ($role) {
            'admin' => 'admin@upwork.test',
            'client' => 'client@upwork.test',
            'freelancer' => 'alex.dev@upwork.test',
            default => 'client@upwork.test',
        };

        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', "Logged in as demo {$role} ({$user->name})");
        }

        return redirect()->route('login');
    }
}
