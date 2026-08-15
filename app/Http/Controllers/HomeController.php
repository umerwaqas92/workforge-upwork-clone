<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobPosting;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('jobs')->orderBy('is_popular', 'desc')->orderBy('sort_order')->take(8)->get();
        $featuredJobs = JobPosting::with(['client.clientProfile', 'category', 'skills'])
            ->where('status', 'open')
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->take(6)
            ->get();
        $topFreelancers = User::with(['freelancerProfile', 'skills'])
            ->where('role', 'freelancer')
            ->where('status', 'active')
            ->has('freelancerProfile')
            ->take(4)
            ->get();

        $stats = [
            'total_jobs' => JobPosting::count(),
            'total_freelancers' => User::where('role', 'freelancer')->count(),
            'total_spent' => JobPosting::where('status', 'completed')->sum('budget_max') ?: 185000,
            'client_satisfaction' => '99%',
        ];

        return view('home', compact('categories', 'featuredJobs', 'topFreelancers', 'stats'));
    }
}
