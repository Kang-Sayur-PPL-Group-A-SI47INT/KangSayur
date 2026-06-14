<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\View\View;

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
}
