<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\JobPosting;
use App\Models\Skill;
use Livewire\Component;
use Livewire\WithPagination;

class BrowseJobs extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $selectedExperience = [];
    public $jobType = '';
    public $sortBy = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'jobType' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingJobType()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->selectedExperience = [];
        $this->jobType = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $query = JobPosting::with(['client.clientProfile', 'category', 'skills'])
            ->where('status', 'open');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('skills', function ($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (!empty($this->jobType)) {
            $query->where('type', $this->jobType);
        }

        if (!empty($this->selectedExperience)) {
            $query->whereIn('experience_level', $this->selectedExperience);
        }

        // Pin featured jobs to the top
        $query->orderByDesc('is_featured');

        if ($this->sortBy === 'budget_high') {
            $query->orderByRaw('COALESCE(budget_max, hourly_rate_max, 0) DESC');
        } elseif ($this->sortBy === 'proposals_low') {
            $query->orderBy('proposals_count', 'asc');
        } else {
            $query->latest('published_at');
        }

        $jobs = $query->paginate(8);
        $categories = Category::orderBy('name')->get();

        return view('livewire.browse-jobs', [
            'jobs' => $jobs,
            'categories' => $categories,
        ]);
    }
}
