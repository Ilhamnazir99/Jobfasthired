<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Skill;

class JobController extends Controller
{
    // Show the job creation form
    public function create()
    {
        $googleApiKey = config('app.google_maps_api_key');
        return view('employer.job_create', compact('googleApiKey'));
    }

    // Handle the job posting form submission
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'address' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'salary' => 'required|numeric',
        ]);

        // ✅ Validate schedule days: must include both start and end if active
        $rawSchedule = $request->input('schedule', []);
        $validatedSchedule = [];

        foreach ($rawSchedule as $day => $data) {
            if (!empty($data['active'])) {
                if (empty($data['start']) || empty($data['end'])) {
                    return back()->withInput()->withErrors([
                        "schedule.$day" => "Please provide both start and end time for " . ucfirst($day),
                    ]);
                }

                // Check if end time is after start time
        $startTimestamp = strtotime($data['start']);
        $endTimestamp = strtotime($data['end']);

        if ($endTimestamp <= $startTimestamp) {
            return back()->withInput()->withErrors([
                "schedule.$day" => ucfirst($day) . " end time must be after start time.",
            ]);
        }

        $validatedSchedule[$day] = [
            'start' => $data['start'],
            'end' => $data['end'],
        ];
            }
        }

        // ✅ Step 1: Create the job
        $job = Job::create([
            'employer_id' => auth()->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'salary' => $request->salary,
            'schedule' => json_encode($validatedSchedule),
            'category' => $request->category,
            'status' => 'pending',
        ]);

        // ✅ Step 2: Attach skills (unchanged)
        if ($request->has('skills') && is_array($request->skills)) {
            $skillIds = [];

            foreach ($request->skills as $skillName) {
                $skillName = trim(strtolower($skillName));
                if (empty($skillName)) continue;

                $skill = Skill::firstOrCreate(['name' => $skillName]);
                $skillIds[] = $skill->id;
            }

            $job->skills()->sync($skillIds);
        }

        return redirect()->route('employer.dashboard')->with([
            'success' => 'Job posted successfully and is under review.',
            'type' => 'success'
        ]);
    }

    // Student view job list
    public function index()
    {
        $jobs = Job::latest()->get(); // latest() will order by created_at DESC
        return view('student.job_listings', compact('jobs'));
    }

    // Delete job
    public function destroy($id)
    {
        $job = Job::findOrFail($id);

        // Optional: Check if current employer owns the job
        if ($job->employer_id !== auth()->id()) {
            abort(403);
        }

        $job->delete();

        return redirect()->route('employer.dashboard')->with([
            'success' => 'Job deleted successfully.',
            'type' => 'delete'
        ]);
    }
}
