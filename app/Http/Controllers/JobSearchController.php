<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Carbon\Carbon;

class JobSearchController extends Controller
{
    // Job Search Page (Public)
    public function index(Request $request)
    {
        $query = Job::query()
            ->where('status', 'approved')
            ->with(['employer', 'skills']); // ✅ Include skills

        // Filter by job title
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', $request->title . '%');
        }

        // Filter by location only if lat/lng/radius is not present
        if ($request->filled('location') && !$request->has(['lat', 'lng', 'radius'])) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
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
            'employer_id'
        )->get();

        // Transform jobs to include schedule, weekly pay, company name, and skills
        $jobs->transform(function ($job) {
            $job->company_name = optional($job->employer)->company_name;

            // Decode schedule JSON
            $scheduleData = is_array($job->schedule)
                ? $job->schedule
                : json_decode($job->schedule, true);
            $job->schedule = $scheduleData;

            // Calculate total weekly hours
            $totalHours = 0;
            if ($scheduleData) {
                foreach ($scheduleData as $times) {
                    if (isset($times['start'], $times['end'])) {
                        try {
                            $start = Carbon::createFromFormat('H:i', $times['start']);
                            $end = Carbon::createFromFormat('H:i', $times['end']);
                            $totalHours += $end->diffInMinutes($start) / 60;
                        } catch (\Exception $e) {
                            // Skip invalid time
                        }
                    }
                }
            }

            $job->weekly_pay = number_format($job->salary * $totalHours, 2);

            // Format skills for API response
            $job->skills = $job->skills->map(function ($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name
                ];
            });

            return $job;
        });

        // If it's an AJAX call or JSON requested
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'jobs' => $jobs->toArray()
            ]);
        }

        return view('job-search', compact('jobs'));
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

    // Redundant method removed: index() already handles JSON job list
}
