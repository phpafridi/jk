<x-app-layout>
    <x-slot name="header">Sell / Buy Markets</x-slot>

    <div class="flex items-center justify-between mb-6">
        <p class="text-slate-500 text-sm">{{ $markets->total() }} markets</p>
        <div class="flex gap-3">
            <a href="{{ route('sell.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-medium">
                <i class="fas fa-list"></i> Sell/Buy Entries
            </a>
            @can('manage sell purchase')
            <button onclick="document.getElementById('modal-add-market').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
                <i class="fas fa-plus"></i> Add Market
            </button>
            @endcan
        </div>
    </div>

    @if($markets->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fas fa-exchange-alt text-slate-300 text-5xl mb-3"></i>
        <p class="text-slate-500 font-medium">No sell/buy markets yet</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($markets as $market)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden">
            <div class="h-28 bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center relative">
                <i class="fas fa-exchange-alt text-white/30 text-6xl"></i>
                <div class="absolute bottom-2 left-3">
                    <span class="bg-white/20 backdrop-blur text-white text-xs px-2 py-1 rounded-lg font-medium">{{ $market->shops_count }} shops</span>
                </div>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-slate-800 truncate">{{ $market->name }}</h3>
                @if($market->location)<p class="text-xs text-slate-500 mt-0.5"><i class="fas fa-map-marker-alt text-amber-400 mr-1"></i>{{ $market->location }}</p>@endif
                <div class="mt-3 space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span><i class="fas fa-store text-amber-400 mr-1"></i>{{ $market->shops_count }} shops</span>
                        @if(isset($market->total_entries))<span>{{ $market->total_entries }} entries</span>@endif
                    </div>
                    @if(isset($market->total_value) && $market->total_value > 0)
                    <div class="flex justify-between">
                        <span class="text-emerald-600 font-medium">Paid: Rs {{ number_format($market->total_paid, 0) }}</span>
                        @if($market->total_pending > 0)
                        <span class="text-amber-600 font-medium">Due: Rs {{ number_format($market->total_pending, 0) }}</span>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('sell.markets.show', $market) }}" class="flex-1 text-center py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-eye mr-1"></i> View Shops
                    </a>
                    @can('manage sell purchase')
                    <button onclick="openEditMarket({{ $market->id }},'{{ addslashes($market->name) }}','{{ addslashes($market->location) }}')"
                            class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl"><i class="fas fa-pen text-xs"></i></button>
                    <form method="POST" action="{{ route('sell.markets.destroy', $market) }}" onsubmit="return confirm('Delete market?')">
                        @csrf @method('DELETE')
                        <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl"><i class="fas fa-trash text-xs"></i></button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-5">{{ $markets->links() }}</div>
    @endif

    @can('manage sell purchase')
    <div id="modal-add-market" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus-circle text-amber-500 mr-2"></i>Add Market</h3>
                <button onclick="document.getElementById('modal-add-market').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="{{ route('sell.markets.store') }}" class="p-5 space-y-4">
                @csrf
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Market Name *</label><input type="text" name="name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Location</label><input type="text" name="location" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-market').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Create</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-edit-market" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-pen text-amber-500 mr-2"></i>Edit Market</h3>
                <button onclick="document.getElementById('modal-edit-market').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" id="edit-market-form" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Name *</label><input type="text" name="name" id="edit-name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Location</label><input type="text" name="location" id="edit-location" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-market').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Update</button>
                </div>
            </form>
        </div>
    </div>
    @endcan
    <script>
    function openEditMarket(id,name,location){
        document.getElementById('edit-name').value=name;
        document.getElementById('edit-location').value=location;
        document.getElementById('edit-market-form').action='/sell-markets/'+id;
        document.getElementById('modal-edit-market').classList.remove('hidden');
    }
    </script>
</x-app-layout>
