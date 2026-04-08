<x-app-layout>
    <x-slot name="header">Owners Ledger</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Left: Owner Selector -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-user-tie text-purple-500"></i> Select Owner
                </h3>
                <form method="GET">
                    <select name="owner_id" onchange="this.form.submit()" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Choose Owner —</option>
                        @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($selectedOwner)
            <!-- Owner Summary -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center text-white text-lg font-bold">
                        {{ substr($selectedOwner->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">{{ $selectedOwner->name }}</p>
                        <p class="text-xs text-slate-400">{{ $selectedOwner->email }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between p-3 rounded-xl {{ $balance >= 0 ? 'bg-green-50' : 'bg-red-50' }}">
                        <span class="text-sm font-medium {{ $balance >= 0 ? 'text-green-700' : 'text-red-700' }}">Net Balance</span>
                        <span class="text-sm font-bold {{ $balance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            Rs {{ number_format(abs($balance), 0) }}
                            {{ $balance >= 0 ? '(CR)' : '(DR)' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Add Ledger Entry -->
            @can('manage owners')
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-purple-500"></i> Add Entry
                </h3>
                <form method="POST" action="{{ route('owners.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="owner_id" value="{{ $selectedOwner->id }}">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Type *</label>
                        <select name="transaction_type" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="debit">Debit (Owner Owes Me)</option>
                            <option value="credit">Credit (I Owe Owner)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Amount (Rs) *</label>
                        <input type="number" name="amount" required min="0.01" step="0.01" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Date *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Market</label>
                        <select name="market_id" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— None —</option>
                            @foreach($markets as $market)
                            <option value="{{ $market->id }}">{{ $market->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Shop</label>
                        <select name="shop_id" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— None —</option>
                            @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->market->name }} #{{ $shop->shop_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Details..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Reference</label>
                        <input type="text" name="reference" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Cheque no, etc.">
                    </div>
                    <button type="submit" class="w-full py-2.5 btn-primary text-white rounded-xl text-sm font-medium">
                        Add Entry
                    </button>
                </form>
            </div>
            @endcan
            @endif
        </div>

        <!-- Right: Ledger Table -->
        <div class="lg:col-span-3">
            @if(!$selectedOwner)
            <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
                <i class="fas fa-user-tie text-slate-300 text-5xl mb-3"></i>
                <p class="text-slate-400">Select an owner from the left to view their ledger</p>
            </div>
            @elseif($ledgers->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <i class="fas fa-book text-slate-300 text-4xl mb-3"></i>
                <p class="text-slate-400">No ledger entries for {{ $selectedOwner->name }}</p>
            </div>
            @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Ledger: {{ $selectedOwner->name }}</h3>
                    <span class="text-sm text-slate-500">{{ $ledgers->total() }} entries</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Market / Shop</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Debit</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Credit</th>
                                <th class="text-right px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($ledgers as $entry)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $entry->date->format('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <p class="text-slate-800">{{ $entry->description ?? '—' }}</p>
                                    @if($entry->reference)
                                    <p class="text-xs text-slate-400">Ref: {{ $entry->reference }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    {{ $entry->market->name ?? '' }}
                                    @if($entry->shop) · Shop #{{ $entry->shop->shop_number }} @endif
                                </td>
                                <td class="px-5 py-3 text-right font-semibold {{ $entry->transaction_type === 'debit' ? 'text-red-600' : 'text-slate-300' }}">
                                    {{ $entry->transaction_type === 'debit' ? 'Rs ' . number_format($entry->amount, 0) : '—' }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold {{ $entry->transaction_type === 'credit' ? 'text-green-600' : 'text-slate-300' }}">
                                    {{ $entry->transaction_type === 'credit' ? 'Rs ' . number_format($entry->amount, 0) : '—' }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @can('manage owners')
                                    <form method="POST" action="{{ route('owners.destroy', $entry) }}" onsubmit="return confirm('Delete?')">
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
                                <td colspan="3" class="px-5 py-3 font-semibold text-slate-700">Net Balance</td>
                                <td colspan="2" class="px-5 py-3 text-right font-bold text-base {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rs {{ number_format(abs($balance), 0) }} {{ $balance >= 0 ? '(CR)' : '(DR)' }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="px-5 py-4">{{ $ledgers->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
