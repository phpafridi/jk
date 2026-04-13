<x-app-layout>
    <x-slot name="header">Rent Entries</x-slot>

    {{-- Market Overview Panel --}}
    @if($rentMarkets->count() > 0)
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i class="fas fa-store text-emerald-500"></i> Market Summary
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($rentMarkets as $rm)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-white font-bold text-sm">{{ $rm['name'] }}</p>
                        @if($rm['location'])<p class="text-emerald-100 text-xs"><i class="fas fa-map-marker-alt mr-1"></i>{{ $rm['location'] }}</p>@endif
                    </div>
                    <a href="{{ route('rent.markets.show', $rm['id']) }}" class="text-emerald-100 hover:text-white text-xs"><i class="fas fa-external-link-alt"></i></a>
                </div>
                <div class="p-3 grid grid-cols-3 gap-2 border-b border-slate-100">
                    <div class="text-center">
                        <p class="text-xs text-slate-400">Shops</p>
                        <p class="font-bold text-slate-700">{{ $rm['total_shops'] }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-slate-400">Pending</p>
                        <p class="font-bold {{ $rm['pending_shops'] > 0 ? 'text-amber-600' : 'text-slate-400' }}">{{ $rm['pending_shops'] }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-slate-400">Paid</p>
                        <p class="font-bold text-emerald-600 text-xs">Rs {{ number_format($rm['paid_amount'], 0) }}</p>
                    </div>
                </div>
                @if($rm['pending_amount'] > 0)
                <div class="px-3 py-2 bg-amber-50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-700">Rent Pending: Rs {{ number_format($rm['pending_amount'], 0) }}</p>
                        <p class="text-xs text-amber-500">{{ $rm['pending_months'] }} month(s) overdue</p>
                    </div>
                    <i class="fas fa-exclamation-circle text-amber-400"></i>
                </div>
                @if(count($rm['shop_details']) > 0)
                <div class="px-3 pb-2 max-h-32 overflow-y-auto">
                    @foreach($rm['shop_details'] as $sd)
                    <div class="flex items-center justify-between py-1 border-b border-slate-50 last:border-0 text-xs">
                        <span class="font-medium text-slate-600">Shop #{{ $sd['shop_number'] }}</span>
                        <span class="text-amber-600 font-semibold">Rs {{ number_format($sd['pending_amount'], 0) }} / {{ $sd['pending_months'] }}mo</span>
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <div class="px-3 py-2 bg-emerald-50 text-center">
                    <p class="text-xs text-emerald-600 font-medium"><i class="fas fa-check-circle mr-1"></i>All rents paid</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex gap-2 flex-wrap flex-1 max-w-2xl">
            <select name="market_id" onchange="this.form.submit()"
                    class="border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">— All Markets —</option>
                @foreach($rentMarketsAll as $market)
                <option value="{{ $market->id }}" {{ request('market_id') == $market->id ? 'selected' : '' }}>{{ $market->name }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search shop number..."
                   class="flex-1 border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-sm text-slate-600"><i class="fas fa-search"></i></button>
        </form>
        <div class="flex gap-2">
            <a href="{{ route('rent.markets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-medium">
                <i class="fas fa-store"></i> Markets
            </a>
        </div>
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
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Market</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Rent</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Paid</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($entries as $entry)
                    @php $due = $entry->rent - $entry->amount_paid; @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-key text-emerald-600 text-xs"></i>
                                </div>
                                <p class="font-medium text-slate-800">Shop # {{ $entry->shop_number }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $entry->rentShop->rentMarket->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($entry->customer)
                                <p class="font-medium text-slate-700">{{ $entry->customer->name }}</p>
                                <p class="text-xs text-slate-400">{{ $entry->customer->phone ?? '' }}</p>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $entry->date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right font-medium text-slate-700">Rs {{ number_format($entry->rent, 0) }}</td>
                        <td class="px-5 py-3 text-right">
                            <span class="font-semibold {{ $entry->amount_paid >= $entry->rent ? 'text-green-600' : 'text-amber-600' }}">
                                Rs {{ number_format($entry->amount_paid, 0) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($due <= 0)
                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium">Paid</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-xs font-medium">Pending Rs {{ number_format($due, 0) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            @can('manage rent')
                            <form method="POST" action="{{ route('rent.entries.destroy', $entry) }}" onsubmit="return confirm('Delete entry?')">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50"><i class="fas fa-trash text-xs"></i></button>
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
</x-app-layout>
