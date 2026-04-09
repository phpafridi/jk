<x-app-layout>
    <x-slot name="header">{{ $market->name }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('markets.index') }}" class="hover:text-indigo-600 transition-colors">Markets</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">{{ $market->name }}</span>
    </nav>

    <!-- Market Header -->
    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <i class="fas fa-store text-white" style="font-size: 200px; position: absolute; right: -20px; bottom: -40px;"></i>
        </div>
        <div class="relative">
            <h2 class="text-2xl font-bold">{{ $market->name }}</h2>
            @if($market->location)
            <p class="text-indigo-200 text-sm mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $market->location }}</p>
            @endif
            @if($market->description)
            <p class="text-indigo-100 text-sm mt-2">{{ $market->description }}</p>
            @endif
            <div class="flex flex-wrap gap-4 mt-4">
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-xs text-indigo-200">Total Shops</p>
                    <p class="text-xl font-bold">{{ $shops->total() }}</p>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-xs text-indigo-200">Added</p>
                    <p class="text-xl font-bold">{{ $market->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
            <i class="fas fa-store-alt text-indigo-500"></i> Shops in this Market
        </h3>
        @can('manage shops')
        <button onclick="document.getElementById('modal-add-shop').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm hover:shadow-md transition-all">
            <i class="fas fa-plus"></i> Add Shop
        </button>
        @endcan
    </div>

    <!-- Shops Grid -->
    @if($shops->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <i class="fas fa-store-slash text-slate-300 text-5xl mb-3"></i>
            <p class="text-slate-500 font-medium">No shops added yet</p>
            @can('manage shops')
            <button onclick="document.getElementById('modal-add-shop').classList.remove('hidden')"
                    class="mt-4 px-5 py-2 btn-primary text-white rounded-xl text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> Add First Shop
            </button>
            @endcan
        </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($shops as $shop)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden">
            <!-- Header -->
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-store text-indigo-600 text-sm"></i>
                    </div>
                    <span class="font-semibold text-slate-800">Shop # {{ $shop->shop_number }}</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ $shop->status === 'active' ? 'bg-green-100 text-green-700' : ($shop->status === 'sold' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600') }}">
                    {{ ucfirst($shop->status) }}
                </span>
            </div>

            <!-- Body -->
            <div class="p-4 space-y-2">
                @if($shop->owner)
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-user text-slate-400 w-4"></i>
                    <span class="text-slate-600">{{ $shop->owner->name }}</span>
                </div>
                @endif
                @if($shop->date_of_payment)
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-calendar text-slate-400 w-4"></i>
                    <span class="text-slate-600">{{ $shop->date_of_payment->format('d M Y') }}</span>
                </div>
                @endif
                @if($shop->total_amount > 0)
                <div class="mt-3 bg-slate-50 rounded-xl p-3">
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span>Paid</span><span>Total</span>
                    </div>
                    <div class="flex justify-between font-semibold text-sm">
                        <span class="text-green-600">Rs {{ number_format($shop->paid_amount, 0) }}</span>
                        <span class="text-slate-700">Rs {{ number_format($shop->total_amount, 0) }}</span>
                    </div>
                    <div class="mt-2 bg-slate-200 rounded-full h-1.5">
                        @php $pct = $shop->total_amount > 0 ? min(100, ($shop->paid_amount / $shop->total_amount) * 100) : 0; @endphp
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Rs {{ number_format($shop->total_amount - $shop->paid_amount, 0) }} remaining</p>
                </div>
                @endif
            </div>

            <!-- Footer Actions -->
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center gap-2">
                <a href="{{ route('shops.show', $shop) }}"
                   class="flex-1 text-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-medium transition-colors">
                    <i class="fas fa-book mr-1"></i> Open Ledger
                </a>
                @can('manage shops')
                <button onclick="openEditShop({{ $shop->id }}, '{{ addslashes($shop->shop_number) }}', {{ $shop->owner_id ?? 'null' }}, {{ $shop->total_amount }}, {{ $shop->paid_amount }}, '{{ $shop->status }}')"
                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors">
                    <i class="fas fa-pen text-xs"></i>
                </button>
                <form method="POST" action="{{ route('shops.destroy', $shop) }}" onsubmit="return confirm('Delete shop?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
                @endcan
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-5">{{ $shops->links() }}</div>
    @endif

    <!-- Add Shop Modal -->
    @can('manage shops')
    <div id="modal-add-shop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus-circle text-indigo-500 mr-2"></i>Add Shop to {{ $market->name }}</h3>
                <button onclick="document.getElementById('modal-add-shop').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('shops.store', $market) }}" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Shop Number *</label>
                        <input type="text" name="shop_number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. A-01">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type *</label>
                        <select name="type" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="instalment">Instalment</option>
                            <option value="rent">Rent</option>
                            <option value="sell">Sell</option>
                            <option value="purchase">Purchase</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Owner</label>
                    <select name="owner_id" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Select Owner —</option>
                        @foreach(\App\Models\Owner::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date of Payment</label>
                    <input type="date" name="date_of_payment" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Total Amount (Rs)</label>
                        <input type="number" name="total_amount" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Paid Amount (Rs)</label>
                        <input type="number" name="paid_amount" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Rent (Rs)</label>
                    <input type="number" name="rent_amount" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Optional">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-shop').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Add Shop</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Shop Modal -->
    <div id="modal-edit-shop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-pen text-indigo-500 mr-2"></i>Edit Shop</h3>
                <button onclick="document.getElementById('modal-edit-shop').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="edit-shop-form" method="POST" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Shop Number *</label>
                    <input type="text" name="shop_number" id="edit-shop-number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Owner</label>
                    <select name="owner_id" id="edit-shop-owner" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Select Owner —</option>
                        @foreach(\App\Models\Owner::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Total Amount</label>
                        <input type="number" name="total_amount" id="edit-shop-total" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Paid Amount</label>
                        <input type="number" name="paid_amount" id="edit-shop-paid" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="status" id="edit-shop-status" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="sold">Sold</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-shop').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    function openEditShop(id, shopNumber, ownerId, total, paid, status) {
        document.getElementById('edit-shop-number').value = shopNumber;
        document.getElementById('edit-shop-owner').value = ownerId || '';
        document.getElementById('edit-shop-total').value = total;
        document.getElementById('edit-shop-paid').value = paid;
        document.getElementById('edit-shop-status').value = status;
        document.getElementById('edit-shop-form').action = '/shops/' + id;
        document.getElementById('modal-edit-shop').classList.remove('hidden');
    }
    </script>
</x-app-layout>
