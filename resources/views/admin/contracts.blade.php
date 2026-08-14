@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Contracts & Escrow Oversight</h1>
        <p class="text-xs text-slate-400 mt-1">Audit active contracts, escrow commitments, and milestone statuses.</p>
    </div>

    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-6 overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead>
                <tr class="border-b border-slate-800 text-slate-400 uppercase font-semibold">
                    <th class="pb-3">Contract</th>
                    <th class="pb-3">Client</th>
                    <th class="pb-3">Freelancer</th>
                    <th class="pb-3">Amount</th>
                    <th class="pb-3">Platform Fee (10%)</th>
                    <th class="pb-3 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @foreach($contracts as $c)
                    <tr class="hover:bg-slate-850 transition">
                        <td class="py-3.5 max-w-xs truncate">
                            <a href="{{ route('contracts.show', $c->id) }}" class="font-bold text-white hover:text-emerald-400">
                                {{ $c->title }}
                            </a>
                        </td>
                        <td class="py-3.5 text-slate-300">{{ $c->client->name }}</td>
                        <td class="py-3.5 text-slate-300">{{ $c->freelancer->name }}</td>
                        <td class="py-3.5 font-bold text-white">${{ number_format($c->amount, 2) }}</td>
                        <td class="py-3.5 text-emerald-400 font-semibold">${{ number_format($c->amount * 0.10, 2) }}</td>
                        <td class="py-3.5 text-right">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $c->status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                                {{ $c->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            {{ $contracts->links() }}
        </div>
    </div>
</div>
@endsection
