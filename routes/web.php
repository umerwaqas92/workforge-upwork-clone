<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DodoPaymentController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Public Marketplace Routes (SSR & SEO Optimized)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/freelancers', [FreelancerController::class, 'index'])->name('freelancers.index');
Route::get('/freelancers/{id}', [FreelancerController::class, 'show'])->name('freelancers.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');

// 1-Click Social OAuth (Google & GitHub)
Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\OAuthController::class, 'callback'])->name('oauth.callback');
Route::post('/auth/{provider}/simulate', [\App\Http\Controllers\OAuthController::class, 'handleSimulatedAuth'])->name('oauth.simulate');

// Dodo Payments Webhook & Public Return
Route::post('/payments/dodo/webhook', [DodoPaymentController::class, 'webhook'])->name('payments.dodo.webhook');
Route::get('/payments/dodo/simulator', [DodoPaymentController::class, 'simulator'])->name('payments.dodo.simulator');

// Authenticated User Routes (Blade + Livewire + Alpine.js)
Route::middleware('auth')->group(function () {
    // Dashboards & Settings
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings/profile', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::post('/settings/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/upload/image', [DashboardController::class, 'uploadImage'])->name('upload.image');

    // Jobs Post & Save (Client & Freelancer)
    Route::get('/post-job', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/post-job', [JobController::class, 'store'])->name('jobs.store');
    Route::post('/jobs/{job}/save', [JobController::class, 'toggleSave'])->name('jobs.save');

    // Proposals
    Route::get('/jobs/{job:slug}/apply', [ProposalController::class, 'create'])->name('proposals.create');
    Route::post('/jobs/{job:slug}/apply', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');
    Route::patch('/proposals/{proposal}/status', [ProposalController::class, 'updateStatus'])->name('proposals.status');

    // Contracts, Milestones & Escrow
    Route::get('/proposals/{proposal}/hire', [ContractController::class, 'createFromProposal'])->name('contracts.hire');
    Route::post('/proposals/{proposal}/hire', [ContractController::class, 'storeHire'])->name('contracts.hire.store');
    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    Route::post('/contracts/{contract}/complete', [ContractController::class, 'completeContract'])->name('contracts.complete');
    Route::post('/contracts/{contract}/review', [ContractController::class, 'submitReview'])->name('contracts.review');
    Route::post('/contracts/milestones/{milestone}/fund', [ContractController::class, 'fundMilestone'])->name('contracts.milestone.fund');
    Route::post('/contracts/milestones/{milestone}/submit', [ContractController::class, 'submitWork'])->name('contracts.milestone.submit');
    Route::post('/contracts/milestones/{milestone}/release', [ContractController::class, 'releasePayment'])->name('contracts.milestone.release');

    // Dodo Payments Checkout & Return
    Route::post('/payments/dodo/checkout', [DodoPaymentController::class, 'checkout'])->name('payments.dodo.checkout');
    Route::get('/payments/dodo/return', [DodoPaymentController::class, 'returnUrl'])->name('payments.dodo.return');

    // Wallet & Financial Ledger
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/wallet/payout', [WalletController::class, 'requestPayout'])->name('wallet.payout');

    // Real-Time Chat Room
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/start', fn() => redirect()->route('messages.index'));
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/start', [MessageController::class, 'start'])->name('messages.start');
});

// Admin Panel Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/disputes/{dispute}/resolve', [AdminController::class, 'resolveDispute'])->name('disputes.resolve');
    Route::post('/payouts/{payout}/approve', [AdminController::class, 'approvePayout'])->name('payouts.approve');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('users.status');
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs');
    Route::patch('/jobs/{job}/status', [AdminController::class, 'updateJobStatus'])->name('jobs.status');
    Route::get('/contracts', [AdminController::class, 'contracts'])->name('contracts');
    Route::get('/payouts', [AdminController::class, 'payouts'])->name('payouts');
    Route::patch('/payouts/{payout}/status', [AdminController::class, 'updatePayoutStatus'])->name('payouts.status');
    Route::get('/revenue', [AdminController::class, 'revenue'])->name('revenue');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

// Automated Cron Web Hook for Freelancer Badge & Reputation Engine
Route::get('/cron/recalculate-badges', function () {
    \Illuminate\Support\Facades\Schema::table('freelancer_profiles', function (\Illuminate\Database\Schema\Blueprint $table) {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('freelancer_profiles', 'badge_tier')) {
            $table->string('badge_tier', 50)->default('none')->after('is_top_rated');
        }
    });

    \Illuminate\Support\Facades\Artisan::call('freelancers:recalculate-badges');
    
    return response()->json([
        'status' => 'success',
        'message' => 'Freelancer badges recalculated successfully!',
        'output' => \Illuminate\Support\Facades\Artisan::output(),
    ]);
})->name('cron.badges');
