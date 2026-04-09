<x-app-layout>
    <x-slot name="header">Construction</x-slot>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search project..."
                   class="border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 w-44">
            <select name="market_id" class="border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                <option value="">All Markets</option>
                @foreach($markets as $market)
                <option value="{{ $market->id }}" {{ request('market_id') == $market->id ? 'selected' : '' }}>{{ $market->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-sm text-slate-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <div class="flex items-center gap-3">
            <div class="bg-rose-50 border border-rose-200 rounded-xl px-4 py-2 text-sm text-rose-700 font-semibold">
                <i class="fas fa-coins mr-1"></i> Grand Total: Rs {{ number_format($grandTotal, 0) }}
            </div>
            @can('manage construction')
            <button onclick="document.getElementById('modal-add-construction').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
                <i class="fas fa-plus"></i> New Project Item
            </button>
            @endcan
        </div>
    </div>

    @if($projects->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fas fa-hard-hat text-slate-300 text-5xl mb-3"></i>
        <p class="text-slate-400 mb-4">No construction projects yet</p>
        @can('manage construction')
        <button onclick="document.getElementById('modal-add-construction').classList.remove('hidden')"
                class="px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium">
            <i class="fas fa-plus mr-2"></i>Add First Item
        </button>
        @endcan
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($projects as $proj)
        <a href="{{ route('construction.show', urlencode($proj['project_name'])) }}"
           class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-rose-300 transition-all group overflow-hidden block">
            <div class="bg-gradient-to-br from-rose-500 to-orange-500 px-5 pt-5 pb-8 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10"><i class="fas fa-hard-hat text-white" style="font-size:140px;position:absolute;right:-20px;bottom:-30px;"></i></div>
                <h3 class="text-white font-bold text-lg relative leading-tight">{{ $proj['project_name'] }}</h3>
                @if($proj['market'])
                <p class="text-rose-200 text-xs mt-1 relative"><i class="fas fa-map-marker-alt mr-1"></i>{{ $proj['market']->name }}</p>
                @endif
            </div>
            <div class="px-5 py-4 -mt-4 relative z-10 flex items-end justify-between">
                <div class="flex gap-3">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-3 py-2">
                        <p class="text-xs text-slate-400">Items</p>
                        <p class="text-lg font-bold text-slate-800">{{ $proj['items_count'] }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-3 py-2">
                        <p class="text-xs text-slate-400">Total Spent</p>
                        <p class="text-base font-bold text-rose-600">Rs {{ number_format($proj['total'], 0) }}</p>
                    </div>
                </div>
                <span class="text-rose-500 group-hover:translate-x-1 transition-transform mb-2">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </div>
            @if($proj['last_date'])
            <div class="px-5 pb-3">
                <p class="text-xs text-slate-400"><i class="fas fa-clock mr-1"></i>Last entry: {{ \Carbon\Carbon::parse($proj['last_date'])->format('d M Y') }}</p>
            </div>
            @endif
        </a>
        @endforeach
    </div>
    @endif

    {{-- Add Construction Item Modal --}}
    @can('manage construction')
    <div id="modal-add-construction" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-hard-hat text-rose-500 mr-2"></i>Add Construction Item</h3>
                <button onclick="document.getElementById('modal-add-construction').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('construction.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market / Project Location</label>
                    <select name="market_id" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="">— Select Market —</option>
                        @foreach($markets as $market)
                        <option value="{{ $market->id }}">{{ $market->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Project Name *</label>
                    <input type="text" name="project_name" required list="project-suggestions"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500"
                           placeholder="e.g. Ground Floor Construction">
                    <datalist id="project-suggestions">
                        @foreach($projects as $proj)
                        <option value="{{ $proj['project_name'] }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Item Name *</label>
                    <input type="text" name="item_name" required
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500"
                           placeholder="e.g. Cement, Steel Bars, Labour">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Quantity *</label>
                        <input type="number" name="quantity" id="inp-qty" required min="0" step="0.01"
                               oninput="calcTotal()" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Unit *</label>
                        <select name="unit" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <option value="sqft">Sqft</option>
                            <option value="meters">Meters</option>
                            <option value="kg">Kg</option>
                            <option value="bags">Bags</option>
                            <option value="pieces">Pieces</option>
                            <option value="tons">Tons</option>
                            <option value="liters">Liters</option>
                            <option value="days">Days</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Measurement</label>
                        <input type="text" name="measurement" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500" placeholder="e.g. 10x20">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Unit Price (Rs) *</label>
                        <input type="number" name="unit_price" id="inp-price" required min="0" step="0.01"
                               oninput="calcTotal()" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Total (Rs) *</label>
                        <input type="number" name="total" id="inp-total" required min="0" step="0.01"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 bg-rose-50 font-semibold" placeholder="Auto-calculated">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date *</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-construction').classList.add('hidden')"
                            class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Save Item</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    function calcTotal() {
        const qty   = parseFloat(document.getElementById('inp-qty').value)   || 0;
        const price = parseFloat(document.getElementById('inp-price').value) || 0;
        document.getElementById('inp-total').value = (qty * price).toFixed(2);
    }
    </script>
</x-app-layout>
