<x-app-layout>
    <x-slot name="header">{{ $projectName }}</x-slot>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('construction.index') }}" class="hover:text-rose-600">Construction</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">{{ $projectName }}</span>
    </nav>

    {{-- Project Summary --}}
    <div class="bg-gradient-to-br from-rose-500 to-orange-500 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"><i class="fas fa-hard-hat text-white" style="font-size:200px;position:absolute;right:-20px;bottom:-40px;"></i></div>
        <div class="relative flex items-start justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold">{{ $projectName }}</h2>
                @if($market)
                <p class="text-rose-200 text-sm mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $market->name }}</p>
                @endif
            </div>
            <div class="flex gap-3">
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-rose-200">Total Items</p>
                    <p class="text-xl font-bold">{{ $items->total() }}</p>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-rose-200">Total Spent</p>
                    <p class="text-xl font-bold">Rs {{ number_format($total, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mb-5">
        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
            <i class="fas fa-list text-rose-500"></i> Payment Items
        </h3>
        @can('manage construction')
        <button onclick="document.getElementById('modal-add-item').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> Add Item
        </button>
        @endcan
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($items->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-box-open text-slate-300 text-5xl mb-3"></i>
            <p class="text-slate-400">No items in this project yet</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Item</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Qty</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Measurement</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit Price</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="text-right px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($items as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800">{{ $item->item_name }}</p>
                            @if($item->notes)<p class="text-xs text-slate-400 mt-0.5">{{ $item->notes }}</p>@endif
                        </td>
                        <td class="px-5 py-3 text-right text-slate-600">{{ number_format($item->quantity, 2) }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $item->unit }}</td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $item->measurement ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-slate-600">Rs {{ number_format($item->unit_price, 0) }}</td>
                        <td class="px-5 py-3 text-right font-bold text-rose-600">Rs {{ number_format($item->total, 0) }}</td>
                        <td class="px-5 py-3 text-slate-500 whitespace-nowrap text-xs">{{ $item->date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            @can('manage construction')
                            <form method="POST" action="{{ route('construction.destroy', $item) }}" onsubmit="return confirm('Delete item?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="5" class="px-5 py-3 font-semibold text-slate-700 text-sm">Project Total</td>
                        <td class="px-5 py-3 text-right font-bold text-rose-600 text-base">Rs {{ number_format($total, 0) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="px-5 py-4">{{ $items->links() }}</div>
        @endif
    </div>

    {{-- Add Item Modal --}}
    @can('manage construction')
    <div id="modal-add-item" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus text-rose-500 mr-2"></i>Add Transaction to "{{ $projectName }}"</h3>
                <button onclick="document.getElementById('modal-add-item').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('construction.store') }}" class="p-5 space-y-4">
                @csrf
                {{-- Lock project name to current project --}}
                <input type="hidden" name="project_name" value="{{ $projectName }}">
                <input type="hidden" name="redirect_project" value="{{ $projectName }}">
                @if($market)
                <input type="hidden" name="market_id" value="{{ $market->id }}">
                @else
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market</label>
                    <select name="market_id" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500">
                        <option value="">— Select Market —</option>
                        @foreach($markets as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Item Name *</label>
                    <input type="text" name="item_name" required
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500"
                           placeholder="e.g. Cement, Steel Bars, Labour">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Quantity *</label>
                        <input type="number" name="quantity" id="edit-qty" required min="0" step="0.01"
                               oninput="calcEditTotal()" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500" placeholder="0">
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
                        <input type="number" name="unit_price" id="edit-price" required min="0" step="0.01"
                               oninput="calcEditTotal()" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Total (Rs) *</label>
                        <input type="number" name="total" id="edit-total" required min="0" step="0.01"
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
                    <button type="button" onclick="document.getElementById('modal-add-item').classList.add('hidden')"
                            class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Add Transaction</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    function calcEditTotal() {
        const qty   = parseFloat(document.getElementById('edit-qty').value)   || 0;
        const price = parseFloat(document.getElementById('edit-price').value) || 0;
        document.getElementById('edit-total').value = (qty * price).toFixed(2);
    }
    </script>
</x-app-layout>
