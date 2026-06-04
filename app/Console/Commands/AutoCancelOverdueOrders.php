<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCancelOverdueOrders extends Command
{
    protected $signature = 'orders:auto-cancel';

    protected $description = 'Automatically cancel orders where farmer has not uploaded shipping proof within 6 hours of payment';

    public function handle(): int
    {
        $overdueOrders = Transaction::where('status', 'paid')
            ->whereNull('shipping_proof')
            ->whereNotNull('paid_status_at')
            ->where('paid_status_at', '<=', now()->subHours(6))
            ->get();

        $count = 0;

        foreach ($overdueOrders as $order) {
            // Restore stock for each item
            foreach ($order->items as $item) {
                $listing = $item->listing;
                if ($listing) {
                    $listing->update([
                        'quantity' => $listing->quantity + $item->quantity,
                        'status' => $listing->quantity + $item->quantity > 0 && $listing->status === 'sold_out'
                            ? 'active' : $listing->status,
                    ]);
                }
            }

            $order->update(['status' => 'cancelled']);
            $count++;

            Log::info("Auto-cancelled order #{$order->transaction_id} (Order ID: {$order->midtrans_order_id}) - no shipping proof after 6 hours.");
        }

        $this->info("Auto-cancelled {$count} overdue order(s).");

        return Command::SUCCESS;
    }
}
