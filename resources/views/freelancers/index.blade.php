@extends('layouts.app')

@section('content')
<div class="bg-slate-900 text-white py-8 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Hire Top Independent Talent</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Discover vetted software engineers, designers, AI experts, and digital consultants.</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <livewire:browse-freelancers />
</div>
@endsection
