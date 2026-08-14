@extends('layouts.app')

@section('content')
<div class="min-h-[85vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8" x-data="{ selectedRole: '{{ old('role', 'client') }}' }">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Sign up to hire or find work</h2>
        <p class="mt-2 text-sm text-slate-500">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-500">Log in</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl">
        <div class="bg-white py-8 px-6 shadow-xl rounded-3xl sm:px-10 border border-slate-100">
            <form class="space-y-6" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Role Selection Cards -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-3">I want to:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label @click="selectedRole = 'client'" :class="selectedRole === 'client' ? 'border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300'" class="cursor-pointer border rounded-2xl p-4 flex flex-col justify-between transition">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="radio" name="role" value="client" x-model="selectedRole" class="text-emerald-600 focus:ring-emerald-500">
                            </div>
                            <span class="font-bold text-slate-900 text-sm">Hire for a Project</span>
                            <span class="text-xs text-slate-500 mt-1">I am a client posting jobs and hiring talent.</span>
                        </label>

                        <label @click="selectedRole = 'freelancer'" :class="selectedRole === 'freelancer' ? 'border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-500/20' : 'border-slate-200 hover:border-slate-300'" class="cursor-pointer border rounded-2xl p-4 flex flex-col justify-between transition">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                </div>
                                <input type="radio" name="role" value="freelancer" x-model="selectedRole" class="text-emerald-600 focus:ring-emerald-500">
                            </div>
                            <span class="font-bold text-slate-900 text-sm">Work as a Freelancer</span>
                            <span class="text-xs text-slate-500 mt-1">I am a freelancer looking for projects & contracts.</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Full Name</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Work Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" placeholder="you@domain.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                        <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    </div>
                </div>

                <div>
                    <label for="country" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Country</label>
                    <select id="country" name="country" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        <option value="United States">United States</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Canada">Canada</option>
                        <option value="Germany">Germany</option>
                        <option value="Australia">Australia</option>
                        <option value="India">India</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl shadow-md text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition">
                        Create My Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
