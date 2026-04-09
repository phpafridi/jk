<x-app-layout>
    <x-slot name="header">Rent Markets</x-slot>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <p class="text-slate-500 text-sm">{{ $markets->total() }} market(s)</p>
        @can('manage rent')
        <button onclick="document.getElementById('modal-add-market').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> Add Market
        </button>
        @endcan
    </div>

    @if($markets->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fas fa-store-slash text-slate-300 text-5xl mb-3"></i>
        <p class="text-slate-400 mb-4">No rent markets yet</p>
        @can('manage rent')
        <button onclick="document.getElementById('modal-add-market').classList.remove('hidden')"
                class="px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium">
            <i class="fas fa-plus mr-2"></i>Create First Market
        </button>
        @endcan
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($markets as $market)
        <a href="{{ route('rent.markets.show', $market) }}"
           class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all group overflow-hidden block">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 px-5 pt-5 pb-8 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10"><i class="fas fa-key text-white" style="font-size:150px;position:absolute;right:-20px;bottom:-30px;"></i></div>
                <h3 class="text-white font-bold text-lg relative">{{ $market->name }}</h3>
                @if($market->location)
                <p class="text-emerald-200 text-xs mt-1 relative"><i class="fas fa-map-marker-alt mr-1"></i>{{ $market->location }}</p>
                @endif
            </div>
            <div class="px-5 py-4 flex items-center justify-between -mt-4 relative z-10">
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-4 py-2">
                    <p class="text-xs text-slate-400">Shops</p>
                    <p class="text-xl font-bold text-slate-800">{{ $market->shops_count }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @can('manage rent')
                    <button onclick="event.preventDefault();openEditMarket({{ $market->id }},'{{ addslashes($market->name) }}','{{ addslashes($market->location ?? '') }}','{{ addslashes($market->description ?? '') }}')"
                            class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                        <i class="fas fa-pen text-sm"></i>
                    </button>
                    <form method="POST" action="{{ route('rent.markets.destroy', $market) }}" onsubmit="return confirm('Delete this market and all its shops?')">
                        @csrf @method('DELETE')
                        <button onclick="event.stopPropagation()" type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </form>
                    @endcan
                    <span class="text-emerald-500 group-hover:translate-x-1 transition-transform">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-5">{{ $markets->links() }}</div>
    @endif

    @can('manage rent')
    {{-- Add Market Modal --}}
    <div id="modal-add-market" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus-circle text-emerald-500 mr-2"></i>Add Rent Market</h3>
                <button onclick="document.getElementById('modal-add-market').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="{{ route('rent.markets.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market Name *</label>
                    <input type="text" name="name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g. Plaza A">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="City / Address">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-market').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Market Modal --}}
    <div id="modal-edit-market" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-pen text-emerald-500 mr-2"></i>Edit Market</h3>
                <button onclick="document.getElementById('modal-edit-market').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" id="edit-market-form" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market Name *</label>
                    <input type="text" name="name" id="edit-market-name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" id="edit-market-location" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" id="edit-market-desc" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-market').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Update</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    function openEditMarket(id, name, location, desc) {
        document.getElementById('edit-market-name').value     = name;
        document.getElementById('edit-market-location').value = location;
        document.getElementById('edit-market-desc').value     = desc;
        document.getElementById('edit-market-form').action    = '/rent-markets/' + id;
        document.getElementById('modal-edit-market').classList.remove('hidden');
    }
    </script>
</x-app-layout>
