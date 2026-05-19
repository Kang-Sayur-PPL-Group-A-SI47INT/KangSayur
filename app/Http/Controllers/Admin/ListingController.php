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
        $query = Listing::with(['farmer', 'produce']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $listings = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.listings.index', compact('listings'));
    }
}
