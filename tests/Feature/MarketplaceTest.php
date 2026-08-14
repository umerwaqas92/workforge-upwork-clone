<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contract;
use App\Models\ContractMilestone;
use App\Models\JobPosting;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('WorkForge');
        $response->assertSee('How work');
    }

    public function test_browse_jobs_page_renders(): void
    {
        $response = $this->get('/jobs');
        $response->assertStatus(200);
        $response->assertSee('Explore Freelance Jobs');
    }

    public function test_browse_freelancers_page_renders(): void
    {
        $response = $this->get('/freelancers');
        $response->assertStatus(200);
        $response->assertSee('Hire Top Independent Talent');
    }

    public function test_job_details_page_renders(): void
    {
        $job = JobPosting::first();
        $response = $this->get('/jobs/' . $job->slug);
        $response->assertStatus(200);
        $response->assertSee($job->title);
    }

    public function test_freelancer_profile_page_renders(): void
    {
        $freelancer = User::where('role', 'freelancer')->first();
        $response = $this->get('/freelancers/' . $freelancer->id);
        $response->assertStatus(200);
        $response->assertSee($freelancer->name);
    }

    public function test_client_can_post_a_job(): void
    {
        $client = User::where('role', 'client')->first();
        $category = Category::first();

        $response = $this->actingAs($client)->post('/post-job', [
            'title' => 'New Awesome Vue.js and Tailwind SaaS Project',
            'category_id' => $category->id,
            'description' => 'We need a rockstar engineer to build a high-performance analytics dashboard from scratch.',
            'type' => 'fixed_price',
            'budget_min' => 1000,
            'budget_max' => 2000,
            'experience_level' => 'expert',
            'duration' => '1_to_3_months',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('job_postings', [
            'client_id' => $client->id,
            'title' => 'New Awesome Vue.js and Tailwind SaaS Project',
        ]);
    }

    public function test_freelancer_can_submit_proposal(): void
    {
        $freelancer = User::where('role', 'freelancer')->where('email', 'sophia.ui@upwork.test')->first();
        $job = JobPosting::where('status', 'open')->first();

        $response = $this->actingAs($freelancer)->post('/jobs/' . $job->slug . '/apply', [
            'bid_amount' => 1500.00,
            'delivery_time_days' => 10,
            'cover_letter' => 'I have 8+ years building enterprise solutions and would love to deliver this project for you.',
            'milestone_titles' => ['Phase 1 Setup', 'Phase 2 Delivery'],
            'milestone_amounts' => [750, 750],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('proposals', [
            'job_posting_id' => $job->id,
            'freelancer_id' => $freelancer->id,
            'bid_amount' => 1500.00,
        ]);
    }

    public function test_client_can_hire_and_create_contract(): void
    {
        $client = User::where('role', 'client')->where('email', 'client@upwork.test')->first();
        $proposal = Proposal::where('status', 'shortlisted')->first();

        $response = $this->actingAs($client)->post('/proposals/' . $proposal->id . '/hire', [
            'title' => 'Automated Test Contract',
            'amount' => 3200,
            'terms' => 'Test terms',
            'milestone_titles' => ['Milestone Alpha', 'Milestone Beta'],
            'milestone_amounts' => [1600, 1600],
            'fund_first_milestone' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contracts', [
            'client_id' => $client->id,
            'amount' => 3200,
        ]);
    }

    public function test_milestone_delivery_and_payment_release(): void
    {
        $client = User::where('role', 'client')->first();
        $contract = Contract::where('status', 'active')->first();
        $milestone = $contract->milestones()->where('status', 'submitted_for_approval')->first();

        // Release payment by client
        $response = $this->actingAs($client)->post('/contracts/milestones/' . $milestone->id . '/release');
        $response->assertRedirect();

        $milestone->refresh();
        $this->assertEquals('approved_and_released', $milestone->status);
    }

    public function test_admin_dashboard_accessible(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Platform Super-Panel Overview');
    }

    public function test_direct_message_start(): void
    {
        $client = User::where('role', 'client')->first();
        $freelancer = User::where('role', 'freelancer')->first();

        $response = $this->actingAs($client)->post('/messages/start', [
            'recipient_id' => $freelancer->id,
            'subject' => 'Project Inquiry',
            'message' => 'Hello, are you available for a project?',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'sender_id' => $client->id,
            'body' => 'Hello, are you available for a project?',
        ]);
    }
}
