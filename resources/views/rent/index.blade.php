<x-app-layout>
    <x-slot name="header">Rent Management</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex gap-2 flex-1 max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search shop number..."
                   class="flex-1 border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-sm text-slate-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
        @can('manage rent')
        <button onclick="document.getElementById('modal-add-rent').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> Add Rent Entry
        </button>
        @endcan
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($entries->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-key text-slate-300 text-5xl mb-3"></i>
            <p class="text-slate-400">No rent entries found</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Shop</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Owner</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Rent</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Paid</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Received By</th>
                        <th class="text-right px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($entries as $entry)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-key text-emerald-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800">Shop # {{ $entry->shop_number }}</p>
                                    <p class="text-xs text-slate-400">{{ $entry->shop->market->name ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $entry->owner->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $entry->date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right font-medium text-slate-700">Rs {{ number_format($entry->rent, 0) }}</td>
                        <td class="px-5 py-3 text-right">
                            <span class="font-semibold {{ $entry->amount_paid >= $entry->rent ? 'text-green-600' : 'text-amber-600' }}">
                                Rs {{ number_format($entry->amount_paid, 0) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $entry->received_by ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            @can('manage rent')
                            <form method="POST" action="{{ route('rent.destroy', $entry) }}" onsubmit="return confirm('Delete entry?')">
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
            </table>
        </div>
        <div class="px-5 py-4">{{ $entries->links() }}</div>
        @endif
    </div>

    <!-- Add Rent Modal -->
    @can('manage rent')
    <div id="modal-add-rent" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-key text-emerald-500 mr-2"></i>Add Rent Entry</h3>
                <button onclick="document.getElementById('modal-add-rent').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('rent.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Shop *</label>
                    <select name="shop_id" required onchange="fillShopNumber(this)" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Select Shop —</option>
                        @foreach($shops as $shop)
                        <option value="{{ $shop->id }}" data-number="{{ $shop->shop_number }}">
                            {{ $shop->market->name }} – Shop #{{ $shop->shop_number }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Shop Number *</label>
                    <input type="text" name="shop_number" id="rent-shop-number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Will auto-fill">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Rent Amount (Rs) *</label>
                        <input type="number" name="rent" required min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Amount Paid (Rs)</label>
                        <input type="number" name="amount_paid" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date *</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Owner</label>
                    <select name="owner_id" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Select Owner —</option>
                        @foreach($owners as $owner)
                        <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Received By</label>
                    <input type="text" name="received_by" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Name of collector">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-rent').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    function fillShopNumber(select) {
        const opt = select.options[select.selectedIndex];
        document.getElementById('rent-shop-number').value = opt.dataset.number || '';
    }
    </script>
</x-app-layout>
