@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Payout Requests & Approvals</h1>
        <p class="text-xs text-slate-400 mt-1">Review pending withdrawal requests and release payments to freelancers.</p>
    </div>

    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-6 overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead>
                <tr class="border-b border-slate-800 text-slate-400 uppercase font-semibold">
                    <th class="pb-3">Freelancer</th>
                    <th class="pb-3">Amount</th>
                    <th class="pb-3">Method</th>
                    <th class="pb-3">Account Details</th>
                    <th class="pb-3">Requested</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($payouts as $p)
                    <tr class="hover:bg-slate-850 transition">
                        <td class="py-3.5 font-bold text-white">{{ $p->user->name }}</td>
                        <td class="py-3.5 font-extrabold text-emerald-400 text-sm">${{ number_format($p->amount, 2) }}</td>
                        <td class="py-3.5 uppercase font-medium">{{ str_replace('_', ' ', $p->payout_method) }}</td>
                        <td class="py-3.5 text-slate-400 text-[11px]">{{ json_encode($p->account_details) }}</td>
                        <td class="py-3.5 text-slate-400">{{ $p->created_at->format('M d, Y') }}</td>
                        <td class="py-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $p->status === 'pending' ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="py-3.5 text-right">
                            @if($p->status === 'pending')
                                <form action="{{ route('admin.payouts.approve', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                        Approve & Transfer
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-500 text-[11px]">Processed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No payout requests submitted.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6">
            {{ $payouts->links() }}
        </div>
    </div>
</div>
@endsection
