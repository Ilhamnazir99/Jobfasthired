<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\JobCategory; // ✅ Added for dropdown
use Carbon\Carbon;

class JobSearchController extends Controller
{
    // Job Search Page (Public)
    public function index(Request $request)
    {
        $query = Job::query()
            ->where('status', 'approved')
            ->with(['employer', 'skills', 'category']); // ✅ include category relationship

        // Filter by job title
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', $request->title . '%');
        }

        // Filter by location only if lat/lng/radius is not present
        if ($request->filled('location') && !$request->has(['lat', 'lng', 'radius'])) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }

        // ✅ Filter by selected job categories
        if ($request->filled('categories')) {
            $query->whereIn('job_category_id', $request->categories);
        }

        // Select only required columns for performance
        $jobs = $query->select(
            'id',
            'title',
            'location',
            'latitude',
            'longitude',
            'salary',
            'description',
            'schedule',
            'employer_id',
            'job_category_id' // ✅ correct column name
        )->get();

        // 👉 If AJAX: transform to array
        if ($request->ajax() || $request->wantsJson()) {
            $transformedJobs = $jobs->map(function ($job) {
                $companyName = optional($job->employer)->company_name;
                $categoryName = optional($job->category)->name;

                // Decode schedule
                $scheduleData = is_array($job->schedule)
                    ? $job->schedule
                    : json_decode($job->schedule, true);

                // Calculate weekly hours
                $totalHours = 0;
                if ($scheduleData) {
                    foreach ($scheduleData as $times) {
                        if (isset($times['start'], $times['end'])) {
                            try {
                                $start = Carbon::createFromFormat('H:i', $times['start']);
                                $end = Carbon::createFromFormat('H:i', $times['end']);
                                $totalHours += $end->diffInMinutes($start) / 60;
                            } catch (\Exception $e) {}
                        }
                    }
                }

                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'location' => $job->location,
                    'latitude' => $job->latitude,
                    'longitude' => $job->longitude,
                    'salary' => $job->salary,
                    'description' => $job->description,
                    'schedule' => $scheduleData,
                    'weekly_pay' => number_format($job->salary * $totalHours, 2),
                    'company_name' => $companyName,
                    'category' => $categoryName,
                    'skills' => $job->skills->map(fn($skill) => [
                        'id' => $skill->id,
                        'name' => $skill->name,
                    ])->values(),
                ];
            });

            return response()->json([
                'jobs' => $transformedJobs
            ]);
        }

        // ✅ For Blade: no transformation (keep full model)
        $jobs->transform(function ($job) {
            $job->company_name = optional($job->employer)->company_name;
            $job->category = optional($job->category)->name;

            $scheduleData = is_array($job->schedule)
                ? $job->schedule
                : json_decode($job->schedule, true);
            $job->schedule = $scheduleData;

            // Weekly hours
            $totalHours = 0;
            if ($scheduleData) {
                foreach ($scheduleData as $times) {
                    if (isset($times['start'], $times['end'])) {
                        try {
                            $start = Carbon::createFromFormat('H:i', $times['start']);
                            $end = Carbon::createFromFormat('H:i', $times['end']);
                            $totalHours += $end->diffInMinutes($start) / 60;
                        } catch (\Exception $e) {}
                    }
                }
            }

            $job->weekly_pay = number_format($job->salary * $totalHours, 2);

            // Skills as array for JS
            $job->skills = $job->skills->map(fn($skill) => [
                'id' => $skill->id,
                'name' => $skill->name
            ]);

            return $job;
        });

        // ✅ Send all categories to Blade for filters
        $categories = JobCategory::all();

        return view('job-search', compact('jobs', 'categories'));
    }

    // AJAX title suggestions for autocomplete
    public function suggestions(Request $request)
    {
        $query = $request->get('query');

        $suggestions = Job::where('status', 'approved')
            ->where('title', 'LIKE', '%' . $query . '%')
            ->select('title')
            ->distinct()
            ->limit(5)
            ->get();

        return response()->json($suggestions);
    }
}
