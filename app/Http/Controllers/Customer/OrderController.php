<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\CoreApi;

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
        $order1->setRelation('items', collect([$item1, $item2]));

        $order2 = new Transaction([
            'midtrans_order_id' => 'KS-CUST-002',
            'status' => 'pending',
            'total_price' => 35000,
            'delivery_fee' => 10000,
        ]);
        $order2->transaction_id = 2;
        $order2->created_at = now()->subMinutes(15);
        $order2->setRelation('user', $user);
        $order2->setRelation('items', collect([$item1, $item2]));

        $orders = new \Illuminate\Pagination\LengthAwarePaginator([$order1, $order2], 2, 10, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('customer.orders', compact('orders'));
    }


    public function pay(Request $request, $transaction_id)
    {
        // For demo, use your mock transaction
        $transaction = new Transaction([
            'midtrans_order_id' => 'KS-CUST-001',
            'total_price'       => 35000,
            'delivery_fee'      => 10000,
        ]);

        $grossAmount = $transaction->total_price + $transaction->delivery_fee;
        $method = $request->input('method'); // bank_transfer, credit_card, gopay, qris

        $baseParams = [
            'transaction_details' => [
                'order_id'     => $transaction->midtrans_order_id . '-' . time(),
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name ?? 'Customer',
                'email'      => auth()->user()->email ?? 'customer@demo.com',
            ],
        ];

        $params = match($method) {
            'bank_transfer' => array_merge($baseParams, [
                'payment_type'   => 'bank_transfer',
                'bank_transfer'  => ['bank' => $request->input('bank', 'bca')],
            ]),
            'credit_card' => array_merge($baseParams, [
                'payment_type' => 'credit_card',
                'credit_card'  => [
                    'token_id'        => $request->input('card_token'),
                    'authentication'  => true,
                ],
            ]),
            'gopay' => array_merge($baseParams, [
                'payment_type' => 'gopay',
                'gopay'        => ['enable_callback' => false],
            ]),
            'qris' => array_merge($baseParams, [
                'payment_type' => 'qris',
                'qris'         => ['acquirer' => 'gopay'],
            ]),
        };

        $response = CoreApi::charge($params);
        return response()->json($response);
    }
}