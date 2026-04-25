<x-app-layout>
    @php $title = 'Edit Listing'; @endphp

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Back Link --}}
        <a href="{{ route('farmer.listings.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-green-700 transition-colors mb-6 group">
            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to My Listings
        </a>

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Listing ✏️</h1>

        {{-- Form Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
            <form method="POST" action="{{ route('farmer.listings.update', $listing) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                



                {{-- Form Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Title (full width) --}}
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $listing->title) }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all"
                               placeholder="e.g. Wortel Segar Organik" required maxlength="100">
                        @error('title')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Produce Category --}}
                    <div>
                        <label for="produce_produce_id" class="block text-sm font-semibold text-gray-700 mb-2">Produce Category</label>
                        <div class="relative">
                            <select name="produce_produce_id" id="produce_produce_id"
                                    class="appearance-none w-full px-4 py-3 pr-10 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all cursor-pointer"
                                    required>
                                <option value="">Select produce</option>
                                @foreach($produces as $produce)
                                    <option value="{{ $produce->produce_id }}"
                                        {{ old('produce_produce_id', $listing->produce_produce_id) == $produce->produce_id ? 'selected' : '' }}>
                                        {{ $produce->emoji }} {{ $produce->name }} — {{ $produce->category }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        @error('produce_produce_id')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rp</span>
                            <input type="number" name="price" id="price" value="{{ old('price', $listing->price) }}"
                                   class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all"
                                   placeholder="15000" required min="0" step="100">
                        </div>
                        @error('price')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $listing->quantity) }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all"
                               placeholder="50" required min="1">
                        @error('quantity')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Unit --}}
                    <div>
                        <label for="unit" class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit', $listing->unit) }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all"
                               placeholder="e.g. kg, ikat, buah" required maxlength="20">
                        @error('unit')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <div class="relative">
                            <select name="status" id="status"
                                    class="appearance-none w-full px-4 py-3 pr-10 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all cursor-pointer"
                                    required>
                                <option value="active" {{ old('status', $listing->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $listing->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="sold_out" {{ old('status', $listing->status) === 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                            </select>
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        @error('status')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Availability Date --}}
                    <div>
                        <label for="availability_date" class="block text-sm font-semibold text-gray-700 mb-2">Availability Date</label>
                        <input type="date" name="availability_date" id="availability_date"
                               value="{{ old('availability_date', $listing->availability_date ? $listing->availability_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all">
                        @error('availability_date')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description (full width) --}}
                    <div class="md:col-span-2">
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea name="content" id="content" rows="4"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 transition-all resize-none"
                                  placeholder="Describe your produce — freshness, origin, special qualities...">{{ old('content', $listing->content) }}</textarea>
                        @error('content')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-8 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-8 py-3 bg-green-800 text-white font-semibold rounded-full hover:bg-green-900 transition-all duration-200 shadow-lg shadow-green-200/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
