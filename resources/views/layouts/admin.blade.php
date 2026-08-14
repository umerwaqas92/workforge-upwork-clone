<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Super-Panel | WorkForge</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full flex bg-slate-950 text-slate-100 font-sans">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0 min-h-screen">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center font-extrabold text-white">W</div>
                <span class="font-extrabold text-white text-lg tracking-tight">AdminForge</span>
            </a>
            <span class="px-2 py-0.5 text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 rounded-full">v1.0</span>
        </div>

        <nav class="flex-1 p-4 space-y-1 text-xs font-semibold">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <span>📊 Dashboard</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users') ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <span>👥 Users & Talent</span>
            </a>
            <a href="{{ route('admin.jobs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.jobs') ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <span>💼 Job Moderation</span>
            </a>
            <a href="{{ route('admin.contracts') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.contracts') ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <span>📜 Contracts & Escrow</span>
            </a>
            <a href="{{ route('admin.payouts') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.payouts') ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <span>💰 Payouts & Ledger</span>
            </a>
            <div class="pt-4 border-t border-slate-800 my-2"></div>
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-emerald-400 hover:bg-slate-800 transition">
                <span>🌐 Public Marketplace &rarr;</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 text-xs">
            <div class="flex items-center justify-between text-slate-400">
                <span>{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300 font-bold">Exit</button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        <!-- Top Flash Notice -->
        @if(session('success'))
            <div class="m-6 p-4 rounded-2xl bg-emerald-950 border border-emerald-800 text-emerald-200 text-xs font-bold flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <main class="flex-1 p-6 sm:p-10">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
