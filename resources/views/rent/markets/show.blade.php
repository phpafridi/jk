<x-app-layout>
    <x-slot name="header">{{ $rentMarket->name }}</x-slot>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('rent.markets.index') }}" class="hover:text-emerald-600">Rent Markets</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">{{ $rentMarket->name }}</span>
    </nav>

    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"><i class="fas fa-key text-white" style="font-size:200px;position:absolute;right:-20px;bottom:-40px;"></i></div>
        <div class="relative">
            <h2 class="text-2xl font-bold">{{ $rentMarket->name }}</h2>
            @if($rentMarket->location)<p class="text-emerald-200 text-sm mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $rentMarket->location }}</p>@endif
            @if($rentMarket->description)<p class="text-emerald-100 text-sm mt-1">{{ $rentMarket->description }}</p>@endif
            <div class="flex gap-4 mt-4">
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-xs text-emerald-200">Total Shops</p>
                    <p class="text-xl font-bold">{{ $shops->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mb-5">
        <h3 class="font-semibold text-slate-800 flex items-center gap-2"><i class="fas fa-store-alt text-emerald-500"></i> Shops — click a shop to manage rent & files</h3>
        @can('manage rent')
        <button onclick="document.getElementById('modal-add-shop').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> Add Shop
        </button>
        @endcan
    </div>

    @if($shops->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <i class="fas fa-store-slash text-slate-300 text-5xl mb-3"></i>
        <p class="text-slate-500">No shops yet. Add your first shop above.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($shops as $shop)
        <a href="{{ route('rent.shops.show', $shop) }}"
           class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all group overflow-hidden block">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-key text-emerald-600 text-sm"></i>
                    </div>
                    <span class="font-semibold text-slate-800">Shop # {{ $shop->shop_number }}</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ $shop->status === 'rented' ? 'bg-blue-100 text-blue-700' : ($shop->status === 'inactive' ? 'bg-slate-100 text-slate-500' : 'bg-green-100 text-green-700') }}">
                    {{ ucfirst($shop->status) }}
                </span>
            </div>
            <div class="p-4">
                @if($shop->tenant_name)
                <p class="text-sm font-medium text-slate-700"><i class="fas fa-user text-emerald-400 mr-1"></i>{{ $shop->tenant_name }}</p>
                @endif
                @if($shop->rent_amount)
                <p class="text-sm text-slate-600 mt-1"><i class="fas fa-coins text-emerald-400 mr-1"></i>Rs {{ number_format($shop->rent_amount, 0) }}/month</p>
                @endif
                @if($shop->notes)
                <p class="text-xs text-slate-400 mt-1">{{ $shop->notes }}</p>
                @endif
            </div>
            <div class="px-4 py-2 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <div class="flex gap-2" onclick="event.preventDefault()">
                    @can('manage rent')
                    <button onclick="openEditShop({{ $shop->id }},'{{ addslashes($shop->shop_number) }}','{{ $shop->status }}',{{ $shop->rent_amount ?? 0 }},'{{ addslashes($shop->tenant_name ?? '') }}','{{ addslashes($shop->tenant_phone ?? '') }}','{{ addslashes($shop->tenant_cnic ?? '') }}','{{ addslashes($shop->notes ?? '') }}')"
                            class="py-1.5 px-3 text-slate-600 hover:bg-slate-100 rounded-lg text-xs font-medium">
                        <i class="fas fa-pen mr-1"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('rent.shops.destroy', $shop) }}" onsubmit="return confirm('Delete shop?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash text-xs"></i></button>
                    </form>
                    @endcan
                </div>
                <span class="text-xs text-emerald-600 font-medium group-hover:translate-x-0.5 transition-transform">
                    View Details <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </span>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-5">{{ $shops->links() }}</div>
    @endif

    @can('manage rent')
    {{-- Add Shop Modal --}}
    <div id="modal-add-shop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus-circle text-emerald-500 mr-2"></i>Add Shop</h3>
                <button onclick="document.getElementById('modal-add-shop').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="{{ route('rent.shops.store', $rentMarket) }}" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Shop Number *</label>
                        <input type="text" name="shop_number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g. R-01">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Rent (Rs)</label>
                    <input type="number" name="rent_amount" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="0">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tenant Name</label>
                        <input type="text" name="tenant_name" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tenant Phone</label>
                        <input type="text" name="tenant_phone" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tenant CNIC</label>
                    <input type="text" name="tenant_cnic" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="00000-0000000-0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-shop').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Add Shop</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Shop Modal --}}
    <div id="modal-edit-shop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-pen text-emerald-500 mr-2"></i>Edit Shop</h3>
                <button onclick="document.getElementById('modal-edit-shop').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" id="edit-shop-form" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Shop Number *</label>
                        <input type="text" name="shop_number" id="edit-shop-number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" id="edit-shop-status" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Rent (Rs)</label>
                    <input type="number" name="rent_amount" id="edit-shop-rent" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tenant Name</label>
                        <input type="text" name="tenant_name" id="edit-shop-tenant" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tenant Phone</label>
                        <input type="text" name="tenant_phone" id="edit-shop-phone" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tenant CNIC</label>
                    <input type="text" name="tenant_cnic" id="edit-shop-cnic" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" id="edit-shop-notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-shop').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Update</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    function openEditShop(id, number, status, rent, tenant, phone, cnic, notes) {
        document.getElementById('edit-shop-number').value  = number;
        document.getElementById('edit-shop-status').value  = status;
        document.getElementById('edit-shop-rent').value    = rent;
        document.getElementById('edit-shop-tenant').value  = tenant;
        document.getElementById('edit-shop-phone').value   = phone;
        document.getElementById('edit-shop-cnic').value    = cnic;
        document.getElementById('edit-shop-notes').value   = notes;
        document.getElementById('edit-shop-form').action   = '/rent-shops/' + id;
        document.getElementById('modal-edit-shop').classList.remove('hidden');
    }
    </script>
</x-app-layout>
