<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Skill;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Common validation
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        if ($user->role === 'employer') {
            $validated['company_name'] = $request->validate([
                'company_name' => ['required', 'string', 'max:255'],
            ])['company_name'];
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'] ?? null,
        ]);

        if ($user->role === 'employer') {
            $user->company_name = $validated['company_name'];
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile image.
     */
    public function updateProfileImage(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        // if ($request->hasFile('profile_image')) {
        //     // Delete old image if exists
        //     if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
        //         Storage::disk('public')->delete($user->profile_image);
        //     }

        //     // Store new image
        //     $path = $request->file('profile_image')->store('profile_images', 'public');
        //     $user->profile_image = $path;
        // }

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }

            // Store new image
            $filename = time() . '_' . $request->file('profile_image')->getClientOriginalName();
            $request->file('profile_image')->move(public_path('images/profile_images'), $filename);
            $user->profile_image = 'profile_images/' . $filename;
        }


        $user->save();

        return back()->with('status', 'profile-image-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Store a new skill for the student.
     */
public function storeSkill(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $user = Auth::user();

    if ($user->role !== 'student') {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }

    // Create or get existing skill
    $skill = Skill::firstOrCreate(['name' => $request->name]);

    // Prevent duplicate
    if (!$user->skills()->where('skill_id', $skill->id)->exists()) {
        $user->skills()->attach($skill->id);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Skill already exists.',
        ], 409);
    }

    return response()->json([
        'status' => 'success',
        'skill' => $skill,
    ]);
}



    /**
     * Remove a skill from the student's profile.
     */
    public function removeSkill($skillId)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user->skills()->detach($skillId);

        return response()->json(null, 204);
    }
}
