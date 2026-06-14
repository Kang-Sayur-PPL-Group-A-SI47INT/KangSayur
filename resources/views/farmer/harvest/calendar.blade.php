<x-app-layout>
    @php $title = 'Harvest Calendar'; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="harvestCalendar()">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Harvest Calendar 📅</h1>
                <p class="text-gray-500 mt-1">Plan and track your upcoming harvests.</p>
            </div>
            <button @click="openCreateModal()"
                class="px-5 py-2.5 bg-green-800 text-white font-semibold rounded-full hover:bg-green-900 transition-all shadow-lg shadow-green-200/50 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Schedule
            </button>
        </div>

        {{-- Month Navigation --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 flex items-center justify-between">
            <a href="{{ route('farmer.harvest-calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
               class="p-2 rounded-xl hover:bg-gray-100 transition-colors text-gray-600 hover:text-green-700 flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span class="hidden sm:inline text-sm font-medium">{{ $prevMonth->format('M') }}</span>
            </a>
            <h2 class="text-lg font-bold text-gray-900">{{ $currentDate->format('F Y') }}</h2>
            <a href="{{ route('farmer.harvest-calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
               class="p-2 rounded-xl hover:bg-gray-100 transition-colors text-gray-600 hover:text-green-700 flex items-center gap-1">
                <span class="hidden sm:inline text-sm font-medium">{{ $nextMonth->format('M') }}</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Calendar Grid --}}
        @php
            $startOfMonth  = $currentDate->copy()->startOfMonth();
            $endOfMonth    = $currentDate->copy()->endOfMonth();
            $startDay      = $startOfMonth->dayOfWeek; // 0 = Sunday
            $daysInMonth   = $endOfMonth->day;
            $today         = now()->format('Y-m-d');
            $isCurrentMonth = (now()->month === $currentMonth && now()->year === $currentYear);
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            {{-- Day Headers --}}
            <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-100">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $day }}</div>
                @endforeach
            </div>

            {{-- Calendar Cells --}}
            <div class="grid grid-cols-7">
                {{-- Leading empty cells --}}
                @for($i = 0; $i < $startDay; $i++)
                    <div class="min-h-[100px] sm:min-h-[120px] border-b border-r border-gray-50 bg-gray-50/30"></div>
                @endfor

                {{-- Day cells --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = $currentDate->copy()->day($day)->format('Y-m-d');
                        $isPast  = $dateStr < $today;
                        $isToday = $dateStr === $today;
                        $daySchedules = $schedules->get($day, collect());
                    @endphp
                    <div dusk="day-cell-{{ $day }}" class="min-h-[100px] sm:min-h-[120px] border-b border-r border-gray-50 p-1.5 sm:p-2 relative transition-colors duration-150
                        {{ $isPast ? 'bg-gray-50/50' : 'hover:bg-green-50/30 cursor-pointer' }}
                        {{ $isToday ? 'ring-2 ring-inset ring-green-500/40 bg-green-50/20' : '' }}"
                        @if($daySchedules->count() > 0)
                            @click="openDateDetail({{ $day }}, '{{ $currentDate->copy()->day($day)->format('M d, Y') }}')"
                        @endif>

                        {{-- Date Number --}}
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs sm:text-sm font-semibold {{ $isPast ? 'text-gray-300' : ($isToday ? 'text-green-700' : 'text-gray-700') }}">
                                {{ $day }}
                            </span>
                            @if($isToday)
                                <span class="hidden sm:inline text-[10px] font-medium text-green-600 bg-green-100 px-1.5 py-0.5 rounded-full">Today</span>
                            @endif
                        </div>

                        {{-- Event Badges --}}
                        @foreach($daySchedules->take(3) as $schedule)
                            <div class="mb-0.5 px-1.5 py-0.5 rounded-md text-[10px] sm:text-xs font-medium truncate
                                {{ $isPast ? 'bg-gray-100 text-gray-400' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ Str::limit($schedule->listing->title, 12) }}
                                <span class="font-bold">×{{ $schedule->estimated_stock }}</span>
                                @if(!$isPast && $schedule->listing->hasDiscount() && $schedule->listing->auto_discount)
                                    <span class="ml-0.5 text-amber-600">🏷️</span>
                                @endif
                            </div>
                        @endforeach
                        @if($daySchedules->count() > 3)
                            <div class="text-[10px] text-gray-400 font-medium px-1.5">+{{ $daySchedules->count() - 3 }} more</div>
                        @endif
                    </div>
                @endfor

                {{-- Trailing empty cells --}}
                @php $totalCells = $startDay + $daysInMonth; @endphp
                @for($i = $totalCells; $i % 7 !== 0; $i++)
                    <div class="min-h-[100px] sm:min-h-[120px] border-b border-r border-gray-50 bg-gray-50/30"></div>
                @endfor
            </div>
        </div>

        {{-- Empty State --}}
        @if($schedules->isEmpty())
            <div class="text-center py-12 mt-6 bg-white rounded-2xl border border-gray-100">
                <div class="text-5xl mb-3">🌾</div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">No harvests scheduled</h3>
                <p class="text-gray-500 text-sm mb-4">Plan your upcoming harvests for {{ $currentDate->format('F Y') }}.</p>
                <button @click="openCreateModal()"
                    class="px-5 py-2.5 bg-green-800 text-white font-semibold rounded-full hover:bg-green-900 transition-all text-sm">
                    + Add Your First Schedule
                </button>
            </div>
        @endif

        {{-- =============================== --}}
        {{-- MODALS                          --}}
        {{-- =============================== --}}

        {{-- Date Detail Modal --}}
        <div x-show="showDateDetail" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showDateDetail = false"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 max-h-[80vh] overflow-y-auto"
                 @click.away="showDateDetail = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900">
                            Harvests on <span x-text="selectedDateLabel" class="text-green-700"></span>
                        </h3>
                        <button @click="showDateDetail = false" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="space-y-3">
                        <template x-for="s in (schedulesData[selectedDay] || [])" :key="s.id">
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm" x-text="s.title"></p>
                                        <p class="text-xs text-gray-500 mt-0.5" x-text="s.dateLabel"></p>
                                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold"
                                                  :class="s.isPast ? 'bg-gray-200 text-gray-500' : 'bg-emerald-100 text-emerald-700'">
                                                <span x-text="'📦 ' + s.stock + ' units'"></span>
                                            </span>
                                            <template x-if="s.hasDiscount">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-700"
                                                      x-text="'🏷️ ' + s.discountPct + '% OFF'"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <template x-if="!s.isPast">
                                        <div class="flex gap-1.5 ml-3 flex-shrink-0">
                                            <button @click="openEditModal(s.id, s.listing_id, s.date, s.stock)"
                                                :dusk="'edit-schedule-' + s.id"
                                                class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="openDeleteModal(s.id)"
                                                :dusk="'delete-schedule-' + s.id"
                                                class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="s.isPast">
                                        <span class="text-[10px] font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded-full ml-3">Past</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="(schedulesData[selectedDay] || []).length === 0">
                            <p class="text-gray-400 text-sm text-center py-4">No schedules for this date.</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Schedule Modal --}}
        <div x-show="showCreate" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showCreate = false"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10"
                 @click.away="showCreate = false">
                <form method="POST" action="{{ route('farmer.harvest-schedules.store') }}" class="p-6">
                    @csrf
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900">Add Harvest Schedule</h3>
                        <button type="button" @click="showCreate = false" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Listing Select --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Listing</label>
                        <select name="listing_id" required x-model="createListingId" @change="updateDiscountPreview()"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-200 transition-all">
                            <option value="">Select a listing...</option>
                            @foreach($listings as $listing)
                                <option value="{{ $listing->listing_id }}" {{ old('listing_id') == $listing->listing_id ? 'selected' : '' }}>
                                    {{ $listing->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Picker --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Availability Date</label>
                        <input type="date" name="availability_date" required
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            value="{{ old('availability_date') }}"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-200 transition-all">
                    </div>

                    {{-- Estimated Stock --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Estimated Stock</label>
                        <input type="number" name="estimated_stock" min="1" required
                            x-model="createStock" @input="updateDiscountPreview()"
                            value="{{ old('estimated_stock') }}"
                            placeholder="e.g. 50"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-200 transition-all">
                    </div>

                    {{-- Discount Preview --}}
                    <div x-show="discountPreview" x-transition
                         class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-800">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🏷️</span>
                            <span x-text="discountPreview"></span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-green-800 text-white font-semibold rounded-xl hover:bg-green-900 transition-all shadow-lg shadow-green-200/50">
                        Create Schedule
                    </button>
                </form>
            </div>
        </div>

        {{-- Edit Schedule Modal --}}
        <div x-show="showEdit" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showEdit = false"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10"
                 @click.away="showEdit = false">
                <form dusk="edit-schedule-form" method="POST" :action="editAction" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900">Edit Harvest Schedule</h3>
                        <button type="button" @click="showEdit = false" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Listing Select --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Listing</label>
                        <select name="listing_id" required x-model="editListingId"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-200 transition-all">
                            @foreach($listings as $listing)
                                <option value="{{ $listing->listing_id }}">{{ $listing->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Picker --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Availability Date</label>
                        <input type="date" name="availability_date" required
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            x-model="editDate"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-200 transition-all">
                    </div>

                    {{-- Estimated Stock --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Estimated Stock</label>
                        <input type="number" name="estimated_stock" min="1" required
                            x-model="editStock"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-200 transition-all">
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-green-800 text-white font-semibold rounded-xl hover:bg-green-900 transition-all shadow-lg shadow-green-200/50">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDelete" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showDelete = false"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative z-10 p-6"
                 @click.away="showDelete = false">
                <div class="text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-red-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Schedule?</h3>
                    <p class="text-sm text-gray-500 mb-6">This action cannot be undone. The harvest schedule will be permanently removed.</p>
                    <div class="flex gap-3">
                        <button @click="showDelete = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                            Cancel
                        </button>
                        <form :action="deleteAction" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full py-2.5 rounded-xl bg-red-600 text-white font-medium hover:bg-red-700 transition-colors text-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Alpine.js Component --}}
    <script>
        function harvestCalendar() {
            return {
                // Modal states
                showDateDetail: false,
                showCreate: false,
                showEdit: false,
                showDelete: false,

                // Date detail
                selectedDay: null,
                selectedDateLabel: '',

                // Create form
                createListingId: '',
                createStock: '',
                discountPreview: '',

                // Edit form
                editAction: '',
                editListingId: '',
                editDate: '',
                editStock: '',

                // Delete form
                deleteAction: '',

                // Listing averages for discount preview (passed from server)
                listingAverages: (function() {
                    @php
                        $averages = [];
                        foreach ($listings as $listing) {
                            $averages[$listing->listing_id] = \App\Services\HarvestDiscountService::calculateAverageHarvest($listing);
                        }
                    @endphp
                    return {!! json_encode($averages) !!};
                })(),

                // Discount tier table (mirrors server-side)
                discountTiers: [
                    { threshold: 25, discount: 15 },
                    { threshold: 20, discount: 12 },
                    { threshold: 15, discount: 10 },
                    { threshold: 10, discount: 7 },
                    { threshold: 7, discount: 5 },
                    { threshold: 5, discount: 3 },
                ],

                // Schedule data indexed by day
                schedulesData: (function() {
                    @php
                        $schedulesJson = $schedules->map(function ($daySchedules) {
                            return $daySchedules->map(function ($s) {
                                return [
                                    'id'         => $s->id,
                                    'listing_id' => $s->listing_id,
                                    'title'      => $s->listing->title,
                                    'date'       => $s->availability_date->format('Y-m-d'),
                                    'dateLabel'  => $s->availability_date->format('M d, Y'),
                                    'stock'      => $s->estimated_stock,
                                    'isPast'     => $s->isPast(),
                                    'hasDiscount'=> $s->listing->hasDiscount() && $s->listing->auto_discount,
                                    'discountPct'=> (float) $s->listing->discount_percentage,
                                ];
                            })->values();
                        });
                    @endphp
                    return {!! json_encode($schedulesJson) !!};
                })(),

                updateDiscountPreview() {
                    const listingId = this.createListingId;
                    const stock = parseFloat(this.createStock);

                    if (!listingId || !stock || stock <= 0) {
                        this.discountPreview = '';
                        return;
                    }

                    const avg = this.listingAverages[listingId] || 0;
                    if (avg <= 0) {
                        this.discountPreview = '';
                        return;
                    }

                    const surplusPercent = ((stock - avg) / avg) * 100;
                    let discount = 0;

                    for (const tier of this.discountTiers) {
                        if (surplusPercent >= tier.threshold) {
                            discount = tier.discount;
                            break;
                        }
                    }

                    if (discount > 0) {
                        this.discountPreview = `Based on your average harvest of ${Math.round(avg)} units, this schedule of ${Math.round(stock)} units (+${Math.round(surplusPercent)}% surplus) will trigger a ${discount}% auto-discount.`;
                    } else {
                        this.discountPreview = '';
                    }
                },

                openCreateModal() {
                    this.createListingId = '';
                    this.createStock = '';
                    this.discountPreview = '';
                    this.showCreate = true;
                },

                openDateDetail(day, label) {
                    this.selectedDay = day;
                    this.selectedDateLabel = label;
                    this.showDateDetail = true;
                },

                openEditModal(id, listingId, date, stock) {
                    this.showDateDetail = false;
                    this.editAction = `{{ url('farmer/harvest-schedules') }}/${id}`;
                    this.editListingId = String(listingId);
                    this.editDate = date;
                    this.editStock = stock;
                    setTimeout(() => { this.showEdit = true; }, 150);
                },

                openDeleteModal(id) {
                    this.showDateDetail = false;
                    this.deleteAction = `{{ url('farmer/harvest-schedules') }}/${id}`;
                    setTimeout(() => { this.showDelete = true; }, 150);
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
