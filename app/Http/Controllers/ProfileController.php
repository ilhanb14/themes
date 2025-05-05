<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule; // Needed for unique email check

class ProfileController extends Controller
{
    public function show()
    { 
        // Eager load the 'images' relationship when fetching the user.
        // Also, order the loaded images by 'created_at' descending (newest first).
        $userId = Auth::id();
        $user = \App\Models\User::with(['images' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($userId);
        return view('profile.show', compact('user'));
    }

    public function edit() { /* Fetches user, returns edit view */
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], // Unique email check!
            'avatar' => ['nullable', File::image()->max(1024)],
        ]);

        $userData = $request->only('name', 'email'); // Get name and email

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Store new one
            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($userData); // Update the user record

        return redirect()->route('profile.show')->with('success', 'Profile updated!');
    }

    /**
     * Handle uploading multiple travel images for the user.
     */
    public function uploadImages(Request $request)
    {
        $user = Auth::user(); // Get the currently authenticated user

        // 1. Validation - using the simpler validate method
        $validated = $request->validate([
            // 'images' field must exist and be an array (from name="images[]")
            'images' => 'required|array',
            // Each image must be valid and under 2MB
            'images.*' => 'required|image|max:2048',
        ]);

        // 2. File Storage & Database Record Creation
        $uploadedPaths = []; // Keep track of successfully processed images
        if ($request->hasFile('images')) { // Check if files were actually uploaded
            foreach ($request->file('images') as $file) { // Loop through each uploaded file
                if ($file->isValid()) { // Check if the file uploaded without errors
                    try {
                        // Store the file in 'storage/app/public/travel_images/user_{id}'
                        // The 'store' method generates a unique filename automatically.
                        $path = $file->store('travel_images/user_' . $user->id, 'public'); 
                        
                        // Create a corresponding record in the 'images' table
                        Image::create([
                            'user_id' => $user->id,
                            'path'    => $path, // Store the generated path
                        ]);
                        $uploadedPaths[] = $path; 
                    } catch (\Exception $e) {
                        // Log any error during storage or DB insertion (optional but recommended)
                        Log::error("Image upload failed for user {$user->id}: " . $e->getMessage());
                        // You could add specific error feedback here if needed
                    }
                }
            }
        }

        // 3. Redirect with Feedback
        if (empty($uploadedPaths)) {
            // If validation passed but no files were actually valid/uploaded
            return back()->withErrors(['images' => 'No valid images were uploaded or an error occurred.'], 'imageUpload');
        }

        // Redirect back to the edit page with a success message
        return redirect()->route('profile.edit')
               ->with('image_upload_success', count($uploadedPaths) . ' image(s) uploaded successfully!');
    }
}
