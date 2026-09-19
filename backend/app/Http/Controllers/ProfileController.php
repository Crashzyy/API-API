<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto_profile' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto_profile.required' => 'Silakan pilih foto profil terlebih dahulu.',
            'foto_profile.image' => 'File yang dipilih harus berupa gambar.',
            'foto_profile.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'foto_profile.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $user = $request->user();
        $directory = public_path('storage/profile');
        File::ensureDirectoryExists($directory);

        if ($user->foto_profile && File::exists(public_path($user->foto_profile))) {
            File::delete(public_path($user->foto_profile));
        }

        $file = $request->file('foto_profile');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        $user->update([
            'foto_profile' => 'storage/profile/' . $filename,
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
