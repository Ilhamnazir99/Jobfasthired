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
use Illuminate\Support\Str;

class StudentController extends Controller
{
    // Student Dashboard (Job Listings)
public function index()
{
    $jobs = Job::query()
        ->where('status', 'approved')
        ->with(['employer', 'skills']) // ✅ include employer and skills
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
        // Add employer name
        $job->company_name = optional($job->employer)->company_name;

       // Safely decode schedule
$scheduleData = is_array($job->schedule)
    ? $job->schedule
    : json_decode($job->schedule, true) ?? [];

$job->schedule = json_decode(json_encode($scheduleData), true);


        // Calculate total weekly hours
        $totalHours = 0;
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

        // Compute weekly pay
        $job->weekly_pay = $job->salary ? number_format($job->salary * $totalHours, 2) : null;

        // Format skills as array of { id, name }
        $job->skills = $job->skills->map(function ($skill) {
            return [
                'id' => $skill->id,
                'name' => $skill->name
            ];
        });

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
        return redirect()->route('student.applied.jobs')->with('success', 'Application submitted successfully.');
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
    \Log::info('Request payload:', $request->all()); // 🔍 log input

    // ✅ Normalize phone number ONLY if filled
    if ($request->filled('phone_number')) {
        $rawPhone = preg_replace('/\D/', '', $request->phone_number); // remove non-digits

        if (Str::startsWith($rawPhone, '0')) {
            $normalized = '+60' . substr($rawPhone, 1);
        } elseif (Str::startsWith($rawPhone, '60')) {
            $normalized = '+' . $rawPhone;
        } elseif (Str::startsWith($rawPhone, '1')) {
            $normalized = '+60' . $rawPhone;
        } else {
            $normalized = $request->phone_number; // fallback (just keep it)
        }

        // Replace input with normalized version
        $request->merge(['phone_number' => $normalized]);
    }

    // ✅ Your original validation
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone_number' => [
            'nullable',
            'regex:/^(\+?60|01)[0-9\s\-]{8,15}$/',
            'max:20',
            'unique:users,phone_number,' . Auth::id(),
        ],
    ]);

    $user = Auth::user();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone_number = $request->phone_number;
    $user->save();

    \Log::info('Updated user profile:', $user->toArray()); // ✅ log result

    return response()->json([
        'status' => 'success',
        'message' => 'Profile updated successfully.',
    ]);
}



public function markAllAsRead()
{
    auth()->user()->unreadNotifications->markAsRead();
    return back()->with('success', 'All notifications marked as read.');
}

public function notifications(Request $request)
{
    $filter = $request->query('filter', 'all'); // default = all

    $query = Auth::user()->notifications()->latest();

    if ($filter === 'unread') {
        $query->whereNull('read_at');
    }

    $notifications = $query->get();

    return view('student.notifications', compact('notifications', 'filter'));
}
public function clearAllNotifications()
{
    Auth::user()->notifications()->delete();

    return redirect()->back()->with('success', 'All notifications cleared.');
}


}
