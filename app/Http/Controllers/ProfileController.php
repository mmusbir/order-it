<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

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
        $validated = $request->validated();

        $basePath = storage_path('app/public/profile-photos');

        // Handle photo removal first
        if ($request->input('remove_photo') === '1') {
            $currentPhoto = (string) $user->profile_photo;
            if ($currentPhoto !== '') {
                $fullPath = $basePath . DIRECTORY_SEPARATOR . $currentPhoto;
                if (file_exists($fullPath) && is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }
            $user->profile_photo = null;
        }
        // Handle profile photo upload
        elseif ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            if ($file && $file->isValid()) {
                // Delete old photo if exists
                $currentPhoto = (string) $user->profile_photo;
                if ($currentPhoto !== '') {
                    $oldFullPath = $basePath . DIRECTORY_SEPARATOR . $currentPhoto;
                    if (file_exists($oldFullPath) && is_file($oldFullPath)) {
                        @unlink($oldFullPath);
                    }
                }

                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = $user->id . '_' . time() . '.' . $extension;

                // Ensure directory exists
                if (!file_exists($basePath)) {
                    mkdir($basePath, 0755, true);
                }

                // Use move() method to bypass FilesystemAdapter issues as seen in RequestController
                $file->move($basePath, $filename);

                $user->profile_photo = $filename;
            }
        }

        // Clean validated data to prevent fill() wanted side effects
        unset($validated['profile_photo']);
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
}
