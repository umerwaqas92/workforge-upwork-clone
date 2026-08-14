@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Users & Identity Management</h1>
        <p class="text-xs text-slate-400 mt-1">Control client and freelancer accounts, balances, and verification states.</p>
    </div>

    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-6 overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead>
                <tr class="border-b border-slate-800 text-slate-400 uppercase font-semibold">
                    <th class="pb-3">User</th>
                    <th class="pb-3">Role</th>
                    <th class="pb-3">Country</th>
                    <th class="pb-3">Wallet Balance</th>
                    <th class="pb-3">Joined</th>
                    <th class="pb-3 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @foreach($users as $u)
                    <tr class="hover:bg-slate-850 transition">
                        <td class="py-3.5 flex items-center gap-3">
                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-lg object-cover">
                            <div>
                                <span class="font-bold text-white block">{{ $u->name }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $u->email }}</span>
                            </div>
                        </td>
                        <td class="py-3.5">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $u->role === 'client' ? 'bg-blue-500/20 text-blue-400' : ($u->role === 'freelancer' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-purple-500/20 text-purple-400') }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="py-3.5 text-slate-400">{{ $u->country ?? 'Worldwide' }}</td>
                        <td class="py-3.5 font-bold text-white">${{ number_format($u->wallet->balance ?? 0, 2) }}</td>
                        <td class="py-3.5 text-slate-400">{{ $u->created_at->format('M d, Y') }}</td>
                        <td class="py-3.5 text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400">
                                {{ ucfirst($u->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
