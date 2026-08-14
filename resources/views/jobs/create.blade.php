@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    jobType: '{{ old('type', 'fixed_price') }}',
    
    // Searchable Category State
    categorySearch: '',
    selectedCategoryId: '{{ old('category_id', '') }}',
    selectedCategoryName: '',
    categoryDropdownOpen: false,
    categories: {{ json_encode($categories) }},
    
    get filteredCategories() {
        if (!this.categorySearch.trim()) return this.categories;
        return this.categories.filter(c => c.name.toLowerCase().includes(this.categorySearch.toLowerCase()));
    },
    
    selectCategory(cat) {
        this.selectedCategoryId = cat.id;
        this.selectedCategoryName = cat.name;
        this.categoryDropdownOpen = false;
        this.categorySearch = '';
    },

    // Searchable Skills State
    skillSearch: '',
    allSkills: {{ json_encode($skills) }},
    selectedSkillIds: {{ json_encode(old('skills', [])) }}.map(id => Number(id)),

    get filteredSkills() {
        if (!this.skillSearch.trim()) return this.allSkills;
        return this.allSkills.filter(s => s.name.toLowerCase().includes(this.skillSearch.toLowerCase()));
    },

    get selectedSkillsList() {
        return this.allSkills.filter(s => this.selectedSkillIds.includes(s.id));
    },

    toggleSkill(skillId) {
        skillId = Number(skillId);
        if (this.selectedSkillIds.includes(skillId)) {
            this.selectedSkillIds = this.selectedSkillIds.filter(id => id !== skillId);
        } else {
            this.selectedSkillIds.push(skillId);
        }
    },

    init() {
        if (this.selectedCategoryId) {
            const found = this.categories.find(c => c.id == this.selectedCategoryId);
            if (found) this.selectedCategoryName = found.name;
        }
    }
}">
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

        <!-- Searchable Category Selector -->
        <div>
            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Category *</label>
            <p class="text-xs text-slate-400 mb-2">Search and select the discipline that best matches this project.</p>
            
            <input type="hidden" name="category_id" :value="selectedCategoryId" required>

            <div class="relative" @click.away="categoryDropdownOpen = false">
                <!-- Dropdown Trigger Button -->
                <div @click="categoryDropdownOpen = !categoryDropdownOpen" class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm flex items-center justify-between cursor-pointer hover:border-emerald-500 transition shadow-2xs">
                    <span x-text="selectedCategoryName || 'Select or search a category...'" :class="selectedCategoryName ? 'font-bold text-slate-900' : 'text-slate-400'"></span>
                    <div class="flex items-center gap-2">
                        <template x-if="selectedCategoryId">
                            <button type="button" @click.stop="selectedCategoryId = ''; selectedCategoryName = ''" class="text-xs text-slate-400 hover:text-red-500 font-bold p-1">✕</button>
                        </template>
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                <!-- Dropdown Search Panel -->
                <div x-show="categoryDropdownOpen" x-cloak class="absolute left-0 right-0 mt-2 bg-white rounded-2xl border border-slate-200 shadow-2xl z-30 p-3 space-y-2">
                    <div class="relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="categorySearch" placeholder="Type to filter categories (e.g. 'Development', 'AI', 'Design')..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="max-h-56 overflow-y-auto space-y-1 pt-1">
                        <template x-for="cat in filteredCategories" :key="cat.id">
                            <div @click="selectCategory(cat)" class="px-3 py-2.5 rounded-xl hover:bg-emerald-50 cursor-pointer flex items-center justify-between text-xs transition" :class="selectedCategoryId == cat.id ? 'bg-emerald-50/80 font-bold text-emerald-800' : 'text-slate-700'">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full" :class="selectedCategoryId == cat.id ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                                    <span x-text="cat.name"></span>
                                </div>
                                <template x-if="selectedCategoryId == cat.id">
                                    <span class="text-emerald-600 font-bold">✓ Selected</span>
                                </template>
                            </div>
                        </template>
                        <template x-if="filteredCategories.length === 0">
                            <p class="text-xs text-slate-400 text-center py-4">No categories match your search.</p>
                        </template>
                    </div>
                </div>
            </div>
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
                    <option value="entry" {{ old('experience_level') === 'entry' ? 'selected' : '' }}>Entry level ($)</option>
                    <option value="intermediate" {{ old('experience_level', 'intermediate') === 'intermediate' ? 'selected' : '' }}>Intermediate ($$)</option>
                    <option value="expert" {{ old('experience_level') === 'expert' ? 'selected' : '' }}>Expert ($$$)</option>
                </select>
            </div>

            <div>
                <label for="duration" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Estimated Duration *</label>
                <select name="duration" id="duration" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-emerald-500">
                    <option value="less_than_1_month" {{ old('duration') === 'less_than_1_month' ? 'selected' : '' }}>Less than 1 month</option>
                    <option value="1_to_3_months" {{ old('duration', '1_to_3_months') === '1_to_3_months' ? 'selected' : '' }}>1 to 3 months</option>
                    <option value="3_to_6_months" {{ old('duration') === '3_to_6_months' ? 'selected' : '' }}>3 to 6 months</option>
                    <option value="more_than_6_months" {{ old('duration') === 'more_than_6_months' ? 'selected' : '' }}>More than 6 months</option>
                </select>
            </div>
        </div>

        <!-- Searchable Skills Picker Component -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Required Skills (Search & Select)</label>
                    <p class="text-xs text-slate-400 mt-0.5">Filter skills dynamically or search by name to attach to this job.</p>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60" x-text="selectedSkillIds.length + ' selected'"></span>
            </div>

            <!-- Hidden Inputs for Form Submission -->
            <template x-for="sId in selectedSkillIds" :key="sId">
                <input type="hidden" name="skills[]" :value="sId">
            </template>

            <!-- Search Input -->
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="skillSearch" placeholder="Type to search skills (e.g. Laravel, React, Figma, Python, Docker, AWS)..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <template x-if="skillSearch">
                    <button type="button" @click="skillSearch = ''" class="absolute right-3.5 top-3 text-xs text-slate-400 hover:text-slate-600 font-bold p-1">✕ Clear</button>
                </template>
            </div>

            <!-- Selected Skills Active Cloud -->
            <template x-if="selectedSkillsList.length > 0">
                <div class="p-3 bg-emerald-50/60 rounded-2xl border border-emerald-200/60 flex flex-wrap gap-2 items-center">
                    <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider mr-1">Active:</span>
                    <template x-for="sk in selectedSkillsList" :key="sk.id">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-600 text-white text-xs font-bold shadow-xs">
                            <span x-text="sk.name"></span>
                            <button type="button" @click="toggleSkill(sk.id)" class="hover:text-emerald-200 font-bold text-xs leading-none">✕</button>
                        </span>
                    </template>
                </div>
            </template>

            <!-- Filtered Skills Grid -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 max-h-56 overflow-y-auto">
                <div class="flex flex-wrap gap-2">
                    <template x-for="sk in filteredSkills" :key="sk.id">
                        <button type="button" 
                                @click="toggleSkill(sk.id)" 
                                :class="selectedSkillIds.includes(sk.id) ? 'bg-emerald-600 text-white font-bold border-emerald-600 shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border-slate-200'"
                                class="px-3 py-1.5 rounded-xl border text-xs transition flex items-center gap-1.5 cursor-pointer">
                            <span x-text="selectedSkillIds.includes(sk.id) ? '✓ ' + sk.name : '+ ' + sk.name"></span>
                        </button>
                    </template>
                    <template x-if="filteredSkills.length === 0">
                        <p class="text-xs text-slate-400 text-center w-full py-4">No skills match "<span x-text="skillSearch"></span>".</p>
                    </template>
                </div>
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
