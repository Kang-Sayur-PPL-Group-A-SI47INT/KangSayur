<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\HarvestSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HarvestCalendarController extends Controller
{
    /**
     * Display the public harvest calendar for customers.
     * Shows all upcoming schedules from all active farmers.
     */
    public function index(Request $request): View
    {
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year',  now()->year);
        $farmerFilter = $request->query('farmer');

        // Clamp month
        $month = max(1, min(12, $month));
        $date  = Carbon::createFromDate($year, $month, 1);

        // Build base query: only future or current-month schedules from active listings
        $query = HarvestSchedule::with(['listing.farmer', 'listing.produce'])
            ->whereHas('listing', fn ($q) => $q->where('status', 'active'))
            ->whereYear('availability_date', $date->year)
            ->whereMonth('availability_date', $date->month)
            ->orderBy('availability_date');

        // Optional: filter by a specific farmer
        if ($farmerFilter) {
            $query->whereHas('listing.farmer', fn ($q) => $q->where('user_id', $farmerFilter));
        }

        $schedules = $query->get()->groupBy(fn ($s) => $s->availability_date->day);

        // Farmers who have at least one active listing with a schedule this month (for filter dropdown)
        $farmers = User::where('role', 'farmer')
            ->whereHas('listings', fn ($q) => $q->where('status', 'active')
                ->whereHas('harvestSchedules', fn ($q2) =>
                    $q2->whereYear('availability_date', $date->year)
                       ->whereMonth('availability_date', $date->month)
                )
            )
            ->orderBy('name')
            ->get(['user_id', 'name']);

        return view('customer.harvest.calendar', [
            'schedules'     => $schedules,
            'farmers'       => $farmers,
            'farmerFilter'  => $farmerFilter,
            'currentDate'   => $date,
            'currentMonth'  => $date->month,
            'currentYear'   => $date->year,
            'prevMonth'     => $date->copy()->subMonth(),
            'nextMonth'     => $date->copy()->addMonth(),
        ]);
    }
}
