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
        $orders = Transaction::with('cart.items.listing')
            ->where('user_user_id', auth()->user()->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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