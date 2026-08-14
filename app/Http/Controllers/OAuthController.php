<?php

namespace App\Http\Controllers;

use App\Mail\MarketplaceNotificationMail;
use App\Models\ClientProfile;
use App\Models\FreelancerProfile;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    protected array $supportedProviders = ['google', 'github'];

    /**
     * Redirect user to OAuth provider or test simulator
     */
    public function redirect(Request $request, string $provider)
    {
        if (!in_array($provider, $this->supportedProviders)) {
            abort(404, 'Unsupported authentication provider.');
        }

        // Store selected role in session for registration
        $role = $request->query('role', 'freelancer');
        if (!in_array($role, ['freelancer', 'client'])) {
            $role = 'freelancer';
        }
        session(['oauth_role' => $role]);

        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");

        // If credentials are configured, use real Socialite OAuth
        if (!empty($clientId) && !empty($clientSecret)) {
            try {
                return Socialite::driver($provider)->redirect();
            } catch (\Throwable $e) {
                Log::warning("OAuth redirect failed for {$provider}: " . $e->getMessage());
            }
        }

        // Fallback to interactive simulator for seamless testing / demo mode
        return view('auth.oauth-simulator', compact('provider', 'role'));
    }

    /**
     * Handle OAuth callback from provider
     */
    public function callback(Request $request, string $provider)
    {
        if (!in_array($provider, $this->supportedProviders)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
            return $this->loginOrCreateUser($socialUser, $provider);
        } catch (\Throwable $e) {
            Log::error("OAuth callback error for {$provider}: " . $e->getMessage());
            return redirect()->route('login')->with('error', "Unable to authenticate with {$provider}. Please try again or use standard login.");
        }
    }

    /**
     * Handle simulated 1-click test authorization
     */
    public function handleSimulatedAuth(Request $request, string $provider)
    {
        if (!in_array($provider, $this->supportedProviders)) {
            abort(404);
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'role' => 'nullable|in:freelancer,client',
        ]);

        $role = $validated['role'] ?? session('oauth_role', 'freelancer');

        // Create a mock social user object
        $socialUser = (object) [
            'id' => 'sim_' . $provider . '_' . md5($validated['email']),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'avatar' => $provider === 'github'
                ? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=300'
                : 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&q=80&w=300',
        ];

        return $this->loginOrCreateUser($socialUser, $provider, $role);
    }

    /**
     * Shared logic to find or create user and log in
     */
    protected function loginOrCreateUser($socialUser, string $provider, ?string $fallbackRole = null)
    {
        $providerIdField = "{$provider}_id";
        $email = is_object($socialUser) && method_exists($socialUser, 'getEmail') ? $socialUser->getEmail() : ($socialUser->email ?? null);
        $name = is_object($socialUser) && method_exists($socialUser, 'getName') ? $socialUser->getName() : ($socialUser->name ?? 'WorkForge Member');
        $avatar = is_object($socialUser) && method_exists($socialUser, 'getAvatar') ? $socialUser->getAvatar() : ($socialUser->avatar ?? null);
        $providerId = (string) (is_object($socialUser) && method_exists($socialUser, 'getId') ? $socialUser->getId() : ($socialUser->id ?? Str::random(16)));

        if (!$email) {
            return redirect()->route('login')->with('error', "Could not retrieve email address from {$provider}.");
        }

        // 1. Check if user already exists with this provider ID or email
        $user = User::where($providerIdField, $providerId)
            ->orWhere('email', $email)
            ->first();

        $isNewUser = false;

        if (!$user) {
            $isNewUser = true;
            $role = $fallbackRole ?? session('oauth_role', 'freelancer');

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => $role,
                'avatar' => $avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=300',
                $providerIdField => $providerId,
                'auth_provider' => $provider,
                'provider_avatar' => $avatar,
                'email_verified_at' => now(),
                'status' => 'active',
                'country' => 'United States',
                'city' => 'San Francisco, CA',
            ]);

            // Create Profile
            if ($role === 'freelancer') {
                FreelancerProfile::create([
                    'user_id' => $user->id,
                    'title' => $provider === 'github' ? 'Senior Full-Stack Software Engineer' : 'Professional Specialist',
                    'bio' => "Experienced professional specializing in high-impact development. Joined via {$provider}.",
                    'hourly_rate' => 65.00,
                    'experience_level' => 'expert',
                    'english_level' => 'Fluent',
                    'availability' => 'more_than_30_hrs',
                    'github_url' => $provider === 'github' ? "https://github.com/{$user->name}" : null,
                    'job_success_score' => 100,
                ]);
            } else {
                ClientProfile::create([
                    'user_id' => $user->id,
                    'company_name' => "{$user->name}'s Enterprise",
                    'industry' => 'Technology & Digital Services',
                    'tagline' => 'Building next-generation digital products',
                    'about' => 'Verified enterprise client hiring specialized talent.',
                    'payment_verified' => true,
                ]);
            }

            // Create initial wallet
            Wallet::firstOrCreate(['user_id' => $user->id], [
                'balance' => 0.00,
                'escrow_balance' => 0.00,
                'currency' => 'USD',
            ]);

            // Send Welcome Email
            try {
                Mail::to($user->email)->send(
                    new MarketplaceNotificationMail(
                        subject: '🚀 Welcome to WorkForge via ' . ucfirst($provider) . '!',
                        greeting: 'Welcome ' . $user->name . '!',
                        mainMessage: 'Your WorkForge account has been created via ' . ucfirst($provider) . ' 1-Click Login. You can now start ' . ($role === 'client' ? 'posting jobs and hiring talent' : 'bidding on projects and completing your profile') . '.',
                        actionUrl: route('dashboard'),
                        actionText: 'Go to Your Dashboard',
                        details: [
                            'Account Role' => ucfirst($role),
                            'Sign-In Method' => ucfirst($provider) . ' OAuth',
                            'Status' => 'Active & Verified',
                        ]
                    )
                );
            } catch (\Throwable $e) {
                Log::warning('Welcome email failed: ' . $e->getMessage());
            }
        } else {
            // Update provider ID if linking existing email
            $updates = [];
            if (empty($user->{$providerIdField})) {
                $updates[$providerIdField] = $providerId;
            }
            if (empty($user->avatar) && $avatar) {
                $updates['avatar'] = $avatar;
            }
            if (!empty($updates)) {
                $user->update($updates);
            }
        }

        Auth::login($user, true);

        $welcomeMsg = $isNewUser
            ? "Welcome to WorkForge, {$user->name}! Your account was created with " . ucfirst($provider) . "."
            : "Welcome back, {$user->name}!";

        return redirect()->intended(route('dashboard'))->with('success', $welcomeMsg);
    }
}
