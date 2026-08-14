<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ClientProfile;
use App\Models\Contract;
use App\Models\ContractMilestone;
use App\Models\Conversation;
use App\Models\FreelancerProfile;
use App\Models\JobPosting;
use App\Models\Message;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\Skill;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Categories
        $categoriesData = [
            [
                'name' => 'Development & IT',
                'slug' => 'development-it',
                'description' => 'Software developers, web engineers, DevOps, and database architects',
                'is_popular' => true,
                'skills' => ['Laravel', 'PHP', 'React', 'Vue.js', 'Tailwind CSS', 'Node.js', 'Python', 'Docker', 'AWS', 'PostgreSQL', 'TypeScript', 'GraphQL', 'Next.js', 'Redis']
            ],
            [
                'name' => 'AI Services',
                'slug' => 'ai-services',
                'description' => 'AI engineers, LLM fine-tuning, RAG pipelines, and prompt engineering',
                'is_popular' => true,
                'skills' => ['Python', 'OpenAI API', 'PyTorch', 'LangChain', 'FastAPI', 'Machine Learning', 'Computer Vision', 'NLP', 'LlamaIndex', 'Vector DB']
            ],
            [
                'name' => 'Design & Creative',
                'slug' => 'design-creative',
                'description' => 'UI/UX designers, design systems, branding, illustration, and motion graphics',
                'is_popular' => true,
                'skills' => ['Figma', 'UI/UX Design', 'Design Systems', 'Web Design', 'Adobe Illustrator', 'Photoshop', 'Brand Identity', 'Motion Graphics', 'Wireframing']
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'iOS, Android, Flutter, and React Native mobile applications',
                'is_popular' => true,
                'skills' => ['Flutter', 'React Native', 'Swift', 'Kotlin', 'iOS Development', 'Android Development', 'Firebase', 'App Store Deployment']
            ],
            [
                'name' => 'Sales & Marketing',
                'slug' => 'sales-marketing',
                'description' => 'Technical SEO, growth hacking, PPC advertising, and performance marketing',
                'is_popular' => true,
                'skills' => ['SEO', 'Google Ads', 'Content Strategy', 'Social Media Marketing', 'Copywriting', 'Email Marketing', 'Conversion Optimization', 'Google Analytics']
            ],
            [
                'name' => 'Writing & Translation',
                'slug' => 'writing-translation',
                'description' => 'Technical writing, content creation, copywriting, and multilingual translation',
                'is_popular' => false,
                'skills' => ['Technical Writing', 'Copywriting', 'Blog Writing', 'Localization', 'Editing & Proofreading', 'API Documentation']
            ],
        ];

        $skillsMap = [];

        foreach ($categoriesData as $index => $catInfo) {
            $category = Category::create([
                'name' => $catInfo['name'],
                'slug' => $catInfo['slug'],
                'description' => $catInfo['description'],
                'sort_order' => $index + 1,
                'is_popular' => $catInfo['is_popular'],
            ]);

            foreach ($catInfo['skills'] as $skillName) {
                $skill = Skill::firstOrCreate([
                    'slug' => Str::slug($skillName),
                ], [
                    'name' => $skillName,
                    'category_id' => $category->id,
                ]);
                $skillsMap[$skillName] = $skill->id;
            }
        }

        // 2. Create Admin User
        $admin = User::create([
            'name' => 'Admin Controller',
            'email' => 'admin@upwork.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'country' => 'United States',
            'city' => 'San Francisco',
            'timezone' => 'America/Los_Angeles',
            'email_verified_at' => now(),
        ]);

        Wallet::create([
            'user_id' => $admin->id,
            'balance' => 24800.00,
            'escrow_balance' => 0.00,
            'currency' => 'USD',
        ]);

        // 3. Create Clients
        $client1 = User::create([
            'name' => 'Marcus Vance',
            'email' => 'client@upwork.test',
            'password' => Hash::make('password'),
            'role' => 'client',
            'status' => 'active',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400',
            'phone' => '+1 (555) 234-5678',
            'country' => 'United States',
            'city' => 'Austin, TX',
            'email_verified_at' => now(),
        ]);

        ClientProfile::create([
            'user_id' => $client1->id,
            'company_name' => 'TechFlow Innovations Inc.',
            'company_website' => 'https://techflow-demo.test',
            'company_size' => '11-50',
            'industry' => 'SaaS / FinTech',
            'tagline' => 'Next-generation cloud infrastructure and analytics',
            'about' => 'TechFlow is a venture-backed enterprise SaaS startup based in Austin. We build scalable SaaS solutions and frequently hire high-caliber freelance engineers and designers for key feature initiatives.',
            'payment_verified' => true,
            'total_spent' => 54600.00,
            'hires_count' => 16,
            'active_contracts_count' => 2,
        ]);

        $clientWallet1 = Wallet::create([
            'user_id' => $client1->id,
            'balance' => 9200.00,
            'escrow_balance' => 1400.00,
            'currency' => 'USD',
        ]);

        $client2 = User::create([
            'name' => 'Elena Rostova',
            'email' => 'sarah.client@upwork.test',
            'password' => Hash::make('password'),
            'role' => 'client',
            'status' => 'active',
            'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=400',
            'phone' => '+1 (555) 876-5432',
            'country' => 'United Kingdom',
            'city' => 'London',
            'email_verified_at' => now(),
        ]);

        ClientProfile::create([
            'user_id' => $client2->id,
            'company_name' => 'Nexus Product Studio',
            'company_website' => 'https://nexusstudio-demo.test',
            'company_size' => '1-10',
            'industry' => 'Digital Agency',
            'tagline' => 'World-class digital product design & engineering',
            'about' => 'Boutique London agency engineering digital products for rapid-growth global startups.',
            'payment_verified' => true,
            'total_spent' => 38400.00,
            'hires_count' => 11,
            'active_contracts_count' => 1,
        ]);

        Wallet::create([
            'user_id' => $client2->id,
            'balance' => 4500.00,
            'escrow_balance' => 0.00,
            'currency' => 'USD',
        ]);

        $client3 = User::create([
            'name' => 'Julian Alvarez',
            'email' => 'julian.client@upwork.test',
            'password' => Hash::make('password'),
            'role' => 'client',
            'status' => 'active',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=400',
            'phone' => '+1 (555) 345-9876',
            'country' => 'Canada',
            'city' => 'Vancouver',
            'email_verified_at' => now(),
        ]);

        ClientProfile::create([
            'user_id' => $client3->id,
            'company_name' => 'PeakHealth AI Systems',
            'company_website' => 'https://peakhealth-ai.test',
            'company_size' => '51-200',
            'industry' => 'HealthTech',
            'tagline' => 'Intelligent clinical trials data analytics',
            'about' => 'Developing AI-driven workflows for biotechnology research labs and clinical trials.',
            'payment_verified' => true,
            'total_spent' => 82000.00,
            'hires_count' => 24,
            'active_contracts_count' => 3,
        ]);

        Wallet::create([
            'user_id' => $client3->id,
            'balance' => 14000.00,
            'escrow_balance' => 3500.00,
            'currency' => 'USD',
        ]);

        // 4. Create Freelancers
        $freelancersData = [
            [
                'name' => 'Alexander Reed',
                'email' => 'alex.dev@upwork.test',
                'title' => 'Senior Full-Stack Laravel 11 & Vue 3 Architect | 10+ Yrs Exp',
                'bio' => "I architect and build clean, scalable, high-performance web applications with complete automated test suites.\n\nSpecialized in:\n• Complex Laravel 11/Livewire 3 ecosystems with multi-tenancy\n• High-concurrency SQL optimization and indexing\n• Stripe Billing, Webhook idempotency, and PayPal Escrow integrations\n• Microservices and AWS/Docker infrastructure deployments\n\n100% job success rate with over 45 successful contracts delivered on time and within budget.",
                'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&q=80&w=400',
                'hourly_rate' => 75.00,
                'experience_level' => 'expert',
                'availability' => 'available_now',
                'country' => 'Canada',
                'city' => 'Toronto',
                'job_success_score' => 100,
                'total_earnings' => 98400.00,
                'completed_jobs_count' => 42,
                'total_hours_worked' => 1560.00,
                'is_top_rated' => true,
                'skills' => ['Laravel', 'PHP', 'Vue.js', 'Tailwind CSS', 'PostgreSQL', 'Docker', 'AWS', 'Redis', 'TypeScript'],
                'portfolio' => [
                    ['title' => 'HealthTech Cloud Portal', 'category' => 'Web App', 'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&q=80&w=600'],
                    ['title' => 'Real-time Crypto Portfolio Ledger', 'category' => 'FinTech', 'image' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&q=80&w=600'],
                    ['title' => 'SaaS Subscription & Billing Engine', 'category' => 'SaaS', 'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=600'],
                ]
            ],
            [
                'name' => 'Sophia Chen',
                'email' => 'sophia.ui@upwork.test',
                'title' => 'Principal UI/UX & Design Systems Designer | Figma Expert',
                'bio' => "Crafting world-class, human-centered digital experiences for enterprise SaaS and consumer apps.\n\n• Comprehensive Figma component design systems (variables, auto-layout, tokens)\n• Mobile (iOS/Android) & Web UX wireframing and prototyping\n• Interaction design with micro-animations\n• Usability testing and conversion rate optimization",
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=400',
                'hourly_rate' => 65.00,
                'experience_level' => 'expert',
                'availability' => 'available_now',
                'country' => 'United States',
                'city' => 'Seattle, WA',
                'job_success_score' => 99,
                'total_earnings' => 84200.00,
                'completed_jobs_count' => 33,
                'total_hours_worked' => 1240.00,
                'is_top_rated' => true,
                'skills' => ['Figma', 'UI/UX Design', 'Design Systems', 'Web Design', 'Brand Identity', 'Wireframing'],
                'portfolio' => [
                    ['title' => 'B2B Analytics Dashboard System', 'category' => 'UI/UX', 'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=600'],
                    ['title' => 'Neobank Mobile App iOS/Android', 'category' => 'Mobile UI', 'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&q=80&w=600'],
                ]
            ],
            [
                'name' => 'David Morales',
                'email' => 'david.ai@upwork.test',
                'title' => 'AI Engineer | LLM Applications, RAG Pipelines & Python Backend',
                'bio' => "Bridging state-of-the-art AI models with robust production systems. Specializing in OpenAI, Claude, LangChain, vector databases (Pinecone, pgvector), and high-performance FastAPI backends with streaming responses.",
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=400',
                'hourly_rate' => 85.00,
                'experience_level' => 'expert',
                'availability' => 'available_now',
                'country' => 'Germany',
                'city' => 'Berlin',
                'job_success_score' => 100,
                'total_earnings' => 72000.00,
                'completed_jobs_count' => 26,
                'total_hours_worked' => 960.00,
                'is_top_rated' => true,
                'skills' => ['Python', 'OpenAI API', 'LangChain', 'FastAPI', 'Machine Learning', 'Vector DB', 'LlamaIndex'],
                'portfolio' => [
                    ['title' => 'Enterprise Document RAG Assistant', 'category' => 'AI/ML', 'image' => 'https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&q=80&w=600'],
                ]
            ],
            [
                'name' => 'Lucas Rossi',
                'email' => 'lucas.flutter@upwork.test',
                'title' => 'Cross-Platform Mobile Developer | Flutter & React Native Specialist',
                'bio' => "6+ years creating buttery smooth 60fps native-feeling mobile apps for iOS and Android. Clean architecture, offline-first syncing, and push notification automation.",
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=400',
                'hourly_rate' => 55.00,
                'experience_level' => 'intermediate',
                'availability' => 'available_now',
                'country' => 'Spain',
                'city' => 'Barcelona',
                'job_success_score' => 96,
                'total_earnings' => 44200.00,
                'completed_jobs_count' => 21,
                'total_hours_worked' => 820.00,
                'is_top_rated' => false,
                'skills' => ['Flutter', 'React Native', 'Firebase', 'iOS Development', 'Android Development', 'App Store Deployment'],
                'portfolio' => [
                    ['title' => 'Fitness & Workout Companion App', 'category' => 'Mobile', 'image' => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&q=80&w=600'],
                ]
            ],
            [
                'name' => 'Amina Al-Mansoor',
                'email' => 'amina.devops@upwork.test',
                'title' => 'Cloud Infrastructure & DevOps Engineer | AWS, Kubernetes, Terraform',
                'bio' => "Senior DevOps consultant specializing in zero-downtime CI/CD automation, Kubernetes container orchestration, security hardening, and cost reduction across AWS and Google Cloud.",
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=400',
                'hourly_rate' => 80.00,
                'experience_level' => 'expert',
                'availability' => 'available_now',
                'country' => 'United Arab Emirates',
                'city' => 'Dubai',
                'job_success_score' => 100,
                'total_earnings' => 69000.00,
                'completed_jobs_count' => 24,
                'total_hours_worked' => 870.00,
                'is_top_rated' => true,
                'skills' => ['AWS', 'Docker', 'PostgreSQL', 'Redis', 'Python'],
                'portfolio' => [
                    ['title' => 'Kubernetes Auto-scaling Cluster', 'category' => 'DevOps', 'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&q=80&w=600'],
                ]
            ],
            [
                'name' => 'Oliver Wright',
                'email' => 'oliver.seo@upwork.test',
                'title' => 'Technical SEO Specialist & SaaS Growth Strategist',
                'bio' => "Helped over 30+ B2B software companies increase organic pipeline by 300%. Core focus: technical site audits, programmatic SEO directory architecture, Core Web Vitals optimization, and data-driven content strategy.",
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=400',
                'hourly_rate' => 50.00,
                'experience_level' => 'intermediate',
                'availability' => 'available_now',
                'country' => 'United Kingdom',
                'city' => 'Manchester',
                'job_success_score' => 98,
                'total_earnings' => 36500.00,
                'completed_jobs_count' => 19,
                'total_hours_worked' => 710.00,
                'is_top_rated' => false,
                'skills' => ['SEO', 'Google Analytics', 'Conversion Optimization', 'Content Strategy', 'Google Ads'],
                'portfolio' => [
                    ['title' => 'Programmatic SEO Directory Growth', 'category' => 'Marketing', 'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=600'],
                ]
            ],
        ];

        $freelancersMap = [];

        foreach ($freelancersData as $fData) {
            $user = User::create([
                'name' => $fData['name'],
                'email' => $fData['email'],
                'password' => Hash::make('password'),
                'role' => 'freelancer',
                'status' => 'active',
                'avatar' => $fData['avatar'],
                'phone' => '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
                'country' => $fData['country'],
                'city' => $fData['city'],
                'email_verified_at' => now(),
            ]);

            FreelancerProfile::create([
                'user_id' => $user->id,
                'title' => $fData['title'],
                'bio' => $fData['bio'],
                'hourly_rate' => $fData['hourly_rate'],
                'experience_level' => $fData['experience_level'],
                'availability' => $fData['availability'],
                'english_level' => 'fluent',
                'job_success_score' => $fData['job_success_score'],
                'total_earnings' => $fData['total_earnings'],
                'completed_jobs_count' => $fData['completed_jobs_count'],
                'total_hours_worked' => $fData['total_hours_worked'],
                'portfolio_items' => $fData['portfolio'],
                'is_top_rated' => $fData['is_top_rated'],
            ]);

            Wallet::create([
                'user_id' => $user->id,
                'balance' => rand(2500, 8400),
                'escrow_balance' => 0.00,
                'currency' => 'USD',
            ]);

            foreach ($fData['skills'] as $sName) {
                if (isset($skillsMap[$sName])) {
                    $user->skills()->attach($skillsMap[$sName], ['proficiency_level' => 'expert']);
                }
            }

            $freelancersMap[$fData['email']] = $user;
        }

        // 5. Create 8 Realistic Job Postings
        $catDev = Category::where('slug', 'development-it')->first();
        $catAI = Category::where('slug', 'ai-services')->first();
        $catDesign = Category::where('slug', 'design-creative')->first();
        $catMobile = Category::where('slug', 'mobile-development')->first();
        $catMarketing = Category::where('slug', 'sales-marketing')->first();

        $jobsList = [
            [
                'client' => $client1,
                'category' => $catDev,
                'title' => 'Full-Stack Laravel & Livewire Engineer for Multi-Tenant SaaS Platform',
                'description' => "We are looking for an experienced Senior Laravel Developer to join our core team for 3 months to build out our Multi-Tenant SaaS billing and analytics features.\n\nRequirements:\n- Strong knowledge of Laravel 11, Livewire 3, and Tailwind CSS\n- Experience designing clean database schemas with MySQL/PostgreSQL\n- Experience with Stripe Billing & Webhooks\n- Clean git workflow and test-driven mindset\n\nScope of Work:\n1. Multi-tenant workspace switcher & invitation engine\n2. Stripe tiered subscription checkout with proration\n3. Real-time event analytics dashboard with filter presets",
                'type' => 'fixed_price',
                'budget_min' => 3000.00,
                'budget_max' => 4500.00,
                'experience_level' => 'expert',
                'duration' => '1_to_3_months',
                'weekly_hours' => 'more_than_30',
                'status' => 'open',
                'is_featured' => true,
                'is_urgent' => true,
                'skills' => ['Laravel', 'PHP', 'Tailwind CSS', 'Vue.js', 'PostgreSQL'],
            ],
            [
                'client' => $client1,
                'category' => $catAI,
                'title' => 'Build Custom Retrieval-Augmented Generation (RAG) Bot for Financial Docs',
                'description' => "Looking for an AI engineer to develop a custom RAG solution using Python, LangChain/LlamaIndex, and OpenAI embeddings.\n\nThe system needs to:\n- Ingest PDF financial quarterly reports\n- Vectorize and store chunks in pgvector or Pinecone\n- Expose a clean FastAPI streaming endpoint\n- Include source attribution and verification links for each answer",
                'type' => 'fixed_price',
                'budget_min' => 2000.00,
                'budget_max' => 2800.00,
                'experience_level' => 'expert',
                'duration' => 'less_than_1_month',
                'weekly_hours' => 'more_than_30',
                'status' => 'open',
                'is_featured' => true,
                'is_urgent' => false,
                'skills' => ['Python', 'OpenAI API', 'LangChain', 'FastAPI', 'Machine Learning'],
            ],
            [
                'client' => $client2,
                'category' => $catDesign,
                'title' => 'Complete UI/UX Redesign & Figma Design System for FinTech Web App',
                'description' => "We need an elite UI/UX designer to overhaul our web application interface. We require a modern, minimalist design with comprehensive tokenized components (light/dark mode), responsive breakpoints, and interactive prototypes for developer handoff.",
                'type' => 'hourly',
                'hourly_rate_min' => 50.00,
                'hourly_rate_max' => 75.00,
                'experience_level' => 'expert',
                'duration' => '1_to_3_months',
                'weekly_hours' => 'more_than_30',
                'status' => 'open',
                'is_featured' => false,
                'is_urgent' => false,
                'skills' => ['Figma', 'UI/UX Design', 'Design Systems', 'Web Design'],
            ],
            [
                'client' => $client2,
                'category' => $catMobile,
                'title' => 'Flutter Mobile Application for Real-Time B2B Inventory Scanning',
                'description' => "Seeking a skilled Flutter developer to build our warehouse companion app with barcode/QR scanning, offline cache synchronization, and Bluetooth label printer integration.",
                'type' => 'fixed_price',
                'budget_min' => 3500.00,
                'budget_max' => 5000.00,
                'experience_level' => 'intermediate',
                'duration' => '3_to_6_months',
                'weekly_hours' => 'more_than_30',
                'status' => 'open',
                'is_featured' => false,
                'is_urgent' => false,
                'skills' => ['Flutter', 'Firebase', 'iOS Development', 'Android Development'],
            ],
            [
                'client' => $client3,
                'category' => $catDev,
                'title' => 'AWS Infrastructure Migration & Kubernetes Cluster Hardening',
                'description' => "We are migrating our core clinical trials pipeline from monolithic servers to an auto-scaling AWS EKS (Kubernetes) cluster with Terraform IaC and SOC2 compliance monitoring.",
                'type' => 'fixed_price',
                'budget_min' => 4000.00,
                'budget_max' => 6000.00,
                'experience_level' => 'expert',
                'duration' => '1_to_3_months',
                'weekly_hours' => 'more_than_30',
                'status' => 'open',
                'is_featured' => true,
                'is_urgent' => false,
                'skills' => ['AWS', 'Docker', 'PostgreSQL', 'Redis'],
            ],
            [
                'client' => $client3,
                'category' => $catMarketing,
                'title' => 'Technical Programmatic SEO Architecture for B2B SaaS Directory',
                'description' => "Looking for a proven technical SEO expert to architect and execute 5,000+ programmatic comparison pages with automated JSON-LD schema, breadcrumbs, and internal linking graph.",
                'type' => 'hourly',
                'hourly_rate_min' => 45.00,
                'hourly_rate_max' => 65.00,
                'experience_level' => 'intermediate',
                'duration' => '1_to_3_months',
                'weekly_hours' => 'less_than_30',
                'status' => 'open',
                'is_featured' => false,
                'is_urgent' => false,
                'skills' => ['SEO', 'Google Analytics', 'Conversion Optimization', 'Content Strategy'],
            ],
        ];

        $createdJobs = [];

        foreach ($jobsList as $j) {
            $job = JobPosting::create([
                'client_id' => $j['client']->id,
                'category_id' => $j['category']->id,
                'title' => $j['title'],
                'slug' => Str::slug($j['title']) . '-' . Str::random(5),
                'description' => $j['description'],
                'type' => $j['type'],
                'budget_min' => $j['budget_min'] ?? null,
                'budget_max' => $j['budget_max'] ?? null,
                'hourly_rate_min' => $j['hourly_rate_min'] ?? null,
                'hourly_rate_max' => $j['hourly_rate_max'] ?? null,
                'experience_level' => $j['experience_level'],
                'duration' => $j['duration'],
                'weekly_hours' => $j['weekly_hours'],
                'status' => $j['status'],
                'is_featured' => $j['is_featured'],
                'is_urgent' => $j['is_urgent'],
                'published_at' => now()->subDays(rand(1, 8)),
            ]);

            foreach ($j['skills'] as $sName) {
                if (isset($skillsMap[$sName])) {
                    $job->skills()->attach($skillsMap[$sName]);
                }
            }

            $createdJobs[] = $job;
        }

        // 6. Create Submitted Proposals for Job 1
        $mainJob = $createdJobs[0];
        $alexUser = $freelancersMap['alex.dev@upwork.test'];

        $proposalAlex = Proposal::create([
            'job_posting_id' => $mainJob->id,
            'freelancer_id' => $alexUser->id,
            'bid_amount' => 3500.00,
            'platform_fee' => 350.00,
            'receive_amount' => 3150.00,
            'delivery_time_days' => 21,
            'cover_letter' => "Hi Marcus,\n\nI have built over a dozen multi-tenant SaaS applications using Laravel 11, Livewire, and Stripe. I am very confident in architecting your workspace switcher and automated subscription engine with zero downtime.\n\nMy proposed roadmap:\n- Milestone 1 ($1,500): Database architecture, tenancy isolation & workspace management\n- Milestone 2 ($2,000): Stripe subscription checkout, webhook listener, and metrics analytics dashboard\n\nI can start immediately and deliver high-coverage unit and feature tests.",
            'milestones' => [
                ['title' => 'Tenancy & Workspace Invitation Engine', 'amount' => 1500.00, 'duration' => '10 days'],
                ['title' => 'Stripe Billing Integration & Analytics Dashboard', 'amount' => 2000.00, 'duration' => '11 days'],
            ],
            'status' => 'shortlisted',
            'client_seen' => true,
        ]);

        $mainJob->increment('proposals_count');

        // 7. Active Contract with Escrow Milestone
        $activeContract = Contract::create([
            'job_posting_id' => $mainJob->id,
            'proposal_id' => $proposalAlex->id,
            'client_id' => $client1->id,
            'freelancer_id' => $alexUser->id,
            'title' => 'SaaS Tenancy & Stripe Billing Architecture',
            'type' => 'fixed_price',
            'amount' => 3500.00,
            'platform_fee_percent' => 10.00,
            'status' => 'active',
            'terms' => 'Deliver clean Laravel 11 multi-tenant architecture with Stripe subscription and Livewire frontend.',
            'start_date' => now()->subDays(4),
        ]);

        $m1 = ContractMilestone::create([
            'contract_id' => $activeContract->id,
            'title' => 'Milestone 1: Multi-tenant database schema & workspace invitation engine',
            'description' => 'Implement complete database migrations, models, tenant scoping middleware, and email invite tokens.',
            'amount' => 1500.00,
            'due_date' => now()->addDays(6),
            'status' => 'submitted_for_approval',
            'submission_notes' => 'Completed all models, policies, and test suites. PR #42 is ready for client review on GitHub staging branch.',
            'funded_at' => now()->subDays(4),
            'submitted_at' => now()->subHours(3),
        ]);

        $m2 = ContractMilestone::create([
            'contract_id' => $activeContract->id,
            'title' => 'Milestone 2: Stripe Billing Webhooks & Real-time Metrics Dashboard',
            'description' => 'Build checkout session listener, customer portal redirect, and Livewire analytics dashboard.',
            'amount' => 2000.00,
            'due_date' => now()->addDays(17),
            'status' => 'pending',
        ]);

        Transaction::create([
            'wallet_id' => $clientWallet1->id,
            'user_id' => $client1->id,
            'type' => 'escrow_lock',
            'amount' => 1500.00,
            'fee' => 0.00,
            'reference_type' => 'ContractMilestone',
            'reference_id' => $m1->id,
            'description' => 'Escrow funded for Milestone 1: Multi-tenant schema',
            'status' => 'completed',
        ]);

        // 8. Completed Contract with Reviews
        $completedContract = Contract::create([
            'job_posting_id' => $mainJob->id,
            'client_id' => $client1->id,
            'freelancer_id' => $alexUser->id,
            'title' => 'API Gateway Optimization & Redis Caching Layer',
            'type' => 'fixed_price',
            'amount' => 2800.00,
            'platform_fee_percent' => 10.00,
            'status' => 'completed',
            'start_date' => now()->subDays(28),
            'end_date' => now()->subDays(7),
        ]);

        Review::create([
            'contract_id' => $completedContract->id,
            'reviewer_id' => $client1->id,
            'reviewee_id' => $alexUser->id,
            'role' => 'client_to_freelancer',
            'rating' => 5.0,
            'communication_rating' => 5.0,
            'quality_rating' => 5.0,
            'deadline_rating' => 5.0,
            'feedback' => 'Alexander is an absolute master of Laravel. Reduced our query latency by 75% and delivered clean, documented code ahead of schedule. Will hire again for sure!',
            'created_at' => now()->subDays(7),
        ]);

        Review::create([
            'contract_id' => $completedContract->id,
            'reviewer_id' => $alexUser->id,
            'reviewee_id' => $client1->id,
            'role' => 'freelancer_to_client',
            'rating' => 5.0,
            'communication_rating' => 5.0,
            'quality_rating' => 5.0,
            'deadline_rating' => 5.0,
            'feedback' => 'Marcus was fantastic to work with. Clear requirements, fast responses, and instant milestone releases. Highly recommended client!',
            'created_at' => now()->subDays(7),
        ]);

        // 9. Chat Thread between Client 1 and Alexander
        $conversation = Conversation::create([
            'job_posting_id' => $mainJob->id,
            'contract_id' => $activeContract->id,
            'subject' => 'SaaS Tenancy & Stripe Billing Architecture',
            'last_message_at' => now()->subHours(1),
        ]);

        $conversation->participants()->attach([$client1->id, $alexUser->id]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $client1->id,
            'body' => 'Hi Alex! We reviewed your proposal and we are excited to work with you. I have initialized the contract and funded Milestone 1.',
            'created_at' => now()->subDays(3),
            'is_read' => true,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $alexUser->id,
            'body' => 'Thank you Marcus! I have set up the tenant isolation middleware and completed the full test suite. I just submitted Milestone 1 for your review.',
            'created_at' => now()->subHours(3),
            'is_read' => true,
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $client1->id,
            'body' => 'Awesome, taking a look at the pull request right now!',
            'created_at' => now()->subHours(1),
            'is_read' => false,
        ]);
    }
}
