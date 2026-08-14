@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-extrabold text-white">Platform Super-Panel Overview</h1>
        <p class="text-xs text-slate-400 mt-1">Real-time marketplace metrics, escrow flow, GMV, and take-rate revenue.</p>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-3xl bg-slate-900 border border-slate-800 space-y-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Gross Merchandise Volume (GMV)</span>
            <span class="text-2xl font-extrabold text-white block">${{ number_format($stats['total_volume'], 2) }}</span>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900 border border-slate-800 space-y-1">
            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Platform Take-Rate (10%)</span>
            <span class="text-2xl font-extrabold text-emerald-400 block">${{ number_format($stats['platform_revenue'], 2) }}</span>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900 border border-slate-800 space-y-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Contracts</span>
            <span class="text-2xl font-extrabold text-white block">{{ $stats['active_contracts'] }}</span>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900 border border-slate-800 space-y-1">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Users</span>
            <span class="text-2xl font-extrabold text-white block">{{ $stats['total_users'] }}</span>
            <span class="text-[10px] text-slate-500">{{ $stats['total_freelancers'] }} Freelancers • {{ $stats['total_clients'] }} Clients</span>
        </div>
    </div>

    <!-- Recent Activity Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Contracts -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="font-bold text-sm text-white">Recent Escrow Contracts</h3>
                <a href="{{ route('admin.contracts') }}" class="text-xs text-emerald-400 hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @foreach($recentContracts as $c)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-950 border border-slate-850 text-xs">
                        <div>
                            <span class="font-bold text-white block">{{ $c->title }}</span>
                            <span class="text-slate-400">{{ $c->client->name }} ➔ {{ $c->freelancer->name }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-extrabold text-emerald-400">${{ number_format($c->amount, 2) }}</span>
                            <span class="block text-[10px] text-slate-500 uppercase">{{ $c->status }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="font-bold text-sm text-white">Live Platform Ledger</h3>
                <a href="{{ route('admin.payouts') }}" class="text-xs text-emerald-400 hover:underline">View Payouts</a>
            </div>
            <div class="space-y-3">
                @foreach($recentTransactions as $tx)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-950 border border-slate-850 text-xs">
                        <div>
                            <span class="font-bold text-white block">{{ $tx->description }}</span>
                            <span class="text-slate-400">{{ $tx->user->name }} • {{ $tx->created_at->diffForHumans() }}</span>
                        </div>
                        <span class="font-extrabold text-white">${{ number_format($tx->amount, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
