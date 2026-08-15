@extends('layouts.admin')

@section('content')
<div x-data="{
    activeUser: null,
    showModal: false,
    openUser(user) {
        this.activeUser = user;
        this.showModal = true;
    },
    closeUser() {
        this.showModal = false;
        this.activeUser = null;
    }
}" class="space-y-6">

    <!-- Header & Stats Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Users & Talent Directory</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Audit freelancer & client profiles, portfolios, wallet balances, and account states.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-300">
                Total: <span class="text-emerald-400">{{ $users->total() }}</span> Accounts
            </span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.users') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Search User</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, country..." 
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Role</label>
                <select name="role" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                    <option value="">All Roles</option>
                    <option value="freelancer" {{ request('role') === 'freelancer' ? 'selected' : '' }}>Freelancer</option>
                    <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Client</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded-xl text-xs transition shadow-md shadow-emerald-950/40 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-2 px-3 rounded-xl text-xs transition" title="Clear Filters">✕</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Responsive Table Card -->
    <div class="bg-slate-900/90 rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs text-slate-300 min-w-[700px]">
                <thead>
                    <tr class="bg-slate-950/60 border-b border-slate-800 text-slate-400 uppercase font-bold text-[11px] tracking-wider">
                        <th class="py-3.5 px-5">User</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Country</th>
                        <th class="py-3.5 px-4">Wallet Balance</th>
                        <th class="py-3.5 px-4">Joined</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-700 shrink-0">
                                    <div class="min-w-0">
                                        <span class="font-bold text-white block truncate text-xs sm:text-sm">{{ $u->name }}</span>
                                        <span class="text-slate-400 text-[11px] truncate block">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $u->role === 'client' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : ($u->role === 'freelancer' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-purple-500/20 text-purple-400 border border-purple-500/30') }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span>📍</span>
                                    <span>{{ $u->country ?? 'Worldwide' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-white whitespace-nowrap">
                                ${{ number_format($u->wallet->balance ?? 0, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 whitespace-nowrap">
                                {{ $u->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $u->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($u->status === 'suspended' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'active' ? 'bg-emerald-400' : ($u->status === 'suspended' ? 'bg-red-400' : 'bg-amber-400') }}"></span>
                                    {{ ucfirst($u->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <button type="button" 
                                        @click="openUser({{ Js::from([
                                            'id' => $u->id,
                                            'name' => $u->name,
                                            'email' => $u->email,
                                            'role' => $u->role,
                                            'status' => $u->status,
                                            'country' => $u->country ?? 'Worldwide',
                                            'city' => $u->city ?? 'N/A',
                                            'timezone' => $u->timezone ?? 'UTC',
                                            'phone' => $u->phone ?? 'N/A',
                                            'avatar_url' => $u->avatar_url,
                                            'created_at_formatted' => $u->created_at->format('M d, Y (h:i A)'),
                                            'wallet_balance' => number_format($u->wallet->balance ?? 0, 2),
                                            'freelancer_profile' => $u->freelancerProfile ? [
                                                'title' => $u->freelancerProfile->title ?? 'N/A',
                                                'bio' => $u->freelancerProfile->bio ?? 'No bio provided',
                                                'hourly_rate' => number_format($u->freelancerProfile->hourly_rate ?? 0, 2),
                                                'experience_level' => ucfirst($u->freelancerProfile->experience_level ?? 'intermediate'),
                                                'english_level' => ucfirst(str_replace('_', ' ', $u->freelancerProfile->english_level ?? 'fluent')),
                                                'job_success_score' => $u->freelancerProfile->job_success_score ?? 100,
                                                'total_earnings' => number_format($u->freelancerProfile->total_earnings ?? 0, 2),
                                                'completed_jobs_count' => $u->freelancerProfile->completed_jobs_count ?? 0,
                                                'total_hours_worked' => $u->freelancerProfile->total_hours_worked ?? 0,
                                                'github_url' => $u->freelancerProfile->github_url,
                                                'linkedin_url' => $u->freelancerProfile->linkedin_url,
                                                'portfolio_url' => $u->freelancerProfile->portfolio_url,
                                                'portfolio_items' => $u->freelancerProfile->portfolio_items ?? [],
                                                'skills' => $u->skills->map(fn($s) => ['name' => $s->name, 'level' => $s->pivot->proficiency_level ?? 'intermediate']),
                                            ] : null,
                                            'client_profile' => $u->clientProfile ? [
                                                'company_name' => $u->clientProfile->company_name ?? 'Individual Client',
                                                'company_website' => $u->clientProfile->company_website,
                                                'company_size' => $u->clientProfile->company_size ?? '1-10 employees',
                                                'industry' => $u->clientProfile->industry ?? 'General',
                                                'tagline' => $u->clientProfile->tagline ?? '',
                                                'about' => $u->clientProfile->about ?? 'No company description provided',
                                                'payment_verified' => (bool) $u->clientProfile->payment_verified,
                                                'total_spent' => number_format($u->clientProfile->total_spent ?? 0, 2),
                                                'hires_count' => $u->clientProfile->hires_count ?? 0,
                                                'active_contracts_count' => $u->clientProfile->active_contracts_count ?? 0,
                                            ] : null,
                                            'public_url' => $u->role === 'freelancer' ? route('freelancers.show', $u->id) : null,
                                            'status_route' => route('admin.users.status', $u->id),
                                        ]) }})"
                                        class="px-3 py-1.5 rounded-xl bg-slate-850 hover:bg-emerald-600 hover:text-white text-emerald-400 font-bold border border-slate-750 hover:border-emerald-500 text-xs transition inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Inspect Profile</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="space-y-2">
                                    <span class="text-3xl block">👥</span>
                                    <span class="text-sm font-semibold block">No user accounts found matching your query.</span>
                                    <a href="{{ route('admin.users') }}" class="text-xs text-emerald-400 hover:underline">Reset search filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- User & Profile Details Interactive Dialog Modal -->
    <div x-show="showModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
         @keydown.escape.window="closeUser()">

        <div @click.outside="closeUser()" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative">

            <!-- Modal Header -->
            <div class="p-5 sm:p-6 border-b border-slate-800 bg-slate-950/70 flex items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <img :src="activeUser?.avatar_url" :alt="activeUser?.name" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-emerald-500/50 shadow-md">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-lg sm:text-xl font-extrabold text-white" x-text="activeUser?.name"></h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                  :class="{
                                      'bg-blue-500/20 text-blue-400 border border-blue-500/30': activeUser?.role === 'client',
                                      'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': activeUser?.role === 'freelancer',
                                      'bg-purple-500/20 text-purple-400 border border-purple-500/30': activeUser?.role === 'admin'
                                  }"
                                  x-text="activeUser?.role"></span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                  :class="{
                                      'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': activeUser?.status === 'active',
                                      'bg-red-500/20 text-red-400 border border-red-500/30': activeUser?.status === 'suspended',
                                      'bg-amber-500/20 text-amber-400 border border-amber-500/30': activeUser?.status === 'pending'
                                  }"
                                  x-text="activeUser?.status"></span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="activeUser?.email"></p>
                    </div>
                </div>

                <button @click="closeUser()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-6 text-xs text-slate-300">
                
                <!-- Quick Key Metrics Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">Wallet Balance</span>
                        <span class="text-base font-extrabold text-white mt-0.5 block" x-text="'$' + (activeUser?.wallet_balance || '0.00')"></span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">Location</span>
                        <span class="text-xs font-bold text-white mt-0.5 block truncate" x-text="activeUser?.city + ', ' + activeUser?.country"></span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">Timezone</span>
                        <span class="text-xs font-bold text-white mt-0.5 block truncate" x-text="activeUser?.timezone"></span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">Member Since</span>
                        <span class="text-xs font-bold text-white mt-0.5 block truncate" x-text="activeUser?.created_at_formatted"></span>
                    </div>
                </div>

                <!-- Freelancer Specific Profile Details -->
                <template x-if="activeUser?.role === 'freelancer' && activeUser?.freelancer_profile">
                    <div class="space-y-4 p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                                <span>💼</span>
                                <span>Freelancer Profile Data</span>
                            </h4>
                            <span class="text-[11px] font-bold text-white" x-text="'$' + activeUser.freelancer_profile.hourly_rate + '/hr'"></span>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Professional Title</span>
                            <p class="text-sm font-bold text-white mt-0.5" x-text="activeUser.freelancer_profile.title"></p>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Bio / Overview</span>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed whitespace-pre-line" x-text="activeUser.freelancer_profile.bio"></p>
                        </div>

                        <!-- Stats Row -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2">
                            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-center">
                                <span class="text-[10px] text-slate-400 block">Job Success</span>
                                <span class="text-xs font-bold text-emerald-400" x-text="activeUser.freelancer_profile.job_success_score + '%'"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-center">
                                <span class="text-[10px] text-slate-400 block">Total Earnings</span>
                                <span class="text-xs font-bold text-white" x-text="'$' + activeUser.freelancer_profile.total_earnings"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-center">
                                <span class="text-[10px] text-slate-400 block">Completed Jobs</span>
                                <span class="text-xs font-bold text-white" x-text="activeUser.freelancer_profile.completed_jobs_count"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-center">
                                <span class="text-[10px] text-slate-400 block">Experience</span>
                                <span class="text-xs font-bold text-white" x-text="activeUser.freelancer_profile.experience_level"></span>
                            </div>
                        </div>

                        <!-- Skills List -->
                        <div x-show="activeUser.freelancer_profile.skills?.length > 0">
                            <span class="text-[10px] font-bold uppercase text-slate-400 block mb-1.5">Tagged Skills</span>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="skill in activeUser.freelancer_profile.skills" :key="skill.name">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-semibold text-slate-200" x-text="skill.name"></span>
                                </template>
                            </div>
                        </div>

                        <!-- External Links -->
                        <div class="flex items-center gap-3 pt-2">
                            <template x-if="activeUser.freelancer_profile.portfolio_url">
                                <a :href="activeUser.freelancer_profile.portfolio_url" target="_blank" class="text-xs text-emerald-400 hover:underline flex items-center gap-1">
                                    <span>🌐 Portfolio</span> ↗
                                </a>
                            </template>
                            <template x-if="activeUser.freelancer_profile.github_url">
                                <a :href="activeUser.freelancer_profile.github_url" target="_blank" class="text-xs text-slate-300 hover:underline flex items-center gap-1">
                                    <span>🐙 GitHub</span> ↗
                                </a>
                            </template>
                            <template x-if="activeUser.freelancer_profile.linkedin_url">
                                <a :href="activeUser.freelancer_profile.linkedin_url" target="_blank" class="text-xs text-blue-400 hover:underline flex items-center gap-1">
                                    <span>💼 LinkedIn</span> ↗
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Client Specific Profile Details -->
                <template x-if="activeUser?.role === 'client' && activeUser?.client_profile">
                    <div class="space-y-4 p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-blue-400 flex items-center gap-1.5">
                                <span>🏢</span>
                                <span>Client Company Profile</span>
                            </h4>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                                  :class="activeUser.client_profile.payment_verified ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400'"
                                  x-text="activeUser.client_profile.payment_verified ? 'Payment Verified ✓' : 'Payment Unverified'"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase text-slate-400 block">Company Name</span>
                                <p class="text-sm font-bold text-white mt-0.5" x-text="activeUser.client_profile.company_name"></p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase text-slate-400 block">Industry & Size</span>
                                <p class="text-xs font-bold text-slate-200 mt-0.5" x-text="activeUser.client_profile.industry + ' (' + activeUser.client_profile.company_size + ')'"></p>
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">About Company</span>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed" x-text="activeUser.client_profile.about"></p>
                        </div>

                        <div class="grid grid-cols-3 gap-2 pt-2">
                            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-center">
                                <span class="text-[10px] text-slate-400 block">Total Spent</span>
                                <span class="text-xs font-bold text-emerald-400" x-text="'$' + activeUser.client_profile.total_spent"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-center">
                                <span class="text-[10px] text-slate-400 block">Hires</span>
                                <span class="text-xs font-bold text-white" x-text="activeUser.client_profile.hires_count"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-center">
                                <span class="text-[10px] text-slate-400 block">Active Contracts</span>
                                <span class="text-xs font-bold text-white" x-text="activeUser.client_profile.active_contracts_count"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Admin Status Moderation Form -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-300">Account Moderation Controls</h4>
                    <form :action="activeUser?.status_route" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        @csrf
                        @method('PATCH')
                        <div class="flex-1">
                            <select name="status" class="w-full bg-slate-900 border border-slate-750 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                                <option value="active" :selected="activeUser?.status === 'active'">Active (Full access)</option>
                                <option value="suspended" :selected="activeUser?.status === 'suspended'">Suspended (Banned from marketplace)</option>
                                <option value="pending" :selected="activeUser?.status === 'pending'">Pending (Verification required)</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2 rounded-xl text-xs transition shadow-md">
                            Update Account Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-800 bg-slate-950/70 flex items-center justify-between gap-3">
                <div>
                    <template x-if="activeUser?.public_url">
                        <a :href="activeUser.public_url" target="_blank" class="text-xs text-emerald-400 hover:underline flex items-center gap-1 font-semibold">
                            <span>View Public Talent Page</span> &rarr;
                        </a>
                    </template>
                </div>
                <button @click="closeUser()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                    Close Dialog
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

