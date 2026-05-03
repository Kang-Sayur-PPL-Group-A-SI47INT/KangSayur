<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        return view('farmer.profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:45',
            'city' => 'nullable|string|max:45',
            'address' => 'nullable|string|max:255',
            'farm_description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_public_profile' => 'boolean',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'city', 'address', 'farm_description', 'latitude', 'longitude']);
        $data['is_public_profile'] = $request->boolean('is_public_profile');

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        $user->update($data);

        return redirect()->route('farmer.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    public function show($userId)
    {
        $farmer = \App\Models\User::where('user_id', $userId)
            ->where('role', 'farmer')
            ->where('is_public_profile', true)
            ->firstOrFail();

        $listings = $farmer->listings()->where('status', 'active')->paginate(12);
        $averageRating = $farmer->averageRating();
        $totalListings = $farmer->totalListings();

        return view('farmer.profile.show', compact('farmer', 'listings', 'averageRating', 'totalListings'));
    }
}
