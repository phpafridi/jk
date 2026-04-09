<x-app-layout>
    <x-slot name="header">{{ $sellMarket->name }}</x-slot>
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('sell.markets.index') }}" class="hover:text-amber-600">Sell/Buy Markets</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">{{ $sellMarket->name }}</span>
    </nav>
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"><i class="fas fa-exchange-alt text-white" style="font-size:200px;position:absolute;right:-20px;bottom:-40px;"></i></div>
        <div class="relative">
            <h2 class="text-2xl font-bold">{{ $sellMarket->name }}</h2>
            @if($sellMarket->location)<p class="text-amber-200 text-sm mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $sellMarket->location }}</p>@endif
            <div class="flex gap-4 mt-4">
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-2"><p class="text-xs text-amber-200">Total Shops/Plots</p><p class="text-xl font-bold">{{ $shops->total() }}</p></div>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-between mb-5">
        <h3 class="font-semibold text-slate-800"><i class="fas fa-store text-amber-500 mr-2"></i>Shops &amp; Plots</h3>
        <div class="flex gap-3">
            <a href="{{ route('sell.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-medium"><i class="fas fa-list mr-1"></i> Entries</a>
            @can('manage sell purchase')
            <button onclick="document.getElementById('modal-add-shop').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm"><i class="fas fa-plus"></i> Add</button>
            @endcan
        </div>
    </div>
    @if($shops->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center"><i class="fas fa-store-slash text-slate-300 text-5xl mb-3"></i><p class="text-slate-500">No shops/plots yet</p></div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($shops as $shop)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg {{ $shop->type==='plot' ? 'bg-green-100' : 'bg-amber-100' }} flex items-center justify-center">
                        <i class="fas {{ $shop->type==='plot' ? 'fa-map text-green-600' : 'fa-store text-amber-600' }} text-sm"></i>
                    </div>
                    <div>
                        <span class="font-semibold text-slate-800">{{ ucfirst($shop->type) }} # {{ $shop->shop_number }}</span>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $shop->status==='sold' ? 'bg-red-100 text-red-700' : ($shop->status==='inactive' ? 'bg-slate-100 text-slate-500' : 'bg-green-100 text-green-700') }}">{{ ucfirst($shop->status) }}</span>
            </div>
            <div class="p-4">
                @if($shop->area_sqft)<p class="text-sm text-slate-600"><i class="fas fa-ruler-combined text-amber-400 mr-1"></i>{{ number_format($shop->area_sqft,0) }} sqft</p>@endif
                @if($shop->notes)<p class="text-xs text-slate-400 mt-1">{{ $shop->notes }}</p>@endif
            </div>
            @can('manage sell purchase')
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex gap-2">
                <button onclick="openEditShop({{ $shop->id }},'{{ addslashes($shop->shop_number) }}','{{ $shop->type }}','{{ $shop->status }}',{{ $shop->area_sqft ?? 0 }},'{{ addslashes($shop->notes ?? '') }}')" class="flex-1 py-2 text-center text-slate-600 hover:bg-slate-100 rounded-xl text-xs font-medium"><i class="fas fa-pen mr-1"></i> Edit</button>
                <form method="POST" action="{{ route('sell.shops.destroy', $shop) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl"><i class="fas fa-trash text-xs"></i></button>
                </form>
            </div>
            @endcan
        </div>
        @endforeach
    </div>
    <div class="mt-5">{{ $shops->links() }}</div>
    @endif

    @can('manage sell purchase')
    <div id="modal-add-shop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus-circle text-amber-500 mr-2"></i>Add Shop / Plot</h3>
                <button onclick="document.getElementById('modal-add-shop').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="{{ route('sell.shops.store', $sellMarket) }}" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Number *</label><input type="text" name="shop_number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="e.g. S-01"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Type *</label>
                        <select name="type" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="shop">Shop</option><option value="plot">Plot</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="available">Available</option><option value="sold">Sold</option><option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Area (sqft)</label><input type="number" name="area_sqft" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="0"></div>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-shop').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Add</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-edit-shop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-pen text-amber-500 mr-2"></i>Edit</h3>
                <button onclick="document.getElementById('modal-edit-shop').classList.add('hidden')" class="text-slate-400 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" id="edit-shop-form" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Number *</label><input type="text" name="shop_number" id="es-number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                        <select name="type" id="es-type" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="shop">Shop</option><option value="plot">Plot</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" id="es-status" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="available">Available</option><option value="sold">Sold</option><option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Area (sqft)</label><input type="number" name="area_sqft" id="es-area" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></div>
                </div>
                <div><label class="block text-sm font-medium text-slate-700 mb-1">Notes</label><textarea name="notes" id="es-notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-shop').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Update</button>
                </div>
            </form>
        </div>
    </div>
    @endcan
    <script>
    function openEditShop(id,number,type,status,area,notes){
        document.getElementById('es-number').value=number;
        document.getElementById('es-type').value=type;
        document.getElementById('es-status').value=status;
        document.getElementById('es-area').value=area;
        document.getElementById('es-notes').value=notes;
        document.getElementById('edit-shop-form').action='/sell-shops/'+id;
        document.getElementById('modal-edit-shop').classList.remove('hidden');
    }
    </script>
</x-app-layout>
