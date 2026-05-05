<x-app-layout>
    @php $title = 'Add Listing'; @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <a href="{{ route('farmer.listings.index') }}" class="text-green-700 hover:text-green-800 text-sm flex items-center gap-1 mb-6">← Back to Listings</a>

        <div class="bg-white rounded-2xl border border-gray-100 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Create New Listing</h1>
            <p class="text-gray-500 text-sm mb-8">Share your fresh produce with customers.</p>

            <form method="POST" action="{{ route('farmer.listings.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Images -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Produce Photos</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-green-300 transition-colors">
                        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden" id="imageUpload"
                            onchange="previewImages(this)">
                        <label for="imageUpload" class="cursor-pointer">
                            <div class="text-4xl mb-2">📸</div>
                            <p class="text-sm font-medium text-gray-600">Click to upload photos</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP only · Max 2MB each</p>
                        </label>
                        <div id="imagePreview" class="flex gap-3 mt-4 flex-wrap justify-center"></div>
                    </div>
                    @error('images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Produce Select -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Produce Type</label>
                        <select name="produce_produce_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                            <option value="">Select produce...</option>
                            @foreach($produces as $p)
                                <option value="{{ $p->produce_id }}" {{ old('produce_produce_id') == $p->produce_id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->category }})</option>
                            @endforeach
                        </select>
                        @error('produce_produce_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Listing Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Fresh Organic Tomatoes"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Price (Rp)</label>
                        <input type="number" name="price" value="{{ old('price') }}" required min="0" placeholder="9000"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" required min="1" placeholder="50"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                        @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Unit -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Unit</label>
                        <select name="unit" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                            <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                            <option value="g" {{ old('unit') === 'g' ? 'selected' : '' }}>Gram (g)</option>
                            <option value="pcs" {{ old('unit') === 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                            <option value="bundle" {{ old('unit') === 'bundle' ? 'selected' : '' }}>Bundle</option>
                        </select>
                    </div>
                </div>

                <!-- Availability Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Available From</label>
                    <input type="date" name="availability_date" value="{{ old('availability_date', date('Y-m-d')) }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="content" rows="4" placeholder="Describe your produce — farming method, freshness, taste..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm resize-none">{{ old('content') }}</textarea>
                </div>

                <button type="submit" class="w-full py-4 bg-green-800 text-white font-bold rounded-full hover:bg-green-900 transition-all shadow-lg shadow-green-200/50 text-lg">
                    Publish Listing
                </button>
            </form>
        </div>
    </div>

    <script>
    function previewImages(input) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        for (const file of input.files) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-20 h-20 object-cover rounded-xl border-2 border-green-200';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }
    </script>
</x-app-layout>
