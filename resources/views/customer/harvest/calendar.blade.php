<x-app-layout>
    @php $title = 'Harvest Calendar'; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="customerCalendar()">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Harvest Calendar 🌾</h1>
                <p class="text-gray-500 mt-1">See what's being harvested soon — plan your fresh produce shopping ahead.</p>
            </div>

            {{-- Farmer Filter --}}
            @if($farmers->count() > 0)
                <form method="GET" action="{{ route('customer.harvest-calendar.index') }}" class="flex items-center gap-2 flex-shrink-0">
                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                    <input type="hidden" name="year" value="{{ $currentYear }}">
                    <select name="farmer" onchange="this.form.submit()"
                        class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-200 transition-all bg-white">
                        <option value="">All Farmers</option>
                        @foreach($farmers as $farmer)
                            <option value="{{ $farmer->user_id }}" {{ $farmerFilter == $farmer->user_id ? 'selected' : '' }}>
                                {{ $farmer->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        {{-- Month Navigation --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 flex items-center justify-between">
            <a href="{{ route('customer.harvest-calendar.index', array_filter(['month' => $prevMonth->month, 'year' => $prevMonth->year, 'farmer' => $farmerFilter])) }}"
               class="p-2 rounded-xl hover:bg-gray-100 transition-colors text-gray-600 hover:text-green-700 flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span class="hidden sm:inline text-sm font-medium">{{ $prevMonth->format('M') }}</span>
            </a>
            <div class="text-center">
                <h2 class="text-lg font-bold text-gray-900">{{ $currentDate->format('F Y') }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $schedules->flatten()->count() }} harvest{{ $schedules->flatten()->count() === 1 ? '' : 's' }} this month</p>
            </div>
            <a href="{{ route('customer.harvest-calendar.index', array_filter(['month' => $nextMonth->month, 'year' => $nextMonth->year, 'farmer' => $farmerFilter])) }}"
               class="p-2 rounded-xl hover:bg-gray-100 transition-colors text-gray-600 hover:text-green-700 flex items-center gap-1">
                <span class="hidden sm:inline text-sm font-medium">{{ $nextMonth->format('M') }}</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 mb-4 text-xs text-gray-500">
            <div class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm bg-emerald-100 border border-emerald-200"></span>
                Upcoming harvest
            </div>
            <div class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm bg-gray-100 border border-gray-200"></span>
                Past / no harvest
            </div>
            <div class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm ring-2 ring-green-400 bg-green-50"></span>
                Today
            </div>
        </div>

        {{-- Calendar Grid --}}
        @php
            $startOfMonth  = $currentDate->copy()->startOfMonth();
            $endOfMonth    = $currentDate->copy()->endOfMonth();
            $startDay      = $startOfMonth->dayOfWeek; // 0 = Sunday
            $daysInMonth   = $endOfMonth->day;
            $today         = now()->format('Y-m-d');
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
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
                        $dateStr      = $currentDate->copy()->day($day)->format('Y-m-d');
                        $isPast       = $dateStr < $today;
                        $isToday      = $dateStr === $today;
                        $daySchedules = $schedules->get($day, collect());
                        $hasSchedules = $daySchedules->count() > 0;
                    @endphp
                    <div dusk="day-cell-{{ $day }}" class="min-h-[100px] sm:min-h-[120px] border-b border-r border-gray-50 p-1.5 sm:p-2 relative transition-colors duration-150
                        {{ $isPast ? 'bg-gray-50/40' : ($hasSchedules ? 'hover:bg-emerald-50/40 cursor-pointer' : 'hover:bg-gray-50/60') }}
                        {{ $isToday ? 'ring-2 ring-inset ring-green-400/50 bg-green-50/20' : '' }}"
                        @if($hasSchedules)
                            @click="openDateDetail({{ $day }}, '{{ $currentDate->copy()->day($day)->format('M d, Y') }}')"
                        @endif>

                        {{-- Date Number --}}
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs sm:text-sm font-semibold
                                {{ $isPast ? 'text-gray-300' : ($isToday ? 'text-green-700' : 'text-gray-700') }}">
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
                                {{ Str::limit($schedule->listing->title, 14) }}
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
            <div class="text-center py-16 mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="text-5xl mb-3">🌱</div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">No harvests scheduled</h3>
                <p class="text-gray-500 text-sm max-w-sm mx-auto">
                    {{ $farmerFilter ? 'This farmer has no upcoming harvests this month.' : 'No farmers have scheduled harvests for ' . $currentDate->format('F Y') . '.' }}
                </p>
                @if($farmerFilter)
                    <a href="{{ route('customer.harvest-calendar.index', ['month' => $currentMonth, 'year' => $currentYear]) }}"
                       class="inline-block mt-4 px-5 py-2 text-sm font-semibold text-green-700 border border-green-200 rounded-full hover:bg-green-50 transition-colors">
                        View All Farmers
                    </a>
                @endif
            </div>
        @endif

        {{-- =============================== --}}
        {{-- DATE DETAIL MODAL (read-only)   --}}
        {{-- =============================== --}}
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
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Harvests on <span x-text="selectedDateLabel" class="text-green-700"></span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Click a listing to view and add to cart</p>
                        </div>
                        <button @click="showDateDetail = false" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Schedule Cards --}}
                    <div class="space-y-3">
                        <template x-for="s in (schedulesData[selectedDay] || [])" :key="s.id">
                            <div class="p-4 rounded-xl border transition-shadow"
                                 :class="s.isPast ? 'bg-gray-50 border-gray-100' : 'bg-gradient-to-r from-emerald-50 to-green-50 border-emerald-100 hover:shadow-sm'">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="font-bold text-gray-900 text-sm" x-text="s.title"></p>
                                            <template x-if="s.isPast">
                                                <span class="text-[10px] font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full">Past</span>
                                            </template>
                                        </div>
                                        <p class="text-xs text-gray-500 mb-2" x-text="'🧑‍🌾 ' + s.farmerName + (s.produce ? ' · ' + s.produce : '')"></p>
                                        <div class="flex items-center flex-wrap gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold"
                                                  :class="s.isPast ? 'bg-gray-200 text-gray-500' : 'bg-emerald-100 text-emerald-800'">
                                                <span x-text="'📦 ' + s.stock + ' ' + s.unit + ' available'"></span>
                                            </span>
                                            <span class="text-xs font-semibold text-green-800" x-text="'Rp ' + Number(s.price).toLocaleString('id-ID') + ' / ' + s.unit"></span>
                                        </div>
                                    </div>
                                    <template x-if="!s.isPast">
                                        <a :href="s.listingUrl"
                                           class="flex-shrink-0 px-3 py-2 bg-green-800 text-white text-xs font-semibold rounded-xl hover:bg-green-900 transition-colors whitespace-nowrap">
                                            View Listing →
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="(schedulesData[selectedDay] || []).length === 0">
                            <p class="text-gray-400 text-sm text-center py-6">No harvests on this date.</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Alpine.js Component --}}
    <script>
        function customerCalendar() {
            return {
                showDateDetail: false,
                selectedDay: null,
                selectedDateLabel: '',

                // Schedule data indexed by day number
                schedulesData: (function () {
                    @php
                        $schedulesJson = $schedules->map(function ($daySchedules) {
                            return $daySchedules->map(function ($s) {
                                return [
                                    'id'           => $s->id,
                                    'listing_id'   => $s->listing->listing_id,
                                    'title'        => $s->listing->title,
                                    'farmerName'   => $s->listing->farmer->name ?? 'Unknown',
                                    'produce'      => $s->listing->produce->category ?? '',
                                    'date'         => $s->availability_date->format('Y-m-d'),
                                    'dateLabel'    => $s->availability_date->format('M d, Y'),
                                    'stock'        => $s->estimated_stock,
                                    'unit'         => $s->listing->unit ?? 'kg',
                                    'price'        => $s->listing->price,
                                    'listingUrl'   => route('marketplace.show', $s->listing->listing_id),
                                    'isPast'       => $s->isPast(),
                                ];
                            })->values();
                        });
                    @endphp
                    return {!! json_encode($schedulesJson) !!};
                })(),

                openDateDetail(day, label) {
                    this.selectedDay = day;
                    this.selectedDateLabel = label;
                    this.showDateDetail = true;
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
