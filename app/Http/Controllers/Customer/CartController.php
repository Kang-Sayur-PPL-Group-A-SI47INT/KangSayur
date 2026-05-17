<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Listing;
use App\Services\DeliveryFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
class CartController extends Controller
{
    /**
     * Display the shopping cart page.
     */
    public function index()
    {
        $user = auth()->user();
        $cart = $user->getOrCreateCart();
        $cart->load([
            'items.listing.farmer',
            'items.listing.produce',
            
        ]);
        // Calculate totals
        $subtotal = $cart->totalPrice();
        $deliveryFee = DeliveryFeeService::calculateForCart($cart, $user);
        $grandTotal = $subtotal + $deliveryFee;
        // Get recommended listings (4 random listings not already in cart)
        $cartListingIds = $cart->items->pluck('listing_listing_id')->toArray();
        $recommended = collect();
        if (Schema::hasTable('listings')) {
            $recommended = Listing::with(['farmer', 'produce', 'ratings'])
                ->where('status', 'active')
                ->whereNotIn('listing_id', $cartListingIds)
                ->inRandomOrder()
                ->take(4)
                ->get();
        }
        // If no real listings exist, use dummy recommended data
        if ($recommended->isEmpty()) {
            $recommended = $this->getDummyRecommendedListings();
        }
        return view('customer.cart', compact(
            'cart',
            'subtotal',
            'deliveryFee',
            'grandTotal',
            'recommended'
        ));
    }
    /**
     * Add an item to the cart.
     */
    public function add(Request $request, Listing $listing)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        // Check listing is active and has stock
        if ($listing->status !== 'active') {
            return redirect()->back()->with('error', 'Produk ini tidak tersedia saat ini.');
        }
        $cart = auth()->user()->getOrCreateCart();
        // Check if item already exists in cart
        $existingItem = $cart->items()
            ->where('listing_listing_id', $listing->listing_id)
            ->first();
        $requestedQty = $request->quantity;
        $currentCartQty = $existingItem ? $existingItem->quantity : 0;
        $totalQty = $currentCartQty + $requestedQty;
        // Validate against available stock
        if ($totalQty > $listing->quantity) {
            return redirect()->back()
                ->with('error', "Stok tidak mencukupi. Tersedia: {$listing->quantity} {$listing->unit}, di keranjang: {$currentCartQty}.");
        }
        if ($existingItem) {

            $existingItem->increment('quantity', $requestedQty);
        } else {
            CartItem::create([
                'quantity' => $requestedQty,
                'cart_cart_id' => $cart->cart_id,
                'listing_listing_id' => $listing->listing_id,
                'offer_offer_id' => $request->offer_id ?? null,
            ]);
        }
        return redirect()->back()->with('success', 'Item added to cart!');
    }
    /**
     * Update item quantity in cart.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        // Verify ownership
        $cart = auth()->user()->getOrCreateCart();
        if ($cartItem->cart_cart_id !== $cart->cart_id) {
            abort(403);
        }
        $cartItem->update(['quantity' => $request->quantity]);
        return redirect()->back()->with('success', 'Cart updated!');
    }
    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $cartItem)
    {
        // Verify ownership
        $cart = auth()->user()->getOrCreateCart();
        if ($cartItem->cart_cart_id !== $cart->cart_id) {
            abort(403);
        }
        $cartItem->delete();
        return redirect()->back()->with('success', 'Item removed from cart.');
    }
    /**
     * Generate dummy recommended listings for display when no real data exists.
     */
    private function getDummyRecommendedListings()
    {
        $dummyItems = [
            [
                'listing_id' => 901,
                'title' => 'Organic Red Tomatoes',
                'price' => 18000,
                'unit' => 'kg',
                'image' => null,
                'status' => 'active',
                'category' => 'Vegetables',
                'emoji' => '🍅',
                'gradient' => 'from-red-100 to-red-200',
                'farmer_name' => 'Pak Budi',
                'rating' => 4.7,
            ],
            [
                'listing_id' => 902,
                'title' => 'Fresh Corn on the Cob',
                'price' => 12000,
                'unit' => 'kg',
                'image' => null,
                'status' => 'active',
                'category' => 'Vegetables',
                'emoji' => '🌽',
                'gradient' => 'from-yellow-100 to-amber-200',
                'farmer_name' => 'Bu Siti',
                'rating' => 4.5,
            ],
            [
                'listing_id' => 903,
                'title' => 'Premium Dragon Fruit',
                'price' => 35000,
                'unit' => 'kg',
                'image' => null,
                'status' => 'active',
                'category' => 'Fruits',
                'emoji' => '🐉',
                'gradient' => 'from-pink-100 to-fuchsia-200',
                'farmer_name' => 'Pak Agus',
                'rating' => 4.9,
            ],
            [
                'listing_id' => 904,
                'title' => 'Fresh Baby Spinach',
                'price' => 15000,
                'unit' => 'kg',
                'image' => null,
                'status' => 'active',
                'category' => 'Greens',
                'emoji' => '🥬',
                'gradient' => 'from-green-100 to-emerald-200',
                'farmer_name' => 'Bu Ani',
                'rating' => 4.6,
            ],
        ];
        return collect($dummyItems)->map(function ($item) {
            return (object) $item;
        });
    }
}