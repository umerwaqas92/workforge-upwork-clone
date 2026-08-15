@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{
    platformFee: {{ \App\Models\PlatformSetting::get('platform_fee_percent', 10.0) }},
    clientFee: {{ \App\Models\PlatformSetting::get('client_processing_fee_percent', 3.0) }},
    connectPrice: {{ \App\Models\PlatformSetting::get('connect_cost_usd', 0.15) }},
    calcAmount: 2500,
    calcConnects: 50,
    get adminRevenue() {
        return ((this.calcAmount * this.platformFee) / 100).toFixed(2);
    },
    get freelancerReceives() {
        return (this.calcAmount - this.adminRevenue).toFixed(2);
    },
    get clientDepositTotal() {
        return (this.calcAmount * (1 + this.clientFee / 100)).toFixed(2);
    },
    get clientFeeAmount() {
        return ((this.calcAmount * this.clientFee) / 100).toFixed(2);
    },
    get connectRevenue() {
        return (this.calcConnects * this.connectPrice).toFixed(2);
    }
}">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span>⚙️ Platform Settings & Monetization Engine</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Configure your marketplace commission rates, client payment surcharges, connects token pricing, and badge thresholds.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('cron.badges') }}" target="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition flex items-center gap-2 border border-slate-700">
                <span>🔄 Recalculate Badges Now</span>
            </a>
        </div>
    </div>

    <!-- Live Monetization Revenue Simulation Calculator -->
    <div class="p-6 rounded-3xl bg-linear-to-r from-emerald-950/60 via-slate-900 to-purple-950/60 border border-emerald-500/30 shadow-2xl space-y-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                <h2 class="text-sm font-bold text-emerald-400 uppercase tracking-wider">Live Revenue Simulation & Profit Preview</h2>
            </div>
            <span class="text-[11px] text-slate-400">Updates dynamically as you adjust rates below</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-1">
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-1">
                <span class="text-[11px] font-semibold text-slate-400 block">Sample Contract Size</span>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="text-slate-500 font-bold">$</span>
                    <input type="number" x-model="calcAmount" class="w-full bg-transparent text-xl font-black text-white focus:outline-none border-b border-slate-700 focus:border-emerald-500 pb-0.5">
                </div>
                <span class="text-[10px] text-slate-500 block">Type any amount to test</span>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 space-y-1">
                <span class="text-[11px] font-bold text-emerald-400 block">Admin Platform Take-Rate (<span x-text="platformFee + '%'"></span>)</span>
                <span class="text-2xl font-black text-emerald-400 block" x-text="'$' + Number(adminRevenue).toLocaleString()"></span>
                <span class="text-[10px] text-emerald-300/70 block">Direct platform profit per job</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-1">
                <span class="text-[11px] font-semibold text-slate-400 block">Freelancer Payout</span>
                <span class="text-2xl font-black text-white block" x-text="'$' + Number(freelancerReceives).toLocaleString()"></span>
                <span class="text-[10px] text-slate-500 block">Net credited to freelancer wallet</span>
            </div>

            <div class="p-4 rounded-2xl bg-purple-950/40 border border-purple-500/40 space-y-1">
                <span class="text-[11px] font-bold text-purple-400 block">50 Connects Pack Revenue</span>
                <span class="text-2xl font-black text-purple-300 block" x-text="'$' + Number(connectRevenue).toLocaleString()"></span>
                <span class="text-[10px] text-purple-300/70 block">At $<span x-text="connectPrice"></span> per bidding token</span>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
        @csrf

        @foreach($settings as $group => $items)
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
                <!-- Group Header -->
                <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-lg">
                        @if($group === 'monetization')
                            💵
                        @elseif($group === 'payouts')
                            💸
                        @elseif($group === 'reputation')
                            ⭐
                        @else
                            ⚙️
                        @endif
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-white capitalize">{{ $group }} Settings</h2>
                        <p class="text-xs text-slate-400">Manage {{ $group }} policies and operational variables</p>
                    </div>
                </div>

                <!-- Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($items as $setting)
                        <div class="space-y-2 p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 transition">
                            <div class="flex items-center justify-between">
                                <label for="setting_{{ $setting->key }}" class="text-xs font-bold text-slate-200 uppercase tracking-wider">
                                    {{ $setting->label }}
                                </label>
                                <span class="text-[10px] font-mono text-slate-500">{{ $setting->key }}</span>
                            </div>

                            @if($setting->type === 'boolean')
                                <select name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500">
                                    <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Enabled (True)</option>
                                    <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Disabled (False)</option>
                                </select>
                            @else
                                <div class="relative">
                                    <input 
                                        type="{{ in_array($setting->type, ['integer', 'float', 'decimal']) ? 'number' : 'text' }}" 
                                        step="{{ $setting->type === 'float' ? '0.01' : '1' }}"
                                        name="settings[{{ $setting->key }}]" 
                                        id="setting_{{ $setting->key }}" 
                                        value="{{ $setting->value }}"
                                        @if($setting->key === 'platform_fee_percent')
                                            x-model="platformFee"
                                        @elseif($setting->key === 'client_processing_fee_percent')
                                            x-model="clientFee"
                                        @elseif($setting->key === 'connect_cost_usd')
                                            x-model="connectPrice"
                                        @endif
                                        required
                                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            @endif

                            @if($setting->description)
                                <p class="text-[11px] text-slate-400 leading-relaxed pt-1">
                                    {{ $setting->description }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Floating Save Bar -->
        <div class="sticky bottom-4 z-30 p-4 bg-slate-900/95 backdrop-blur-md rounded-2xl border border-slate-800 shadow-2xl flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Changes take effect immediately across all new contracts, proposals, and fee deductions.</span>
            </div>
            <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-600/30 transition flex items-center gap-2">
                <span>Save All Platform Settings &rarr;</span>
            </button>
        </div>
    </form>
</div>
@endsection
