@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    activeTab: 'main',
    portfolios: {{ json_encode($user->freelancerProfile->portfolio_items ?? []) }},
    employments: {{ json_encode($user->freelancerProfile->employment_history ?? []) }},
    educations: {{ json_encode($user->freelancerProfile->education ?? []) }},
    certifications: {{ json_encode($user->freelancerProfile->certifications ?? []) }},
    languages: {{ json_encode($user->freelancerProfile->languages ?? [['name' => 'English', 'level' => 'Fluent']]) }},
    
    addPortfolio() {
        this.portfolios.push({ title: '', category: 'Web App', image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=600', description: '', link: '' });
    },
    removePortfolio(idx) {
        this.portfolios.splice(idx, 1);
    },
    addEmployment() {
        this.employments.push({ company: '', title: '', period: '2022 - Present', description: '' });
    },
    removeEmployment(idx) {
        this.employments.splice(idx, 1);
    },
    addEducation() {
        this.educations.push({ school: '', degree: 'Bachelor of Computer Science', year: '2021' });
    },
    removeEducation(idx) {
        this.educations.splice(idx, 1);
    },
    addCert() {
        this.certifications.push({ name: '', issuer: '', year: '{{ date('Y') }}' });
    },
    removeCert(idx) {
        this.certifications.splice(idx, 1);
    },
    addLanguage() {
        this.languages.push({ name: '', level: 'Fluent' });
    },
    removeLanguage(idx) {
        this.languages.splice(idx, 1);
    }
}">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Edit Freelancer Profile & Credentials</h1>
            <p class="text-sm text-slate-500 mt-1">Complete your Upwork-standard profile to unlock top-tier jobs and client invites.</p>
        </div>
        @if($user->isFreelancer())
            <a href="{{ route('freelancers.show', $user->id) }}" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-2">
                <span>View Public Profile &rarr;</span>
            </a>
        @endif
    </div>

    @if($user->isFreelancer())
        <!-- Profile Completeness Score Card -->
        @php
            $completeness = $user->freelancerProfile?->completeness_percentage ?? 80;
            $missingSteps = $user->freelancerProfile?->missing_profile_steps ?? [];
        @endphp
        <div class="mb-8 p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl {{ $completeness >= 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }} flex items-center justify-center font-extrabold text-lg">
                        {{ $completeness }}%
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">
                            {{ $completeness >= 100 ? '🎉 Your profile is 100% complete!' : 'Profile Completeness: ' . $completeness . '%' }}
                        </h3>
                        <p class="text-xs text-slate-500">Profiles above 80% completeness get 4.5x more client invitations and job views.</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-3 py-1.5 rounded-full {{ $completeness >= 100 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                    {{ $completeness >= 100 ? '⭐ All Stars Unlocked' : 'Incomplete' }}
                </span>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden mb-4">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500" style="width: {{ $completeness }}%"></div>
            </div>

            @if(count($missingSteps) > 0)
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Recommended steps to reach 100%:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($missingSteps as $mStep)
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs flex items-center justify-between">
                                <span class="text-slate-700 font-medium">{{ $mStep['step'] }}</span>
                                <span class="font-bold text-emerald-700">{{ $mStep['weight'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Section 1: Basic & Contact Info -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="pb-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">1. Basic Information & Location</h2>
                <p class="text-xs text-slate-400">Your name, profile photo, and working location.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Avatar Image URL</label>
                    <input type="text" name="avatar" value="{{ old('avatar', $user->avatar) }}" placeholder="https://..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}" placeholder="e.g. San Francisco" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Country *</label>
                    <input type="text" name="country" value="{{ old('country', $user->country) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>
        </div>

        @if($user->isFreelancer())
            <!-- Section 2: Title, Rate, & Overview -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="pb-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">2. Professional Title, Rate & Bio</h2>
                    <p class="text-xs text-slate-400">Describe your specialty and set your hourly rate.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Professional Title *</label>
                    <input type="text" name="title" value="{{ old('title', $user->freelancerProfile->title ?? '') }}" placeholder="e.g. Senior Full-Stack Laravel & Vue Architect | 10+ Yrs Exp" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    <p class="text-[11px] text-slate-400 mt-1">Example: "Senior React & Node.js Engineer", "Lead UI/UX Product Designer"</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Hourly Rate ($/hr) *</label>
                        <input type="number" step="0.01" min="5" name="hourly_rate" value="{{ old('hourly_rate', $user->freelancerProfile->hourly_rate ?? 40) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-base font-extrabold text-emerald-700">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Experience Level</label>
                        <select name="experience_level" class="w-full text-xs rounded-xl border-slate-300 py-2.5">
                            <option value="entry" {{ ($user->freelancerProfile->experience_level ?? '') === 'entry' ? 'selected' : '' }}>Entry Level ($)</option>
                            <option value="intermediate" {{ ($user->freelancerProfile->experience_level ?? '') === 'intermediate' ? 'selected' : '' }}>Intermediate ($$)</option>
                            <option value="expert" {{ ($user->freelancerProfile->experience_level ?? 'expert') === 'expert' ? 'selected' : '' }}>Expert ($$$)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Availability</label>
                        <select name="availability" class="w-full text-xs rounded-xl border-slate-300 py-2.5">
                            <option value="available_now" {{ ($user->freelancerProfile->availability ?? '') === 'available_now' ? 'selected' : '' }}>More than 30 hrs/week (Available Now)</option>
                            <option value="open_to_offers" {{ ($user->freelancerProfile->availability ?? '') === 'open_to_offers' ? 'selected' : '' }}>Less than 30 hrs/week (Open to Offers)</option>
                            <option value="not_available" {{ ($user->freelancerProfile->availability ?? '') === 'not_available' ? 'selected' : '' }}>As Needed / Not Available</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Professional Overview / Bio *</label>
                    <textarea name="bio" rows="7" required placeholder="Highlight your key achievements, tech stack expertise, client successes, and working style..." class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm leading-relaxed">{{ old('bio', $user->freelancerProfile->bio ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">GitHub Profile URL</label>
                        <input type="url" name="github_url" value="{{ old('github_url', $user->freelancerProfile->github_url ?? '') }}" placeholder="https://github.com/..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->freelancerProfile->linkedin_url ?? '') }}" placeholder="https://linkedin.com/in/..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Personal Website</label>
                        <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $user->freelancerProfile->portfolio_url ?? '') }}" placeholder="https://..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                    </div>
                </div>
            </div>

            <!-- Section 3: Skills List -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">3. Skills & Technologies</h2>
                        <p class="text-xs text-slate-400">Select skills that match your core competencies.</p>
                    </div>
                </div>

                @php $userSkillIds = $user->skills->pluck('id')->toArray(); @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 max-h-56 overflow-y-auto p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    @foreach($allSkills as $skill)
                        <label class="flex items-center text-xs text-slate-700 hover:text-slate-900 cursor-pointer">
                            <input type="checkbox" name="skills[]" value="{{ $skill->id }}" {{ in_array($skill->id, $userSkillIds) ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500 rounded mr-2">
                            <span>{{ $skill->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Section 4: Portfolio Projects Builder -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">4. Portfolio Projects (<span x-text="portfolios.length"></span>)</h2>
                        <p class="text-xs text-slate-400">Showcase past work, screenshots, live links, and case studies.</p>
                    </div>
                    <button type="button" @click="addPortfolio" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl text-xs font-bold border border-emerald-200/60 transition">
                        + Add Project
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(p, idx) in portfolios" :key="idx">
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4 relative">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider" x-text="'Project #' + (idx + 1)"></span>
                                <button type="button" @click="removePortfolio(idx)" class="text-red-500 hover:text-red-700 text-xs font-bold">
                                    ✕ Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Project Title</label>
                                    <input type="text" name="portfolio_titles[]" x-model="p.title" placeholder="e.g. Enterprise Analytics Portal" class="w-full text-xs rounded-xl border-slate-300 py-2">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Category / Tag</label>
                                    <input type="text" name="portfolio_categories[]" x-model="p.category" placeholder="e.g. SaaS Web App / UI/UX" class="w-full text-xs rounded-xl border-slate-300 py-2">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Cover Image URL</label>
                                    <input type="text" name="portfolio_images[]" x-model="p.image" placeholder="https://images.unsplash.com/..." class="w-full text-xs rounded-xl border-slate-300 py-2">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Live Demo / Repository Link</label>
                                    <input type="text" name="portfolio_links[]" x-model="p.link" placeholder="https://..." class="w-full text-xs rounded-xl border-slate-300 py-2">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Project Description & Results</label>
                                <textarea name="portfolio_descriptions[]" x-model="p.description" rows="2" placeholder="Explain what you built, technical challenges solved, and outcome..." class="w-full text-xs rounded-xl border-slate-300"></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Section 5: Employment History Builder -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">5. Employment / Work History (<span x-text="employments.length"></span>)</h2>
                        <p class="text-xs text-slate-400">Past full-time or contract roles and companies.</p>
                    </div>
                    <button type="button" @click="addEmployment" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl text-xs font-bold border border-emerald-200/60 transition">
                        + Add Experience
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(e, idx) in employments" :key="idx">
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider" x-text="'Position #' + (idx + 1)"></span>
                                <button type="button" @click="removeEmployment(idx)" class="text-red-500 hover:text-red-700 text-xs font-bold">
                                    ✕ Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Company / Organization</label>
                                    <input type="text" name="employment_companies[]" x-model="e.company" placeholder="e.g. Stripe, Acme Corp" class="w-full text-xs rounded-xl border-slate-300 py-2">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Job Role / Title</label>
                                    <input type="text" name="employment_titles[]" x-model="e.title" placeholder="e.g. Senior Software Engineer" class="w-full text-xs rounded-xl border-slate-300 py-2">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Period</label>
                                    <input type="text" name="employment_periods[]" x-model="e.period" placeholder="e.g. 2021 - 2024" class="w-full text-xs rounded-xl border-slate-300 py-2">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Key Responsibilities</label>
                                <textarea name="employment_descriptions[]" x-model="e.description" rows="2" placeholder="Describe achievements, team leadership, or technologies..." class="w-full text-xs rounded-xl border-slate-300"></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Section 6: Education & Certifications Builder -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Education -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">6. Education</h2>
                        <button type="button" @click="addEducation" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">+ Add</button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(ed, idx) in educations" :key="idx">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex justify-between items-center">
                                    <input type="text" name="education_schools[]" x-model="ed.school" placeholder="University / School" class="w-full text-xs font-bold rounded-lg border-slate-300 py-1.5">
                                    <button type="button" @click="removeEducation(idx)" class="text-red-500 ml-2 font-bold text-xs">✕</button>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="education_degrees[]" x-model="ed.degree" placeholder="Degree / Major" class="text-xs rounded-lg border-slate-300 py-1">
                                    <input type="text" name="education_years[]" x-model="ed.year" placeholder="Year" class="text-xs rounded-lg border-slate-300 py-1">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Certifications -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">7. Certifications</h2>
                        <button type="button" @click="addCert" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">+ Add</button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(c, idx) in certifications" :key="idx">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex justify-between items-center">
                                    <input type="text" name="certification_names[]" x-model="c.name" placeholder="Certificate Name (e.g. AWS Solutions Architect)" class="w-full text-xs font-bold rounded-lg border-slate-300 py-1.5">
                                    <button type="button" @click="removeCert(idx)" class="text-red-500 ml-2 font-bold text-xs">✕</button>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="certification_issuers[]" x-model="c.issuer" placeholder="Issuer (e.g. Amazon Web Services)" class="text-xs rounded-lg border-slate-300 py-1">
                                    <input type="text" name="certification_years[]" x-model="c.year" placeholder="Year" class="text-xs rounded-lg border-slate-300 py-1">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        @endif

        @if($user->isClient())
            <!-- Client Specific Company Details -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="pb-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Company Profile Details</h2>
                    <p class="text-xs text-slate-400">Tell freelancers about your brand and hiring missions.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $user->clientProfile->company_name ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Company Website</label>
                        <input type="text" name="company_website" value="{{ old('company_website', $user->clientProfile->company_website ?? '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Industry</label>
                        <input type="text" name="industry" value="{{ old('industry', $user->clientProfile->industry ?? '') }}" placeholder="e.g. SaaS / FinTech" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tagline</label>
                        <input type="text" name="tagline" value="{{ old('tagline', $user->clientProfile->tagline ?? '') }}" placeholder="e.g. Building next-gen enterprise tools" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">About the Company</label>
                    <textarea name="about" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm leading-relaxed">{{ old('about', $user->clientProfile->about ?? '') }}</textarea>
                </div>
            </div>
        @endif

        <div class="sticky bottom-6 z-30 p-4 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200 shadow-xl flex items-center justify-between gap-4">
            <span class="text-xs text-slate-500 hidden sm:inline">Make sure all sections are saved to update your public profile.</span>
            <div class="flex items-center gap-3 ml-auto">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-800">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md transition">
                    Save Profile & Credentials
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
