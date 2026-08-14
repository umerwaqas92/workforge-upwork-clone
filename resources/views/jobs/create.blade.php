@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ jobType: 'fixed_price' }">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Post a New Job</h1>
        <p class="text-sm text-slate-500 mt-1">Connect with thousands of skilled freelancers ready to execute your vision.</p>
    </div>

    <form action="{{ route('jobs.store') }}" method="POST" class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200/80 shadow-sm space-y-8">
        @csrf

        <!-- Title -->
        <div>
            <label for="title" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Job Post Title *</label>
            <p class="text-xs text-slate-400 mb-2">Write a clear title describing what needs to be accomplished.</p>
            <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="e.g. Senior Laravel & Livewire Engineer for Multi-Tenant SaaS" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
            @error('title')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category -->
        <div>
            <label for="category_id" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Category *</label>
            <select name="category_id" id="category_id" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                <option value="">Select a category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Project Description & Requirements *</label>
            <textarea name="description" id="description" rows="6" required placeholder="Describe the responsibilities, tech stack, deliverables, and any deadlines..." class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Job Type & Budget -->
        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-6">
            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Compensation Structure *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label @click="jobType = 'fixed_price'" :class="jobType === 'fixed_price' ? 'border-emerald-600 bg-white ring-2 ring-emerald-500/20' : 'border-slate-200 bg-white'" class="cursor-pointer border rounded-2xl p-4 flex items-center justify-between transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">$</div>
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Fixed-Price Project</span>
                                <span class="text-xs text-slate-400">Pay by approved milestones</span>
                            </div>
                        </div>
                        <input type="radio" name="type" value="fixed_price" x-model="jobType" class="text-emerald-600 focus:ring-emerald-500">
                    </label>

                    <label @click="jobType = 'hourly'" :class="jobType === 'hourly' ? 'border-emerald-600 bg-white ring-2 ring-emerald-500/20' : 'border-slate-200 bg-white'" class="cursor-pointer border rounded-2xl p-4 flex items-center justify-between transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">⏱️</div>
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Hourly Rate</span>
                                <span class="text-xs text-slate-400">Pay for verified hours worked</span>
                            </div>
                        </div>
                        <input type="radio" name="type" value="hourly" x-model="jobType" class="text-emerald-600 focus:ring-emerald-500">
                    </label>
                </div>
            </div>

            <!-- Fixed Price Inputs -->
            <div x-show="jobType === 'fixed_price'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Minimum Budget ($)</label>
                    <input type="number" name="budget_min" placeholder="500" value="{{ old('budget_min') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Maximum Budget ($)</label>
                    <input type="number" name="budget_max" placeholder="1500" value="{{ old('budget_max') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <!-- Hourly Inputs -->
            <div x-show="jobType === 'hourly'" class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-cloak>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Minimum Hourly Rate ($/hr)</label>
                    <input type="number" name="hourly_rate_min" placeholder="30" value="{{ old('hourly_rate_min') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Maximum Hourly Rate ($/hr)</label>
                    <input type="number" name="hourly_rate_max" placeholder="60" value="{{ old('hourly_rate_max') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>
        </div>

        <!-- Experience & Duration -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="experience_level" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Experience Level *</label>
                <select name="experience_level" id="experience_level" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-emerald-500">
                    <option value="entry">Entry level ($)</option>
                    <option value="intermediate" selected>Intermediate ($$)</option>
                    <option value="expert">Expert ($$$)</option>
                </select>
            </div>

            <div>
                <label for="duration" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Estimated Duration *</label>
                <select name="duration" id="duration" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-emerald-500">
                    <option value="less_than_1_month">Less than 1 month</option>
                    <option value="1_to_3_months" selected>1 to 3 months</option>
                    <option value="3_to_6_months">3 to 6 months</option>
                    <option value="more_than_6_months">More than 6 months</option>
                </select>
            </div>
        </div>

        <!-- Skills Picker -->
        <div>
            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Required Skills (Select applicable)</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-3 bg-slate-50 rounded-2xl border border-slate-200">
                @foreach($skills as $sk)
                    <label class="flex items-center text-xs text-slate-700 hover:text-slate-900 cursor-pointer">
                        <input type="checkbox" name="skills[]" value="{{ $sk->id }}" class="text-emerald-600 focus:ring-emerald-500 rounded mr-2">
                        <span>{{ $sk->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-4">
            <a href="{{ route('jobs.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                Publish Job Post
            </button>
        </div>
    </form>
</div>
@endsection
