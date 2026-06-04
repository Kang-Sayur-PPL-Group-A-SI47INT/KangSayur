<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\HarvestSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HarvestScheduleController extends Controller
{
    /**
     * Display the harvest calendar for the current month (or the requested month/year).
     */
    public function index(Request $request): View
    {
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        // Clamp month and derive Carbon date for the requested period
        $month = max(1, min(12, $month));
        $date  = Carbon::createFromDate($year, $month, 1);

        $farmer = auth()->user();
        $listingIds = $farmer->listings()->pluck('listing_id');

        // All schedules in the displayed month
        $schedules = HarvestSchedule::with('listing')
            ->whereIn('listing_id', $listingIds)
            ->whereYear('availability_date', $date->year)
            ->whereMonth('availability_date', $date->month)
            ->orderBy('availability_date')
            ->get()
            ->groupBy(fn($s) => $s->availability_date->day);

        // Farmer's active listings for the create form dropdown
        $listings = $farmer->listings()->where('status', 'active')->get();

        return view('farmer.harvest.calendar', [
            'schedules'    => $schedules,
            'listings'     => $listings,
            'currentDate'  => $date,
            'currentMonth' => $date->month,
            'currentYear'  => $date->year,
            'prevMonth'    => $date->copy()->subMonth(),
            'nextMonth'    => $date->copy()->addMonth(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $farmer = auth()->user();
        $farmerListingIds = $farmer->listings()->pluck('listing_id')->toArray();

        $validated = $request->validate([
            'listing_id'        => ['required', 'integer', function ($attr, $value, $fail) use ($farmerListingIds) {
                if (!in_array((int) $value, $farmerListingIds)) {
                    $fail('The selected listing does not belong to you.');
                }
            }],
            'availability_date' => 'required|date|after:today',
            'estimated_stock'   => 'required|integer|min:1',
        ]);

        $exists = HarvestSchedule::where('listing_id', $validated['listing_id'])
            ->where('availability_date', $validated['availability_date'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['availability_date' => 'A schedule already exists for this listing on the selected date.'])->withInput();
        }

        HarvestSchedule::create($validated);

        return back()->with('success', 'Harvest schedule created successfully!');
    }
}