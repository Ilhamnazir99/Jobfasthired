<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;

class AdminJobController extends Controller
{
    // Display all jobs
    public function index()
    {
        $jobs = Job::with('employer')->latest()->get();
        return view('admin.jobs.index', compact('jobs'));
    }

    // Approve a job
    public function approve($id)
    {
        $job = Job::findOrFail($id);
        $job->status = 'approved';
        $job->save();

        return redirect()->route('admin.jobs.index')->with('success', 'Job approved successfully.');
    }

        // Reject a job posting
    public function rejectJob($id)
    {
        $job = Job::findOrFail($id);
        $job->status = 'rejected';
        $job->save();

        return redirect()->route('admin.jobs.index')->with('success', 'Job has been rejected.');
    }


    // Delete a job
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }
}
