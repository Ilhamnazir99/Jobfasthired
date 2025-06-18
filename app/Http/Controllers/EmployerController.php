<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class EmployerController extends Controller
{
    // Employer Dashboard (with tabbed job status view)
    public function dashboard()
    {
        $employerId = Auth::id();

        // Group jobs by status for tabs
        $jobs = [
            'all' => Job::where('employer_id', $employerId)
                        ->with('applications.student')
                        ->latest()->get(),

            'active' => Job::where('employer_id', $employerId)
                        ->where('status', 'approved')
                        ->with('applications.student')->get(),

            'pending' => Job::where('employer_id', $employerId)
                        ->where('status', 'pending')
                        ->with('applications.student')->get(),

            'rejected' => Job::where('employer_id', $employerId)
                        ->where('status', 'rejected')
                        ->with('applications.student')->get(),
        ];

        // New: count stats for cards
        $activeJobs = $jobs['active']->count();
        $pendingJobs = $jobs['pending']->count();
        $totalApplications = Application::whereIn(
            'job_id',
            Job::where('employer_id', $employerId)->pluck('id')
        )->count();

        return view('employer.dashboard', compact(
            'jobs',
            'activeJobs',
            'pendingJobs',
            'totalApplications'
        ));
    }

    // Update Application Status (Accept/Reject)
    public function updateApplication(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:Accepted,Rejected'
    ]);

    $application = Application::findOrFail($id);
    $application->status = $request->status;
    $application->save();

    // Notify the student
    $student = $application->student;
    $job = $application->job; // get job info

    Notification::send($student, new ApplicationStatusNotification([
        'job_id'    => $job->id, // ✅ include job_id
        'job_title' => $job->title,
        'status'    => $application->status
    ]));

    return redirect()->route('employer.dashboard')->with([
        'success' => 'Application status updated and student notified.',
        'type' => 'success'
    ]);
}


    // Mark notification as read
    public function markNotification(Request $request, $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with([
            'success' => 'Notification marked as read.',
            'type' => 'success'
        ]);
    }

    public function viewApplications(Job $job)
    {
        // Ensure the logged-in employer owns this job
        if ($job->employer_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $job->load('applications.student'); // eager load student details
        return view('employer.applications', compact('job'));
    }
    
}
