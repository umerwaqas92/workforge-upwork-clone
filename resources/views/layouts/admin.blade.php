<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Admin Super-Panel | WorkForge</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.6); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="min-h-full flex flex-col lg:flex-row bg-slate-950 text-slate-100 font-sans selection:bg-emerald-500 selection:text-slate-950">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div x-show="sidebarOpen" 
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 lg:hidden">
    </div>

    <!-- Sidebar (Responsive Mobile Drawer + Desktop Fixed) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-72 lg:w-64 bg-slate-900/95 lg:bg-slate-900 border-r border-slate-800/80 flex flex-col shrink-0 transition-transform duration-300 ease-in-out lg:static lg:min-h-screen">
        
        <!-- Sidebar Brand Header -->
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center font-black text-white shadow-lg shadow-emerald-950/50 group-hover:scale-105 transition-transform">
                    W
                </div>
                <div>
                    <span class="font-extrabold text-white text-base tracking-tight block">WorkForge</span>
                    <span class="text-[10px] font-semibold text-emerald-400 uppercase tracking-wider block">Super Admin</span>
                </div>
            </a>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-md">v1.2</span>
                <!-- Close Button (Mobile Only) -->
                <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1.5 text-xs font-semibold overflow-y-auto custom-scrollbar">
            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">Core Management</div>
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                <span class="text-base">📊</span>
                <span class="font-bold text-sm">Dashboard</span>
            </a>
            
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                <span class="text-base">👥</span>
                <span class="font-bold text-sm">Users & Profiles</span>
            </a>
            
            <a href="{{ route('admin.jobs') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.jobs') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                <span class="text-base">💼</span>
                <span class="font-bold text-sm">Job Postings</span>
            </a>
            
            <a href="{{ route('admin.contracts') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.contracts') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                <span class="text-base">📜</span>
                <span class="font-bold text-sm">Contracts & Escrow</span>
            </a>
            
            <a href="{{ route('admin.payouts') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.payouts') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                <span class="text-base">💰</span>
                <span class="font-bold text-sm">Payout Requests</span>
            </a>

            <a href="{{ route('admin.revenue') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.revenue') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                <span class="text-base">📈</span>
                <span class="font-bold text-sm">Revenue & Fees</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                <span class="text-base">⚙️</span>
                <span class="font-bold text-sm">Platform Settings</span>
            </a>

            <div class="pt-4 border-t border-slate-800/80 my-3"></div>
            
            <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">Live Shortcuts</div>
            
            <a href="{{ route('jobs.index') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 rounded-xl text-slate-400 hover:text-emerald-400 hover:bg-slate-800/60 transition">
                <span class="flex items-center gap-2.5">
                    <span>🔍</span>
                    <span>Browse Jobs</span>
                </span>
                <span class="text-[10px] text-slate-600">↗</span>
            </a>

            <a href="{{ route('freelancers.index') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 rounded-xl text-slate-400 hover:text-emerald-400 hover:bg-slate-800/60 transition">
                <span class="flex items-center gap-2.5">
                    <span>🌟</span>
                    <span>Find Talent</span>
                </span>
                <span class="text-[10px] text-slate-600">↗</span>
            </a>

            <a href="{{ route('home') }}" class="flex items-center justify-between px-3.5 py-2 rounded-xl text-slate-400 hover:text-emerald-400 hover:bg-slate-800/60 transition">
                <span class="flex items-center gap-2.5">
                    <span>🌐</span>
                    <span>Main Site</span>
                </span>
                <span class="text-[10px] text-slate-600">➔</span>
            </a>
        </nav>

        <!-- Sidebar User Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-900/50">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-700">
                    <div class="min-w-0">
                        <span class="font-bold text-white text-xs truncate block">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] text-emerald-400 font-semibold block">Admin</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Navigation Bar for Mobile & Desktop -->
        <header class="sticky top-0 z-30 bg-slate-900/90 backdrop-blur-md border-b border-slate-800 px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Mobile Hamburger Button -->
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div>
                    <h2 class="text-sm sm:text-base font-extrabold text-white tracking-tight flex items-center gap-2">
                        @if(request()->routeIs('admin.dashboard')) 📊 Overview Dashboard
                        @elseif(request()->routeIs('admin.users')) 👥 Users & Talent Profiles
                        @elseif(request()->routeIs('admin.jobs')) 💼 Job Moderation
                        @elseif(request()->routeIs('admin.contracts')) 📜 Contracts & Escrow
                        @elseif(request()->routeIs('admin.payouts')) 💰 Payout Requests
                        @else Super Admin Control
                        @endif
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 text-[11px] text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>System Live</span>
                </div>
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-750 text-slate-200 hover:text-white text-xs font-semibold border border-slate-700 transition flex items-center gap-1.5">
                    <span>View Site</span>
                    <span class="text-slate-400">&rarr;</span>
                </a>
            </div>
        </header>

        <!-- Flash Notice Notification -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mx-4 sm:mx-8 mt-4 p-4 rounded-2xl bg-emerald-950/80 border border-emerald-800 text-emerald-200 text-xs font-bold flex items-center justify-between shadow-lg shadow-emerald-950/30">
                <div class="flex items-center gap-2.5">
                    <span class="text-base">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-white text-sm font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mx-4 sm:mx-8 mt-4 p-4 rounded-2xl bg-red-950/80 border border-red-800 text-red-200 text-xs font-bold flex items-center justify-between shadow-lg shadow-red-950/30">
                <div class="flex items-center gap-2.5">
                    <span class="text-base">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-400 hover:text-white text-sm font-bold">&times;</button>
            </div>
        @endif

        <!-- Main Page Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
