<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class FarmerVerificationController extends Controller
{
    /**
     * List all farmers with filterable verification status.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'farmer');
        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }
        $farmers = $query->orderByRaw("
            FIELD(verification_status, 'pending', 'unverified', 'rejected', 'verified')
        ")->paginate(15);
        $counts = [
            'all' => User::where('role', 'farmer')->count(),
            'pending' => User::where('role', 'farmer')->where('verification_status', 'pending')->count(),
            'verified' => User::where('role', 'farmer')->where('verification_status', 'verified')->count(),
            'rejected' => User::where('role', 'farmer')->where('verification_status', 'rejected')->count(),
            'unverified' => User::where('role', 'farmer')->where('verification_status', 'unverified')->count(),
        ];
        return view('admin.verifications.index', compact('farmers', 'counts'));
    }
    /**
     * Show a farmer's uploaded documents for review.
     */
    public function show(User $user): View
    {
        if ($user->role !== 'farmer') {
            abort(404);
        }
        return view('admin.verifications.show', compact('user'));
    }
    /**
     * Approve a farmer's verification.
     */
    public function approve(User $user): RedirectResponse
    {
        if ($user->role !== 'farmer') {
            abort(404);
        }
        $user->update([
            'verification_status' => 'verified',
            'rejection_note' => null,
            'verified_at' => now(),
        ]);
        return redirect()->route('admin.verifications.index')
            ->with('success', "Petani {$user->name} berhasil diverifikasi!");
    }
    /**
     * Reject a farmer's verification with an optional note.
     */
    public function reject(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'farmer') {
            abort(404);
        }
        $request->validate([
            'rejection_note' => 'nullable|string|max:1000',
        ]);
        $user->update([
            'verification_status' => 'rejected',
            'rejection_note' => $request->rejection_note ?? 'Dokumen tidak memenuhi persyaratan.',
            'verified_at' => null,
        ]);
        return redirect()->route('admin.verifications.index')
            ->with('success', "Verifikasi petani {$user->name} ditolak.");
    }
}