<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // Top metrics
        $totalJobs = Job::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalEmployers = User::where('role', 'employer')->count();
        $totalApplications = Application::count();

        return view('admin.users.index', compact(
            'users', 'totalJobs', 'totalStudents', 'totalEmployers', 'totalApplications'
        ));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Optional: Prevent deleting other admins
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete another admin.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
