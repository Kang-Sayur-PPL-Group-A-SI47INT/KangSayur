<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\DeliveryFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page with cart items and delivery form.
     */
    public function index()
    {
        $user = auth()->user();
        $cart = $user->getOrCreateCart();
        $cart->load(['items.listing.farmer', 'items.listing.produce']);

        // Redirect back if cart is empty
        if ($cart->items->isEmpty()) {
            return redirect()->route('customer.cart')
                ->with('error', 'Your cart is empty. Add some items before checkout.');
        }

        $subtotal = $cart->totalPrice();
        $deliveryFee = DeliveryFeeService::calculateForCart($cart, $user);
        $grandTotal = $subtotal + $deliveryFee;

        return view('marketplace.checkout', compact(
            'cart',
            'subtotal',
            'deliveryFee',
            'grandTotal'
        ));
    }

    /**
     * Process the checkout — create transaction and redirect to payment simulation.
     */
    public function process(Request $request)
    {
        $request->validate([
            'delivery_name' => 'required|string|max:100',
            'delivery_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $cart = $user->getOrCreateCart();
        $cart->load(['items.listing.farmer', 'items.listing.produce']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('customer.cart')
                ->with('error', 'Your cart is empty.');
        }

        $subtotal = $cart->totalPrice();
        $deliveryFee = DeliveryFeeService::calculateForCart($cart, $user);

        // Generate unique order ID
        $orderId = 'KS-' . strtoupper(Str::random(8)) . '-' . time();

        try {
            $transaction = DB::transaction(function () use (
                $request, $user, $cart, $subtotal, $deliveryFee, $orderId
            ) {
                // Create the transaction record
                $transaction = Transaction::create([
                    'total_price' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'delivery_name' => $request->delivery_name,
                    'delivery_phone' => $request->delivery_phone,
                    'delivery_address' => $request->delivery_address,
                    'status' => 'pending',
                    'midtrans_order_id' => $orderId,
                    'user_user_id' => $user->user_id,
                    'cart_cart_id' => $cart->cart_id,
                ]);

                // Snapshot cart items into transaction items
                foreach ($cart->items as $item) {
                    $unitPrice = $item->unitPrice();
                    TransactionItem::create([
                        'quantity' => $item->quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $item->quantity * $unitPrice,
                        'transaction_transaction_id' => $transaction->transaction_id,
                        'listing_listing_id' => $item->listing_listing_id,
                    ]);
                }

                return $transaction;
            });

            return redirect()->route('customer.checkout.payment', $transaction->transaction_id);
        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Something went wrong during checkout. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the payment simulation page.
     */
    public function paymentPage(Transaction $transaction)
    {
        // Verify ownership
        if ($transaction->user_user_id !== auth()->id()) {
            abort(403);
        }

        // If already paid, redirect to order detail
        if ($transaction->isPaid()) {
            return redirect()->route('customer.orders.detail', $transaction->transaction_id)
                ->with('success', 'This order has already been paid.');
        }

        $transaction->load(['items.listing.farmer', 'items.listing.produce']);

        return view('marketplace.payment', compact('transaction'));
    }

    /**
     * Simulate a successful payment (no real payment gateway needed).
     */
    public function simulatePayment(Transaction $transaction)
    {
        // Verify ownership
        if ($transaction->user_user_id !== auth()->id()) {
            abort(403);
        }

        if ($transaction->status !== 'pending') {
            return redirect()->route('customer.orders.detail', $transaction->transaction_id)
                ->with('info', 'This order has already been processed.');
        }

        $transaction->update([
            'status' => 'paid',
            'payment_type' => 'simulated',
            'paid_at' => now(),
        ]);

        // Clear the cart items after successful payment
        $cart = $transaction->cart;
        if ($cart) {
            $cart->items()->delete();
        }

        return redirect()->route('customer.orders.detail', $transaction->transaction_id)
            ->with('success', 'Payment successful! Your order is being processed. 🎉');
    }

    /**
     * Show order history.
     */
    public function orders()
    {
        $orders = Transaction::where('user_user_id', auth()->id())
            ->with(['items.listing.produce'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    /**
     * Show a single order detail.
     */
    public function orderDetail(Transaction $transaction)
    {
        if ($transaction->user_user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load(['items.listing.farmer', 'items.listing.produce']);

        return view('customer.order-detail', compact('transaction'));
    }
}
