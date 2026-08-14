<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobPosting;
use App\Models\Proposal;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $skills = Skill::orderBy('name')->take(30)->get();

        return view('jobs.index', compact('categories', 'skills'));
    }

    public function show($slug)
    {
        $job = JobPosting::with(['client.clientProfile', 'category', 'skills', 'proposals.freelancer.freelancerProfile'])
            ->where('slug', $slug)
            ->firstOrFail();

        $existingProposal = null;
        $isSaved = false;

        if (Auth::check()) {
            if (Auth::user()->isFreelancer()) {
                $existingProposal = Proposal::where('job_posting_id', $job->id)
                    ->where('freelancer_id', Auth::id())
                    ->first();
                $isSaved = Auth::user()->savedJobs()->where('job_posting_id', $job->id)->exists();
            }
        }

        $similarJobs = JobPosting::where('id', '!=', $job->id)
            ->where('category_id', $job->category_id)
            ->where('status', 'open')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('jobs.show', compact('job', 'existingProposal', 'isSaved', 'similarJobs'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $skills = Skill::orderBy('name')->get();

        return view('jobs.create', compact('categories', 'skills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:10|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:30',
            'type' => 'required|in:fixed_price,hourly',
            'budget_min' => 'nullable|numeric|min:5',
            'budget_max' => 'nullable|numeric|min:5',
            'hourly_rate_min' => 'nullable|numeric|min:5',
            'hourly_rate_max' => 'nullable|numeric|min:5',
            'experience_level' => 'required|in:entry,intermediate,expert',
            'duration' => 'required|string',
            'weekly_hours' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $job = JobPosting::create([
            'client_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'description' => $validated['description'],
            'type' => $validated['type'],
            'budget_min' => $validated['budget_min'] ?? null,
            'budget_max' => $validated['budget_max'] ?? null,
            'hourly_rate_min' => $validated['hourly_rate_min'] ?? null,
            'hourly_rate_max' => $validated['hourly_rate_max'] ?? null,
            'experience_level' => $validated['experience_level'],
            'duration' => $validated['duration'],
            'weekly_hours' => $validated['weekly_hours'] ?? 'more_than_30',
            'status' => 'open',
            'published_at' => now(),
        ]);

        if (!empty($validated['skills'])) {
            $job->skills()->attach($validated['skills']);
        }

        return redirect()->route('jobs.show', $job->slug)->with('success', 'Job posting published successfully! Freelancers can now discover and submit bids.');
    }

    public function toggleSave(JobPosting $job)
    {
        $user = Auth::user();
        if ($user->savedJobs()->where('job_posting_id', $job->id)->exists()) {
            $user->savedJobs()->detach($job->id);
            $saved = false;
        } else {
            $user->savedJobs()->attach($job->id);
            $saved = true;
        }

        return back()->with('success', $saved ? 'Job saved to your list.' : 'Job removed from saved.');
    }
}
