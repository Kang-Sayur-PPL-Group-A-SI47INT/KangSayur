<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'farm_description' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:45',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'doc_skp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_nib' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_ktp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_skt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_land_cert' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = $request->only(['name', 'farm_description', 'city', 'address', 'latitude', 'longitude']);
        $data['is_public_profile'] = $request->has('is_public_profile') ? 1 : 0;

        // Handle profile photo
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Handle verification documents
        $docFields = ['doc_skp', 'doc_nib', 'doc_ktp', 'doc_skt', 'doc_land_cert'];
        foreach ($docFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old document if exists
                if ($user->$field) {
                    Storage::disk('public')->delete($user->$field);
                }
                $data[$field] = $request->file($field)->store('farmer-documents', 'public');
            }
        }

        $user->update($data);

        return redirect()->route('farmer.profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
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

    /**
     * Update verification documents only.
     */
    public function updateDocuments(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'doc_skp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_nib' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_ktp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_skt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'doc_land_cert' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = [];
        $docFields = ['doc_skp', 'doc_nib', 'doc_ktp', 'doc_skt', 'doc_land_cert'];
        $uploaded = 0;

        foreach ($docFields as $field) {
            if ($request->hasFile($field)) {
                if ($user->$field) {
                    Storage::disk('public')->delete($user->$field);
                }
                $data[$field] = $request->file($field)->store('farmer-documents', 'public');
                $uploaded++;
            }
        }

        if (empty($data)) {
            return redirect()->route('farmer.profile.edit')
                ->with('error', 'Tidak ada dokumen yang dipilih untuk diunggah.');
        }

        $user->update($data);

        return redirect()->route('farmer.profile.edit')
            ->with('success', "$uploaded dokumen berhasil diunggah!");
    }

    /**
     * Submit documents for verification review.
     */
    public function submitVerification(): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->hasAllDocuments()) {
            return redirect()->route('farmer.profile.edit')
                ->with('error', 'Harap unggah semua dokumen verifikasi terlebih dahulu.');
        }

        if ($user->verification_status === 'pending') {
            return redirect()->route('farmer.profile.edit')
                ->with('info', 'Dokumen Anda sudah dalam proses verifikasi.');
        }

        $user->update([
            'verification_status' => 'pending',
            'rejection_note' => null,
        ]);

        return redirect()->route('farmer.profile.edit')
            ->with('success', 'Dokumen berhasil dikirim untuk verifikasi! Admin akan meninjau dalam 1-3 hari kerja.');
    }
}
