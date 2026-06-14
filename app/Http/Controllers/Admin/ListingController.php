<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::with(['farmer', 'produce'])->withAvg('ratings', 'score');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $allowed   = ['title', 'created_at', 'price', 'quantity', 'avg_rating'];
        $sort      = in_array($request->get('sort'), $allowed) ? $request->get('sort') : 'created_at';
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';

        // 'avg_rating' maps to the withAvg column name
        $dbSort = $sort === 'avg_rating' ? 'ratings_avg_score' : $sort;
        $query->orderBy($dbSort, $direction);

        $listings = $query->paginate(15);

        return view('admin.listings.index', compact('listings', 'sort', 'direction'));
    }

    public function destroy(Listing $listing)
    {
        $listing->update(['status' => 'inactive']);
        return back()->with('success', 'Listing berhasil dinonaktifkan.');
    }
}
