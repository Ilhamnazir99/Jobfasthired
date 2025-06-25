<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobSearchController; // Updated: Renamed from HomeController
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\Admin\AdminJobController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| that contains the "web" middleware group.
|
*/

/* ===============================
| 🌐 Public Routes
=============================== */

// Redirect root URL to the Job Search page
Route::get('/', function () {
    return redirect()->route('job.search');
});

// Job Search Page (Previously Home)
Route::get('/job-search', [JobSearchController::class, 'index'])->name('job.search');
Route::get('/job-search/suggestions', [JobSearchController::class, 'suggestions'])->name('job.suggestions');

// Redirect old /home to /job-search
Route::get('/home', function () {
    return redirect()->route('job.search');
});

Route::get('/register/student', [RegisteredUserController::class, 'showStudentRegisterForm'])->name('register.student.form');
Route::post('/register/student', [RegisteredUserController::class, 'registerStudent'])->name('register.student');

Route::get('/register/employer', [RegisteredUserController::class, 'showEmployerRegisterForm'])->name('register.employer.form');
Route::post('/register/employer', [RegisteredUserController::class, 'registerEmployer'])->name('register.employer');

/* ===============================
| 🟩 Admin Login Routes (Public Access)
=============================== */
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

/* ===============================
| 🔐 Authenticated User Routes
=============================== */
Route::middleware('auth')->group(function () {

    // 📌 User Dashboard (Role-based redirection)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    /* ===============================
    | 👤 Profile Management (All Users)
    =============================== */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/image', [\App\Http\Controllers\ProfileController::class, 'updateProfileImage'])->name('profile.image.update');


    // ✅ New: Student Skill Management
    Route::delete('/profile/skills/{skillId}', [ProfileController::class, 'removeSkill'])->name('profile.skills.remove');
    Route::post('/profile/skills', [ProfileController::class, 'storeSkill'])->name('profile.skills.store');

    /* ===============================
    | 🟦 Employer Routes
    =============================== */
    // Employer Dashboard
    Route::get('/employer/dashboard', [EmployerController::class, 'dashboard'])->name('employer.dashboard');
    Route::get('/employer/jobs/{job}/applications', [EmployerController::class, 'viewApplications'])
    ->name('employer.jobs.applications');


    // Job Management (Employer)
    Route::get('/employer/job/create', [JobController::class, 'create'])->name('job.create');
    Route::post('/employer/job/store', [JobController::class, 'store'])->name('job.store');
    Route::delete('/employer/job/{id}', [JobController::class, 'destroy'])->name('job.destroy');


    // Optional: Employer job creation shortcut (Custom view)
    Route::get('/employer/job_create', function () {
        return view('employer.job_create');
    })->name('employer.job_create');

    // Employer Application Management
    Route::patch('/employer/application/{id}', [EmployerController::class, 'updateApplication'])->name('employer.updateApplication');

    // Employer Mark Notification as Read
  // ✅ Employer Notifications (Clean & Consistent)
Route::prefix('employer/notifications')->middleware('auth')->group(function () {
    Route::get('/', [EmployerController::class, 'notifications'])->name('employer.notifications');
    Route::post('/{id}/read', [EmployerController::class, 'markAsRead'])->name('employer.notifications.mark');
    Route::post('/mark-all-read', [EmployerController::class, 'markAllAsRead'])->name('employer.notifications.markAllAsRead');
    Route::delete('/clear', [EmployerController::class, 'clearAll'])->name('employer.notifications.clearAll');
});


    /* ===============================
   /* ===============================
| 🟩 Admin Routes (Protected After Login)
=============================== */
Route::middleware(['auth', 'admin'])->group(function () {

    // 🏠 Admin Dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // 👤 Admin View All Users
    Route::get('/admin/users', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.users.index');
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('admin.users.destroy');

  Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/jobs', [AdminJobController::class, 'index'])->name('jobs.index');
    Route::patch('/jobs/{id}/approve', [AdminJobController::class, 'approve'])->name('jobs.approve');
    Route::patch('/jobs/{id}/reject', [AdminJobController::class, 'rejectJob'])->name('jobs.reject');
    Route::delete('/jobs/{id}', [AdminJobController::class, 'destroy'])->name('jobs.destroy');
});




});


    /* ===============================
    | 🟩 Student Routes
    =============================== */
    // Student Dashboard
    Route::get('/student/dashboard', [StudentController::class, 'index'])->name('student.dashboard');

    // Student Job Listings
    Route::get('/student/jobs', [JobController::class, 'index'])->name('student.jobs');
    Route::get('/job-search/ajax', [JobSearchController::class, 'ajaxSearch']);

    

    // View Job Details (For Student)
    Route::get('/student/jobs/{id}/show', [StudentController::class, 'showJob'])->name('student.jobs.show');

    // Student Apply for Job
    Route::get('/student/jobs/{id}/apply', [StudentController::class, 'apply'])->name('student.jobs.apply');
    Route::post('/student/jobs/{id}/apply', [StudentController::class, 'submitApplication'])->name('student.jobs.submit');

    // Skill Management (AJAX for adding/removing skills)
    Route::post('/profile/skills', [StudentController::class, 'addSkill'])->name('profile.skills.store');
    Route::delete('/profile/skills/{id}', [StudentController::class, 'removeSkill'])->name('profile.skills.destroy');
    Route::post('/student/profile/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');

    // Student Applied Jobs
    Route::get('/student/applied-jobs', [StudentController::class, 'appliedJobs'])->name('student.applied.jobs');

    // Student Notifications
    Route::get('/student/notifications', [StudentController::class, 'notifications'])->name('student.notifications');
    Route::post('/student/notifications/{id}/read', [StudentController::class, 'markNotificationAsRead'])->name('student.notifications.markAsRead');
    Route::post('/student/notifications/mark-all', [StudentController::class, 'markAllAsRead'])
    ->name('student.notifications.markAllAsRead');
    Route::delete('/student/notifications/clear-all', [StudentController::class, 'clearAllNotifications'])
    ->name('student.notifications.clearAll');



    
});

/* ===============================
| 🔑 Authentication Routes
=============================== */
require __DIR__.'/auth.php';
