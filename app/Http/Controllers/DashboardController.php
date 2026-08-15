<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contract;
use App\Models\JobPosting;
use App\Models\Proposal;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isClient()) {
            $postedJobs = JobPosting::where('client_id', $user->id)->with(['proposals', 'category'])->latest()->get();
            $activeContracts = Contract::where('client_id', $user->id)->where('status', 'active')->with(['freelancer.freelancerProfile', 'milestones'])->latest()->get();
            $recentProposals = Proposal::whereHas('jobPosting', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            })->with(['freelancer.freelancerProfile', 'jobPosting'])->latest()->take(5)->get();

            $metrics = [
                'active_jobs' => $postedJobs->where('status', 'open')->count(),
                'proposals_count' => $recentProposals->count(),
                'active_contracts' => $activeContracts->count(),
                'total_spent' => $user->clientProfile?->total_spent ?? 0,
            ];

            return view('dashboard.client', compact('user', 'postedJobs', 'activeContracts', 'recentProposals', 'metrics'));
        }

        // Freelancer Dashboard
        $submittedProposals = Proposal::where('freelancer_id', $user->id)->with(['jobPosting.client'])->latest()->get();
        $activeContracts = Contract::where('freelancer_id', $user->id)->where('status', 'active')->with(['client.clientProfile', 'milestones'])->latest()->get();
        $savedJobs = $user->savedJobs()->with(['category', 'client'])->latest()->take(5)->get();

        $recommendedJobs = JobPosting::where('status', 'open')->latest('published_at')->take(6)->get();

        $metrics = [
            'total_earnings' => $user->freelancerProfile?->total_earnings ?? 0,
            'active_contracts' => $activeContracts->count(),
            'proposals_submitted' => $submittedProposals->count(),
            'job_success_score' => $user->freelancerProfile?->job_success_score ?? 100,
            'completeness' => $user->freelancerProfile?->completeness_percentage ?? 100,
        ];

        return view('dashboard.freelancer', compact('user', 'submittedProposals', 'activeContracts', 'savedJobs', 'recommendedJobs', 'metrics'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        $user->load(['freelancerProfile', 'clientProfile', 'skills']);
        $allSkills = Skill::orderBy('name')->get();
        $allCategories = Category::orderBy('name')->get();

        return view('dashboard.profile-edit', compact('user', 'allSkills', 'allCategories'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'avatar' => 'nullable|string',
            'avatar_file' => 'nullable|image|max:15360', // Up to 15MB before compression
            'avatar_base64' => 'nullable|string',
            // Freelancer fields
            'title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:5',
            'experience_level' => 'nullable|in:entry,intermediate,expert',
            'english_level' => 'nullable|string',
            'availability' => 'nullable|string',
            'github_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
            // Multi-item JSON sections
            'portfolio_titles' => 'nullable|array',
            'portfolio_categories' => 'nullable|array',
            'portfolio_images' => 'nullable|array',
            'portfolio_descriptions' => 'nullable|array',
            'portfolio_links' => 'nullable|array',
            'employment_companies' => 'nullable|array',
            'employment_titles' => 'nullable|array',
            'employment_periods' => 'nullable|array',
            'employment_descriptions' => 'nullable|array',
            'education_schools' => 'nullable|array',
            'education_degrees' => 'nullable|array',
            'education_years' => 'nullable|array',
            'certification_names' => 'nullable|array',
            'certification_issuers' => 'nullable|array',
            'certification_years' => 'nullable|array',
            'language_names' => 'nullable|array',
            'language_levels' => 'nullable|array',
            // Client fields
            'company_name' => 'nullable|string|max:255',
            'company_website' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'about' => 'nullable|string',
        ]);

        // Process Compressed Avatar Upload
        $avatarPath = $user->avatar;
        if (!empty($validated['avatar_base64']) && str_starts_with($validated['avatar_base64'], 'data:image')) {
            $avatarPath = $this->saveCompressedAvatarFromBase64($validated['avatar_base64'], $user);
        } elseif ($request->hasFile('avatar_file') && $request->file('avatar_file')->isValid()) {
            $avatarPath = $this->saveCompressedAvatarFromFile($request->file('avatar_file'), $user);
        } elseif (!empty($validated['avatar'])) {
            $avatarPath = $validated['avatar'];
        }

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'country' => $validated['country'] ?? $user->country,
            'city' => $validated['city'] ?? $user->city,
            'avatar' => $avatarPath,
        ]);

        if ($user->isFreelancer()) {
            if (!$user->freelancerProfile) {
                $user->freelancerProfile()->create([]);
                $user->load('freelancerProfile');
            }

            // Sync skills
            if (isset($validated['skills'])) {
                $user->skills()->sync($validated['skills']);
            }

            // Assemble Portfolio Items
            $portfolio = [];
            if (!empty($validated['portfolio_titles'])) {
                foreach ($validated['portfolio_titles'] as $idx => $pTitle) {
                    if (!empty($pTitle)) {
                        $portfolio[] = [
                            'title' => $pTitle,
                            'category' => $validated['portfolio_categories'][$idx] ?? 'Project',
                            'image' => $validated['portfolio_images'][$idx] ?? 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=600',
                            'description' => $validated['portfolio_descriptions'][$idx] ?? '',
                            'link' => $validated['portfolio_links'][$idx] ?? '',
                        ];
                    }
                }
            }

            // Assemble Employment History
            $employment = [];
            if (!empty($validated['employment_companies'])) {
                foreach ($validated['employment_companies'] as $idx => $empComp) {
                    if (!empty($empComp)) {
                        $employment[] = [
                            'company' => $empComp,
                            'title' => $validated['employment_titles'][$idx] ?? 'Role',
                            'period' => $validated['employment_periods'][$idx] ?? '2022 - Present',
                            'description' => $validated['employment_descriptions'][$idx] ?? '',
                        ];
                    }
                }
            }

            // Assemble Education
            $education = [];
            if (!empty($validated['education_schools'])) {
                foreach ($validated['education_schools'] as $idx => $school) {
                    if (!empty($school)) {
                        $education[] = [
                            'school' => $school,
                            'degree' => $validated['education_degrees'][$idx] ?? 'Bachelor Degree',
                            'year' => $validated['education_years'][$idx] ?? '2020',
                        ];
                    }
                }
            }

            // Assemble Certifications
            $certifications = [];
            if (!empty($validated['certification_names'])) {
                foreach ($validated['certification_names'] as $idx => $certName) {
                    if (!empty($certName)) {
                        $certifications[] = [
                            'name' => $certName,
                            'issuer' => $validated['certification_issuers'][$idx] ?? 'Issuer',
                            'year' => $validated['certification_years'][$idx] ?? date('Y'),
                        ];
                    }
                }
            }

            // Assemble Languages
            $languages = [];
            if (!empty($validated['language_names'])) {
                foreach ($validated['language_names'] as $idx => $langName) {
                    if (!empty($langName)) {
                        $languages[] = [
                            'name' => $langName,
                            'level' => $validated['language_levels'][$idx] ?? 'Fluent',
                        ];
                    }
                }
            }

            $user->freelancerProfile->update([
                'title' => $validated['title'] ?? $user->freelancerProfile->title,
                'bio' => $validated['bio'] ?? $user->freelancerProfile->bio,
                'hourly_rate' => $validated['hourly_rate'] ?? $user->freelancerProfile->hourly_rate,
                'experience_level' => $validated['experience_level'] ?? $user->freelancerProfile->experience_level,
                'english_level' => $validated['english_level'] ?? $user->freelancerProfile->english_level,
                'availability' => $validated['availability'] ?? $user->freelancerProfile->availability,
                'github_url' => $validated['github_url'] ?? $user->freelancerProfile->github_url,
                'linkedin_url' => $validated['linkedin_url'] ?? $user->freelancerProfile->linkedin_url,
                'portfolio_url' => $validated['portfolio_url'] ?? $user->freelancerProfile->portfolio_url,
                'portfolio_items' => !empty($portfolio) ? $portfolio : $user->freelancerProfile->portfolio_items,
                'employment_history' => !empty($employment) ? $employment : $user->freelancerProfile->employment_history,
                'education' => !empty($education) ? $education : $user->freelancerProfile->education,
                'certifications' => !empty($certifications) ? $certifications : $user->freelancerProfile->certifications,
                'languages' => !empty($languages) ? $languages : $user->freelancerProfile->languages,
            ]);
        } elseif ($user->isClient()) {
            if (!$user->clientProfile) {
                $user->clientProfile()->create([]);
                $user->load('clientProfile');
            }

            $user->clientProfile->update([
                'company_name' => $validated['company_name'] ?? $user->clientProfile->company_name,
                'company_website' => $validated['company_website'] ?? $user->clientProfile->company_website,
                'industry' => $validated['industry'] ?? $user->clientProfile->industry,
                'tagline' => $validated['tagline'] ?? $user->clientProfile->tagline,
                'about' => $validated['about'] ?? $user->clientProfile->about,
            ]);
        }

        return back()->with('success', 'Profile and avatar updated successfully!');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|file|image|max:15360', // up to 15MB
            'type' => 'nullable|string|in:avatar,portfolio,general',
        ]);

        $user = Auth::user();
        $type = $request->input('type', 'avatar');
        $file = $request->file('image');

        $folder = $type === 'portfolio' ? 'portfolio' : 'avatars';
        $dir = storage_path('app/public/' . $folder);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $folder . '_' . $user->id . '_' . time() . '_' . uniqid() . '.webp';
        $fullPath = $dir . '/' . $filename;
        $binary = file_get_contents($file->getRealPath());

        if (extension_loaded('gd')) {
            $src = @imagecreatefromstring($binary);
            if ($src) {
                $origWidth = imagesx($src);
                $origHeight = imagesy($src);
                $maxDim = $type === 'portfolio' ? 1000 : 500;

                $newWidth = $origWidth;
                $newHeight = $origHeight;

                if ($origWidth > $maxDim || $origHeight > $maxDim) {
                    if ($origWidth >= $origHeight) {
                        $newWidth = $maxDim;
                        $newHeight = (int) round(($origHeight * $maxDim) / $origWidth);
                    } else {
                        $newHeight = $maxDim;
                        $newWidth = (int) round(($origWidth * $maxDim) / $origHeight);
                    }
                }

                $dst = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                if (function_exists('imagewebp')) {
                    imagewebp($dst, $fullPath, 85);
                } else {
                    $filename = $folder . '_' . $user->id . '_' . time() . '_' . uniqid() . '.jpg';
                    $fullPath = $dir . '/' . $filename;
                    imagejpeg($dst, $fullPath, 85);
                }

                $relativePath = $folder . '/' . $filename;

                if ($type === 'avatar') {
                    if ($user->avatar && str_starts_with($user->avatar, 'avatars/') && file_exists(storage_path('app/public/' . $user->avatar))) {
                        @unlink(storage_path('app/public/' . $user->avatar));
                    }
                    $user->update(['avatar' => $relativePath]);
                }

                return response()->json([
                    'success' => true,
                    'path' => $relativePath,
                    'url' => asset('storage/' . $relativePath),
                    'filename' => $filename,
                    'size' => filesize($fullPath),
                ]);
            }
        }

        // Direct fallback
        file_put_contents($fullPath, $binary);
        $relativePath = $folder . '/' . $filename;
        if ($type === 'avatar') {
            $user->update(['avatar' => $relativePath]);
        }

        return response()->json([
            'success' => true,
            'path' => $relativePath,
            'url' => asset('storage/' . $relativePath),
            'filename' => $filename,
            'size' => filesize($fullPath),
        ]);
    }

    private function saveCompressedAvatarFromBase64(string $base64Data, User $user): string
    {
        $dir = storage_path('app/public/avatars');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Extract binary data from data URL
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $decoded = base64_decode($data);
            if ($decoded !== false) {
                return $this->compressAndStoreBinary($decoded, $user);
            }
        }

        return $user->avatar ?? '';
    }

    private function saveCompressedAvatarFromFile($file, User $user): string
    {
        $binary = file_get_contents($file->getRealPath());
        return $this->compressAndStoreBinary($binary, $user);
    }

    private function compressAndStoreBinary(string $binary, User $user): string
    {
        $dir = storage_path('app/public/avatars');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'avatar_' . $user->id . '_' . time() . '.webp';
        $fullPath = $dir . '/' . $filename;

        // Try GD compression if GD is loaded
        if (extension_loaded('gd')) {
            $src = @imagecreatefromstring($binary);
            if ($src) {
                $origWidth = imagesx($src);
                $origHeight = imagesy($src);

                $maxDim = 500;
                $newWidth = $origWidth;
                $newHeight = $origHeight;

                if ($origWidth > $maxDim || $origHeight > $maxDim) {
                    if ($origWidth >= $origHeight) {
                        $newWidth = $maxDim;
                        $newHeight = (int) round(($origHeight * $maxDim) / $origWidth);
                    } else {
                        $newHeight = $maxDim;
                        $newWidth = (int) round(($origWidth * $maxDim) / $origHeight);
                    }
                }

                $dst = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                // Save as WebP with quality 85
                if (function_exists('imagewebp')) {
                    imagewebp($dst, $fullPath, 85);
                } else {
                    $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
                    $fullPath = $dir . '/' . $filename;
                    imagejpeg($dst, $fullPath, 85);
                }

                // Delete old local avatar if exists
                if ($user->avatar && str_starts_with($user->avatar, 'avatars/') && file_exists(storage_path('app/public/' . $user->avatar))) {
                    @unlink(storage_path('app/public/' . $user->avatar));
                }

                return 'avatars/' . $filename;
            }
        }

        // Fallback: Save binary directly if GD not available or fails
        file_put_contents($fullPath, $binary);
        return 'avatars/' . $filename;
    }
}
