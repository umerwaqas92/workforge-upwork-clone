<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;

class FreelancerController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $skills = Skill::orderBy('name')->take(30)->get();

        return view('freelancers.index', compact('categories', 'skills'));
    }

    public function show($id)
    {
        $freelancer = User::with([
            'freelancerProfile',
            'skills',
            'reviewsReceived.reviewer',
            'freelancerContracts.jobPosting'
        ])
            ->where('role', 'freelancer')
            ->findOrFail($id);

        $completedContracts = $freelancer->freelancerContracts()
            ->where('status', 'completed')
            ->with(['jobPosting', 'reviews'])
            ->latest('end_date')
            ->get();

        return view('freelancers.show', compact('freelancer', 'completedContracts'));
    }
}
