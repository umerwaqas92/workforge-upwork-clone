<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? "WorkForge | The World's Work Marketplace")</title>
    <meta name="description" content="@yield('meta_description', 'Hire top 1% independent talent or discover high-paying freelance projects with milestone escrow protection on WorkForge.')">
    <meta name="keywords" content="@yield('meta_keywords', 'freelance marketplace, hire developers, freelance jobs, upwork clone, milestone escrow, remote talent')">
    <meta name="author" content="WorkForge Marketplace">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <!-- Open Graph / Facebook / WhatsApp / LinkedIn / Slack -->
    <meta property="og:site_name" content="WorkForge Marketplace">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', $title ?? "WorkForge | The World's Work Marketplace")">
    <meta property="og:description" content="@yield('og_description', 'Hire top 1% independent talent or discover high-paying freelance projects with milestone escrow protection.')">
    <meta property="og:image" content="@yield('og_image', asset('images/workforge-og.svg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card Meta -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('og_title', $title ?? "WorkForge | The World's Work Marketplace")">
    <meta name="twitter:description" content="@yield('og_description', 'Hire top 1% independent talent or discover high-paying freelance projects with milestone escrow protection.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/workforge-og.svg'))">

    <!-- Schema.org Organization Structured Data -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "Organization",
        "name": "WorkForge Marketplace",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('favicon.svg') }}",
        "sameAs": [
            "https://github.com/umerwaqas92/workforge-upwork-clone"
        ]
    }
    </script>
    @yield('structured_data')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full flex flex-col font-sans bg-slate-50 text-slate-900 selection:bg-emerald-500 selection:text-white" x-data="{ mobileMenuOpen: false, userDropdownOpen: false }">

    <!-- Quick Role Switcher Banner for Easy Demo Testing -->
    <div class="bg-slate-900 text-slate-300 text-xs px-4 py-1.5 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1 font-medium text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                Demo Environment Quick Switcher:
            </span>
            <span class="hidden sm:inline text-slate-400">Switch persona instantly to test complete marketplace workflows:</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('quick.login', 'client') }}" class="px-2 py-0.5 rounded {{ Auth::check() && Auth::user()->isClient() ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-800 hover:bg-slate-700 text-slate-200' }} transition">Client (Marcus)</a>
            <a href="{{ route('quick.login', 'freelancer') }}" class="px-2 py-0.5 rounded {{ Auth::check() && Auth::user()->isFreelancer() ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-800 hover:bg-slate-700 text-slate-200' }} transition">Freelancer (Alex)</a>
            <a href="{{ route('quick.login', 'admin') }}" class="px-2 py-0.5 rounded {{ Auth::check() && Auth::user()->isAdmin() ? 'bg-emerald-600 text-white font-semibold' : 'bg-slate-800 hover:bg-slate-700 text-slate-200' }} transition">Admin Panel</a>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Main Links -->
                <div class="flex items-center gap-8">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-emerald-600/20 group-hover:bg-emerald-700 transition">
                            <span class="tracking-tighter">W</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold tracking-tight text-slate-900 group-hover:text-emerald-700 transition">WorkForge</span>
                            <span class="text-[10px] font-medium tracking-widest text-slate-600 -mt-1 uppercase">Marketplace</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                        <a href="{{ route('jobs.index') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('jobs.*') ? 'text-emerald-600 font-semibold' : '' }}">Find Work</a>
                        <a href="{{ route('freelancers.index') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('freelancers.*') ? 'text-emerald-600 font-semibold' : '' }}">Find Talent</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('dashboard') ? 'text-emerald-600 font-semibold' : '' }}">Dashboard</a>
                            <a href="{{ route('messages.index') }}" class="hover:text-emerald-600 transition flex items-center gap-1.5 {{ request()->routeIs('messages.*') ? 'text-emerald-600 font-semibold' : '' }}">
                                Messages
                            </a>
                            <a href="{{ route('wallet.index') }}" class="hover:text-emerald-600 transition {{ request()->routeIs('wallet.*') ? 'text-emerald-600 font-semibold' : '' }}">
                                Wallet (${{ number_format(Auth::user()->wallet->balance ?? 0, 2) }})
                            </a>
                        @endauth
                    </nav>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        @if(Auth::user()->isClient())
                            <a href="{{ route('jobs.create') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-medium bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-full shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Post a Job
                            </a>
                        @endif

                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-900 text-white font-medium px-3 py-1.5 rounded-lg hover:bg-slate-800 transition">Admin Panel</a>
                        @endif

                        <!-- Profile Dropdown -->
                        <div class="relative ml-2" @click.away="userDropdownOpen = false">
                            <button @click="userDropdownOpen = !userDropdownOpen" type="button" class="flex items-center gap-2 p-1 rounded-full hover:ring-2 hover:ring-emerald-500/30 transition focus:outline-none">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                                <span class="hidden lg:block text-xs font-semibold text-slate-800 max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="userDropdownOpen" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95" 
                                 class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-slate-100 py-2 z-50 text-slate-700"
                                 x-cloak>
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-[11px] text-slate-400 font-medium">Signed in as</p>
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-1 text-[10px] uppercase font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </span>
                                </div>
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-slate-50 transition">Dashboard</a>
                                @if(Auth::user()->isFreelancer())
                                    <a href="{{ route('freelancers.show', Auth::id()) }}" class="block px-4 py-2 text-sm hover:bg-slate-50 transition">Public Profile</a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-slate-50 transition">Settings & Profile</a>
                                <a href="{{ route('wallet.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-50 transition">Billing & Wallet</a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">Log Out</button>
                                </form>
                            </div>
                        </div>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 hover:text-emerald-600 px-3 py-2 transition">Log In</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-full shadow-xs hover:shadow transition">Sign Up</a>
                    @endguest

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="mobileMenuOpen" class="md:hidden border-t border-slate-200 bg-white px-4 py-4 space-y-3" x-cloak>
            <a href="{{ route('jobs.index') }}" class="block py-2 text-base font-medium text-slate-700">Find Work</a>
            <a href="{{ route('freelancers.index') }}" class="block py-2 text-base font-medium text-slate-700">Find Talent</a>
            @auth
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 mb-2">
                    <p class="text-xs font-bold text-slate-900">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    <span class="inline-block mt-1.5 text-[10px] uppercase font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full">{{ ucfirst(Auth::user()->role) }}</span>
                </div>

                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-3 bg-slate-900 text-white rounded-xl text-sm font-bold flex items-center justify-between shadow-xs hover:bg-slate-800 transition">
                        <span class="flex items-center gap-2">
                            <span>👑</span>
                            <span>Admin Super-Panel</span>
                        </span>
                        <span class="text-[10px] bg-emerald-600 px-2 py-0.5 rounded-md font-extrabold uppercase">Control</span>
                    </a>
                @endif

                <a href="{{ route('dashboard') }}" class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600 transition">
                    {{ Auth::user()->isFreelancer() ? 'Freelancer Dashboard' : (Auth::user()->isClient() ? 'Client Dashboard' : 'Marketplace Overview') }}
                </a>
                
                @if(Auth::user()->isFreelancer())
                    <a href="{{ route('freelancers.show', Auth::id()) }}" class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600 transition">Public Profile</a>
                @endif

                <a href="{{ route('messages.index') }}" class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600 transition">Messages</a>
                <a href="{{ route('wallet.index') }}" class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600 transition">Billing & Wallet</a>
                <a href="{{ route('profile.edit') }}" class="block py-2 text-base font-medium text-slate-700 hover:text-emerald-600 transition">Settings & Profile</a>
                
                @if(Auth::user()->isClient())
                    <a href="{{ route('jobs.create') }}" class="block py-2.5 px-3 text-center bg-emerald-600 text-white rounded-xl text-sm font-bold hover:bg-emerald-700 transition shadow-xs mt-2">+ Post a New Job</a>
                @endif

                <div class="border-t border-slate-100 pt-2 mt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-2 text-base font-semibold text-red-600 hover:text-red-700 transition">Log Out</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <!-- Flash Notifications -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center justify-between text-emerald-800 animate-fade-in" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-800"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 flex items-center justify-between text-red-800" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 rounded-xl bg-blue-50 border border-blue-200 p-4 flex items-center justify-between text-blue-800" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ session('info') }}</span>
                    </div>
                    <button @click="show = false" class="text-blue-600 hover:text-blue-800"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
        </div>

        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 mt-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider mb-4">For Clients</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('freelancers.index') }}" class="hover:text-white transition">How to Hire</a></li>
                        <li><a href="{{ route('jobs.create') }}" class="hover:text-white transition">Post a Project</a></li>
                        <li><a href="{{ route('freelancers.index') }}" class="hover:text-white transition">Search Freelancers</a></li>
                        <li><a href="#" class="hover:text-white transition">Enterprise Solution</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider mb-4">For Talent</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">How to Find Work</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">Direct Contracts</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">Find Worldwide Jobs</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">Win Proposals</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider mb-4">Top Categories</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">Development & IT</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">AI Services</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">Design & Creative</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">Mobile Development</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-white uppercase tracking-wider mb-4">WorkForge Trust</h3>
                    <p class="text-xs leading-relaxed text-slate-400 mb-4">Protected milestone escrow, verified identities, and seamless collaboration built for modern remote teams.</p>
                    <div class="flex items-center gap-3 text-emerald-400 font-semibold text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>100% Escrow Protected</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} WorkForge Inc. All rights reserved.</p>
                <div class="flex gap-6 mt-4 sm:mt-0">
                    <a href="#" class="hover:text-slate-400 transition">Terms of Service</a>
                    <a href="#" class="hover:text-slate-400 transition">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-400 transition">Accessibility</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
