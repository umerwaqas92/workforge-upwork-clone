@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="w-12 h-12 rounded-2xl bg-emerald-600 flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4 shadow-lg shadow-emerald-600/30">
            W
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Log in to WorkForge</h2>
        <p class="mt-2 text-sm text-slate-500">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-semibold text-emerald-600 hover:text-emerald-500">Sign up here</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-xl rounded-3xl sm:px-10 border border-slate-100">
            <!-- Quick Login Shortcuts Box -->
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">⚡ 1-Click Demo Login:</p>
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('quick.login', 'client') }}" class="text-center px-2 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition">Client</a>
                    <a href="{{ route('quick.login', 'freelancer') }}" class="text-center px-2 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition">Freelancer</a>
                    <a href="{{ route('quick.login', 'admin') }}" class="text-center px-2 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition">Admin</a>
                </div>
            </div>

            <form class="space-y-5" action="{{ route('login') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Password</label>
                        <a href="#" class="text-xs font-medium text-emerald-600 hover:underline">Forgot password?</a>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                        <label for="remember" class="ml-2 block text-xs text-slate-600">Keep me logged in</label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition">
                        Log In
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
