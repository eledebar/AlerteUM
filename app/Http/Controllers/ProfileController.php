<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->name = $data['name'];

        if ($user->email !== $data['email']) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
        }

        if ($request->file('photo')) {
            $disk = env('AVATAR_DISK', 'local');
            $dir = 'avatars/'.$user->id;
            $ext = strtolower($request->file('photo')->getClientOriginalExtension() ?: 'jpg');
            $filename = 'avatar.'.$ext;

            Storage::disk($disk)->deleteDirectory($dir);
            Storage::disk($disk)->makeDirectory($dir);
            Storage::disk($disk)->putFileAs($dir, $request->file('photo'), $filename);

            $user->profile_photo_path = $dir.'/'.$filename;
            $user->profile_photo_disk = $disk;
            $user->touch();
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profil-mis-a-jour');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->profile_photo_path) {
            $disk = $user->profile_photo_disk ?: 'local';
            Storage::disk($disk)->deleteDirectory('avatars/'.$user->id);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
