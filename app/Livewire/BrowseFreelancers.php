<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class BrowseFreelancers extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $experienceLevel = '';
    public $minRate = '';
    public $maxRate = '';
    public $onlyTopRated = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'experienceLevel' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->experienceLevel = '';
        $this->minRate = '';
        $this->maxRate = '';
        $this->onlyTopRated = false;
        $this->resetPage();
    }

    public function render()
    {
        $query = User::with(['freelancerProfile', 'skills', 'reviewsReceived'])
            ->where('role', 'freelancer')
            ->where('status', 'active')
            ->has('freelancerProfile');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('freelancerProfile', function ($fq) {
                      $fq->where('title', 'like', '%' . $this->search . '%')
                         ->orWhere('bio', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('skills', function ($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->experienceLevel)) {
            $query->whereHas('freelancerProfile', function ($fq) {
                $fq->where('experience_level', $this->experienceLevel);
            });
        }

        if ($this->onlyTopRated) {
            $query->whereHas('freelancerProfile', function ($fq) {
                $fq->where('is_top_rated', true);
            });
        }

        if (!empty($this->minRate)) {
            $query->whereHas('freelancerProfile', function ($fq) {
                $fq->where('hourly_rate', '>=', (float) $this->minRate);
            });
        }

        if (!empty($this->maxRate)) {
            $query->whereHas('freelancerProfile', function ($fq) {
                $fq->where('hourly_rate', '<=', (float) $this->maxRate);
            });
        }

        $freelancers = $query->paginate(8);
        $categories = Category::orderBy('name')->get();

        return view('livewire.browse-freelancers', [
            'freelancers' => $freelancers,
            'categories' => $categories,
        ]);
    }
}
