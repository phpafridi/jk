<x-app-layout>
    <x-slot name="header">Instalment Markets</x-slot>

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <p class="text-slate-500 text-sm">{{ $markets->total() }} markets found</p>
        @can('manage markets')
        <button onclick="document.getElementById('modal-add-market').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm hover:shadow-md transition-all">
            <i class="fas fa-plus"></i> Add Market
        </button>
        @endcan
    </div>

    <!-- Markets Grid -->
    @if($markets->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
            <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-store text-indigo-400 text-2xl"></i>
            </div>
            <p class="text-slate-600 font-medium">No markets yet</p>
            <p class="text-slate-400 text-sm mt-1">Create your first market to start adding shops.</p>
            @can('manage markets')
            <button onclick="document.getElementById('modal-add-market').classList.remove('hidden')"
                    class="mt-4 px-6 py-2.5 btn-primary text-white rounded-xl text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Create Market
            </button>
            @endcan
        </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($markets as $market)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-all group">
            <!-- Market Image -->
            <div class="h-36 bg-gradient-to-br from-indigo-500 to-purple-600 relative overflow-hidden">
                @if($market->image)
                    <img src="{{ asset('storage/' . $market->image) }}" alt="{{ $market->name }}" class="w-full h-full object-cover opacity-80">
                @else
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-store text-white/40 text-5xl"></i>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                <div class="absolute bottom-3 left-3">
                    <span class="bg-white/20 backdrop-blur text-white text-xs px-2 py-1 rounded-lg font-medium">
                        {{ $market->shops_count }} shops
                    </span>
                </div>
            </div>

            <!-- Info -->
            <div class="p-4">
                <h3 class="font-semibold text-slate-800 truncate">{{ $market->name }}</h3>
                @if($market->location)
                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                    <i class="fas fa-map-marker-alt text-indigo-400"></i> {{ $market->location }}
                </p>
                @endif
                @if($market->description)
                <p class="text-xs text-slate-400 mt-2 line-clamp-2">{{ $market->description }}</p>
                @endif

                <div class="flex items-center gap-2 mt-4">
                    <a href="{{ route('markets.show', $market) }}"
                       class="flex-1 text-center py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-eye mr-1"></i> View Shops
                    </a>
                    @can('manage markets')
                    <button onclick="openEditMarket({{ $market->id }}, '{{ addslashes($market->name) }}', '{{ addslashes($market->location) }}', '{{ addslashes($market->description) }}')"
                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors">
                        <i class="fas fa-pen"></i>
                    </button>
                    <form method="POST" action="{{ route('markets.destroy', $market) }}" onsubmit="return confirm('Delete this market?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $markets->links() }}</div>
    @endif

    <!-- Add Market Modal -->
    @can('manage markets')
    <div id="modal-add-market" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus-circle text-indigo-500 mr-2"></i>Add New Market</h3>
                <button onclick="document.getElementById('modal-add-market').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('markets.store') }}" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market Name *</label>
                    <input type="text" name="name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="e.g. Al-Hafeez Market">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="City / Area">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Optional notes..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-market').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Create Market</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Market Modal -->
    <div id="modal-edit-market" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-pen text-indigo-500 mr-2"></i>Edit Market</h3>
                <button onclick="document.getElementById('modal-edit-market').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="edit-market-form" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market Name *</label>
                    <input type="text" name="name" id="edit-market-name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" id="edit-market-location" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" id="edit-market-description" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Replace Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-market').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    function openEditMarket(id, name, location, description) {
        document.getElementById('edit-market-name').value = name;
        document.getElementById('edit-market-location').value = location;
        document.getElementById('edit-market-description').value = description;
        document.getElementById('edit-market-form').action = '/markets/' + id;
        document.getElementById('modal-edit-market').classList.remove('hidden');
    }
    </script>
</x-app-layout>
