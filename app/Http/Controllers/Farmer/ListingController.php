<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Produce;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = auth()->user()->listings()->with('produce');

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('produce', fn($q) => $q->where('category', $request->category));
        }

        // Status filter
        if ($request->filled('status')) {
            match ($request->status) {
                'low_stock' => $query->whereRaw('CAST(quantity AS UNSIGNED) <= 10')->where('status', '!=', 'sold_out'),
                default => $query->where('status', $request->status),
            };
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(12);
        $categories = Produce::distinct()->pluck('category');

        return view('farmer.listings.index', compact('listings', 'categories'));
    }

    public function create(): View
    {
        $produces = Produce::all();
        return view('farmer.listings.create', compact('produces'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string|max:20',
            'produce_produce_id' => 'required|exists:produces,produce_id',
            'availability_date' => 'nullable|date',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'images.*.image' => 'Invalid file format. Only JPG, PNG, and WEBP are allowed.',
            'images.*.mimes' => 'Invalid file format. Only JPG, PNG, and WEBP are allowed.',
        ]);

        $data = $request->only(['title', 'content', 'price', 'quantity', 'unit', 'produce_produce_id', 'availability_date']);
        $data['user_user_id'] = auth()->user()->user_id;
        // Status based on quantity: zero stock → inactive
        $data['status'] = ((int) $data['quantity'] <= 0) ? 'inactive' : 'active';

        // Handle multiple images
        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('listings', 'public');
            }
            $data['image'] = json_encode($paths);
        }

        $listing = Listing::create($data);

        return redirect()->route('farmer.listings.index')->with('success', 'Listing created successfully!');
    }

    public function edit(Listing $listing): View
    {
        $this->authorize($listing);
        $produces = Produce::all();
        return view('farmer.listings.edit', compact('listing', 'produces'));
    }

    public function update(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize($listing);

        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'produce_produce_id' => 'required|exists:produces,produce_id',
            'status' => 'required|in:active,inactive,sold_out',
            'availability_date' => 'nullable|date',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Enforce: cannot set active if quantity is 0
        if ((int) $request->quantity <= 0 && $request->status === 'active') {
            return back()->withInput()->with('error', 'Tidak dapat mengaktifkan listing dengan stok 0. Tambahkan stok terlebih dahulu.');
        }

        $data = $request->only(['title', 'content', 'price', 'quantity', 'unit', 'produce_produce_id', 'status', 'availability_date']);

        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('listings', 'public');
            }
            $data['image'] = json_encode($paths);
        }

        $listing->update($data);

        return redirect()->route('farmer.listings.index')->with('success', 'Listing updated successfully!');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorize($listing);

        if ($listing->hasActiveOrders()) {
            return back()->with('error', 'Cannot delete this listing — it has active orders.');
        }

        $listing->delete();

        return redirect()->route('farmer.listings.index')->with('success', 'Listing deleted.');
    }

    public function getAveragePrice($produce_id)
    {
        $avg = Listing::getAveragePrice($produce_id);

        return response()->json([
            'average_price' => round($avg, 0)
        ]);
    }

    private function authorize(Listing $listing): void
    {
        if ($listing->user_user_id !== auth()->user()->user_id) {
            abort(403);
        }
    }
}