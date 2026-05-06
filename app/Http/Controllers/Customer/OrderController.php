<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        // === MOCK DATA (KARENA TABEL TRANSACTIONS BELUM ADA) ===
        // JANGAN DIUBAH KEMBALI KE QUERY DATABASE SAMPAI TABEL DIBUAT
        
        $user = auth()->user() ?? new \App\Models\User(['name' => 'John Customer']);

        $listing1 = new \App\Models\Listing(['title' => 'Sayur Bayam Segar', 'price' => 10000]);
        $listing2 = new \App\Models\Listing(['title' => 'Wortel Manis', 'price' => 15000]);

        $item1 = new \App\Models\CartItem(['quantity' => 2]);
        $item1->setRelation('listing', $listing1);
        
        $item2 = new \App\Models\CartItem(['quantity' => 1]);
        $item2->setRelation('listing', $listing2);

        $cart = new \App\Models\Cart();
        $cart->setRelation('items', collect([$item1, $item2]));

        $order1 = new Transaction([
            'midtrans_order_id' => 'KS-CUST-001',
            'status' => 'paid',
            'total_price' => 35000,
            'delivery_fee' => 10000,
        ]);
        $order1->transaction_id = 1;
        $order1->created_at = now()->subHours(2);
        $order1->setRelation('user', $user);
        $order1->setRelation('cart', $cart);

        $order2 = new Transaction([
            'midtrans_order_id' => 'KS-CUST-002',
            'status' => 'pending',
            'total_price' => 35000,
            'delivery_fee' => 10000,
        ]);
        $order2->transaction_id = 2;
        $order2->created_at = now()->subMinutes(15);
        $order2->setRelation('user', $user);
        $order2->setRelation('cart', $cart);

        $orders = new \Illuminate\Pagination\LengthAwarePaginator([$order1, $order2], 2, 10, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('customer.orders', compact('orders'));
    }
}
