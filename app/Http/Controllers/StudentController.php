<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;
use App\Models\User;
use App\Models\Skill; 
use App\Notifications\JobApplicationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class StudentController extends Controller
{
    // Student Dashboard (Job Listings)
  public function index()
{
    $jobs = Job::with('employer') // load employer details
        ->where('status', 'approved')
        ->select(
            'id',
            'title',
            'location',
            'latitude',
            'longitude',
            'salary',
            'description',
            'schedule',
            'employer_id'
        )
        ->get();

    $jobs->transform(function ($job) {
        $job->company_name = optional($job->employer)->company_name;

        $scheduleData = is_array($job->schedule)
            ? $job->schedule
            : json_decode($job->schedule, true);
        $job->schedule = $scheduleData;

        $totalHours = 0;
        if ($scheduleData) {
            foreach ($scheduleData as $times) {
                if (isset($times['start'], $times['end'])) {
                    try {
                        $start = \Carbon\Carbon::createFromFormat('H:i', $times['start']);
                        $end = \Carbon\Carbon::createFromFormat('H:i', $times['end']);
                        $totalHours += $end->diffInMinutes($start) / 60;
                    } catch (\Exception $e) {
                        // skip invalid time
                    }
                }
            }
        }

        $job->weekly_pay = $job->salary ? number_format($job->salary * $totalHours, 2) : null;
        return $job;
    });

    return view('student.dashboard', compact('jobs'));
}


    // Student Apply for a Job (Application Form)
    public function apply($id)
    {
        // Fetch the job details
        $job = Job::findOrFail($id);

        // Show the application form
        return view('student.apply', compact('job'));
    }

    // Student Submit Application
    public function submitApplication(Request $request, $id)
    {
        // ✅ 1. Validate name and email only
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
    
        // ✅ 2. Update user profile
        $user = Auth::user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();
    
        // ✅ 3. Fetch the job
        $job = Job::findOrFail($id);
    
        // ✅ 4. Check if already applied
        $existingApplication = Application::where('student_id', $user->id)
            ->where('job_id', $job->id)
            ->first();
    
        if ($existingApplication) {
            return redirect()->route('student.dashboard')->with('error', 'You have already applied for this job.');
        }
    
        // ✅ 5. Save the application (no message)
        Application::create([
            'student_id' => $user->id,
            'job_id' => $job->id,
            'status' => 'Pending',
        ]);
    
        // ✅ 6. Notify employer
        $employer = User::find($job->employer_id);
        if ($employer) {
            Notification::send($employer, new JobApplicationNotification([
                'student_name' => $user->name,
                'job_title' => $job->title,
                'job_id' => $job->id,
            ]));
        }
    
        // ✅ 7. Redirect with success
        return redirect()->route('student.dashboard')->with('success', 'Application submitted successfully.');
    }
    

    // Student Applied Jobs
    public function appliedJobs()
    {
        // Fetch the jobs the student has applied to
     $applications = Application::with('job')
    ->where('student_id', Auth::id())
    ->whereHas('job', function ($query) {
        $query->where('status', '!=', 'rejected'); // ❌ hide jobs rejected by admin
    })
    ->orderBy('created_at', 'desc')
    ->get();

        // Pass applied jobs to the view
        return view('student.applied_jobs', compact('applications'));
    }

    // Student Notifications (In-App Notifications)
    public function notifications()
    {
        // Fetch all notifications for the student
        $notifications = Auth::user()->notifications()->latest()->get();

        return view('student.notifications', compact('notifications'));
    }

    // Mark Notification as Read (Optional)
    public function markNotificationAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    // Show Job Details (For Applied Jobs)
    public function showJob($id)
    {
        // Fetch the job details
        $job = Job::findOrFail($id);

        return view('student.show_job', compact('job'));
    }

    public function addSkill(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        $skillName = $request->input('name');

        // Find or create the skill
        $skill = Skill::firstOrCreate(['name' => $skillName]);

        // Attach skill if not already attached
        if (!$user->skills->contains($skill->id)) {
            $user->skills()->attach($skill);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Skill added successfully.',
            'skill' => $skill
        ]);
    }

    /**
     * Remove a skill from the student's profile (via AJAX)
     */
    public function removeSkill($id)
    {
        $user = Auth::user();
        $skill = Skill::findOrFail($id);

        $user->skills()->detach($skill);

        return response()->json([
            'status' => 'success',
            'message' => 'Skill removed successfully.'
        ]);
    }

    public function updateProfile(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ]);

    $user = Auth::user();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Profile updated successfully.',
    ]);
}

}
