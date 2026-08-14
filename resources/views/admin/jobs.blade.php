@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Job Postings Moderation</h1>
        <p class="text-xs text-slate-400 mt-1">Review all live, completed, and drafted marketplace projects.</p>
    </div>

    <div class="bg-slate-900 rounded-3xl border border-slate-800 p-6 overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead>
                <tr class="border-b border-slate-800 text-slate-400 uppercase font-semibold">
                    <th class="pb-3">Title</th>
                    <th class="pb-3">Client</th>
                    <th class="pb-3">Category</th>
                    <th class="pb-3">Budget</th>
                    <th class="pb-3">Proposals</th>
                    <th class="pb-3 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @foreach($jobs as $j)
                    <tr class="hover:bg-slate-850 transition">
                        <td class="py-3.5 max-w-xs truncate">
                            <a href="{{ route('jobs.show', $j->slug) }}" class="font-bold text-white hover:text-emerald-400">
                                {{ $j->title }}
                            </a>
                        </td>
                        <td class="py-3.5 text-slate-300">{{ $j->client->name }}</td>
                        <td class="py-3.5 text-slate-400">{{ $j->category->name ?? 'N/A' }}</td>
                        <td class="py-3.5 font-bold text-emerald-400">{{ $j->budget_formatted }}</td>
                        <td class="py-3.5 text-slate-300">{{ $j->proposals_count }}</td>
                        <td class="py-3.5 text-right">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400">
                                {{ $j->status }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection
