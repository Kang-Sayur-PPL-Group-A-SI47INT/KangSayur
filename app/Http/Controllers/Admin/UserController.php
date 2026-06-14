<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedIdentifier;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        // Add avg_rating as a computed column via correlated subquery (farmers only)
        $query = User::query()->addSelect([
            'users.*',
            DB::raw('(SELECT COALESCE(AVG(r.score), 0)
                      FROM ratings r
                      JOIN listings l ON r.listing_listing_id = l.listing_id
                      WHERE l.user_user_id = users.user_id) AS avg_rating'),
        ]);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false);
            }
        }

        $allowed   = ['name', 'created_at', 'role', 'avg_rating'];
        $sort      = in_array($request->get('sort'), $allowed) ? $request->get('sort') : 'created_at';
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sort, $direction);

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users', 'sort', 'direction'));
    }

    public function ban(Request $request, User $user)
    {
        $request->validate([
            'ban_reason' => 'required|string|max:500',
        ]);

        if ($user->isAdmin()) {
            abort(403);
        }

        $user->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $request->ban_reason,
        ]);

        BannedIdentifier::create([
            'type' => 'email',
            'value' => $user->email,
            'user_user_id' => $user->user_id,
            'banned_by' => auth()->id(),
            'reason' => $request->ban_reason,
        ]);

        if ($user->doc_ktp) {
            BannedIdentifier::create([
                'type' => 'ktp',
                'value' => $user->doc_ktp,
                'user_user_id' => $user->user_id,
                'banned_by' => auth()->id(),
                'reason' => $request->ban_reason,
            ]);
        }

        $user->listings()->update(['status' => 'inactive']);

        return back()->with('success', 'Pengguna berhasil di-ban.');
    }

    public function unban(User $user)
    {
        $user->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
        ]);

        BannedIdentifier::where('user_user_id', $user->user_id)->delete();

        return back()->with('success', 'Pengguna berhasil di-unban.');
    }
}
