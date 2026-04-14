<x-app-layout>
    <x-slot name="header">Reports</x-slot>

    {{-- Summary Cards Row 1: Totals --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
        <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-store text-indigo-600"></i>
            </div>
            <div>
                <p class="text-xs text-indigo-500 font-semibold uppercase tracking-wide">Instalment Markets</p>
                <p class="text-2xl font-bold text-indigo-700">{{ $summary['total_instalment_markets'] }}</p>
                <p class="text-xs text-indigo-400">{{ $summary['total_instalment_shops'] }} total shops</p>
            </div>
        </div>
        <div class="bg-teal-50 border border-teal-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-key text-teal-600"></i>
            </div>
            <div>
                <p class="text-xs text-teal-500 font-semibold uppercase tracking-wide">Rent Markets</p>
                <p class="text-2xl font-bold text-teal-700">{{ $summary['total_rent_markets'] }}</p>
                <p class="text-xs text-teal-400">{{ $summary['total_rent_shops'] }} total shops</p>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-door-open text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-blue-500 font-semibold uppercase tracking-wide">Rented</p>
                <p class="text-2xl font-bold text-blue-700">{{ $summary['total_rented'] }}</p>
                <p class="text-xs text-blue-400">of {{ $summary['total_rent_shops'] }} rent shops</p>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-door-closed text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-green-500 font-semibold uppercase tracking-wide">Available</p>
                <p class="text-2xl font-bold text-green-700">{{ $summary['total_available_rent'] }}</p>
                <p class="text-xs text-green-400">rent shops free</p>
            </div>
        </div>
    </div>

    {{-- Summary Cards Row 2: Financial alerts --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-center">
            <p class="text-xs text-red-500 font-semibold uppercase tracking-wide">Rent Overdue</p>
            <p class="text-2xl font-bold text-red-700">{{ $summary['rent_missed_shops'] }}</p>
            <p class="text-xs text-red-400 font-medium">Rs {{ number_format($summary['rent_missed_amount'],0) }}</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-2xl p-4 text-center">
            <p class="text-xs text-orange-500 font-semibold uppercase tracking-wide">Instal. Missed</p>
            <p class="text-2xl font-bold text-orange-700">{{ $summary['instalment_missed_shops'] }}</p>
            <p class="text-xs text-orange-400 font-medium">Rs {{ number_format($summary['instalment_missed_amount'],0) }}</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4 text-center">
            <p class="text-xs text-purple-500 font-semibold uppercase tracking-wide">Total Balance Due</p>
            <p class="text-xl font-bold text-purple-700">Rs {{ number_format($summary['instalment_due_amount'],0) }}</p>
            <p class="text-xs text-purple-400">instalment balance</p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 overflow-x-auto">
            <nav class="flex gap-0 min-w-max">
                @php
                    $tabs = [
                        ['key'=>'rent_available',       'label'=>'🟢 Available (Rent)',      'count'=>$summary['total_available_rent']],
                        ['key'=>'rent_rented',          'label'=>'🔵 Rented Shops',          'count'=>$summary['total_rented']],
                        ['key'=>'rent_missed',          'label'=>'🔴 Rent Overdue',          'count'=>$summary['rent_missed_shops']],
                        ['key'=>'rent_paid',            'label'=>'✅ Rent Paid',             'count'=>$summary['rent_paid_shops']],
                        ['key'=>'instalment_missed',    'label'=>'⚠️ Instal. Missed',        'count'=>$summary['instalment_missed_shops']],
                        ['key'=>'instalment_paid',      'label'=>'✅ Instal. Paid',          'count'=>$summary['instalment_paid_shops']],
                        ['key'=>'instalment_due',       'label'=>'💰 Instal. Due Amount',    'count'=>$summary['instalment_active']],
                    ];
                @endphp
                @foreach($tabs as $tab)
                <a href="{{ route('reports.index', ['type'=>$tab['key']]) }}"
                   class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
                       {{ $reportType === $tab['key']
                           ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
                           : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                    {{ $tab['label'] }}
                    <span class="text-xs px-1.5 py-0.5 rounded-full {{ $reportType === $tab['key'] ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
                @endforeach
            </nav>
        </div>

        <div class="p-5">

        {{-- ── AVAILABLE RENT SHOPS ─────────────────────────────────── --}}
        @if($reportType === 'rent_available')
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>
                    Available Shops for Rent
                </h3>
                <span class="text-sm text-slate-500">{{ $availableRentShops->count() }} shop(s)</span>
            </div>
            @if($availableRentShops->isEmpty())
                <p class="text-slate-400 text-sm text-center py-8">No available rent shops found.</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Market</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Shop #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Rent/Month</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Notes</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($availableRentShops as $shop)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $shop->rentMarket->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-bold text-green-700">{{ $shop->shop_number }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $shop->rent_amount ? 'Rs '.number_format($shop->rent_amount,0) : '—' }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">{{ $shop->notes ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('rent.shops.show', $shop) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        {{-- ── RENTED SHOPS ─────────────────────────────────────────── --}}
        @elseif($reportType === 'rent_rented')
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                    Currently Rented Shops
                </h3>
                <span class="text-sm text-slate-500">{{ $rentedShops->count() }} shop(s)</span>
            </div>
            @if($rentedShops->isEmpty())
                <p class="text-slate-400 text-sm text-center py-8">No rented shops found.</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Market</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Shop #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Tenant</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Rent/Month</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Since</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rentedShops as $shop)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $shop->rentMarket->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-bold text-blue-700">{{ $shop->shop_number }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $shop->tenant_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $shop->tenant_phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $shop->rent_amount ? 'Rs '.number_format($shop->rent_amount,0) : '—' }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">{{ $shop->rent_start_date ? $shop->rent_start_date->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('rent.shops.show', $shop) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        {{-- ── RENT OVERDUE ─────────────────────────────────────────── --}}
        @elseif($reportType === 'rent_missed')
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>
                    Shops with Overdue Rent
                </h3>
                <div class="text-right">
                    <p class="text-sm font-semibold text-red-600">Total Overdue: Rs {{ number_format($summary['rent_missed_amount'],0) }}</p>
                    <p class="text-xs text-slate-400">{{ $rentMissedShops->count() }} shops</p>
                </div>
            </div>
            @if($rentMissedShops->isEmpty())
                <p class="text-green-600 text-sm text-center py-8 font-medium">✅ All rent is up to date!</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Market</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Shop #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Tenant</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Missed Months</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-red-500 uppercase">Overdue Amount</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rentMissedShops as $shop)
                        <tr class="hover:bg-red-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $shop->rentMarket->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-bold text-red-700">{{ $shop->shop_number }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $shop->tenant_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $shop->tenant_phone ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $shop->_status['months_missed'] }} month(s)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-red-600">
                                Rs {{ number_format($shop->_status['missed_amount'],0) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('rent.shops.show', $shop) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-red-50 border-t-2 border-red-200">
                        <tr>
                            <td colspan="5" class="px-4 py-3 font-semibold text-red-700">Total Overdue</td>
                            <td class="px-4 py-3 text-right font-bold text-red-700">Rs {{ number_format($summary['rent_missed_amount'],0) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif

        {{-- ── RENT PAID ────────────────────────────────────────────── --}}
        @elseif($reportType === 'rent_paid')
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                    Shops with Rent Up-to-Date
                </h3>
                <span class="text-sm text-slate-500">{{ $rentPaidShops->count() }} shop(s)</span>
            </div>
            @if($rentPaidShops->isEmpty())
                <p class="text-slate-400 text-sm text-center py-8">No fully-paid rent shops found.</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Market</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Shop #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Tenant</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Months Paid</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-emerald-600 uppercase">Total Paid</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rentPaidShops as $shop)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $shop->rentMarket->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-bold text-emerald-700">{{ $shop->shop_number }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $shop->tenant_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $shop->tenant_phone ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle"></i>
                                    {{ $shop->_status['months_paid'] }} month(s)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-600">
                                Rs {{ number_format($shop->_status['paid_amount'],0) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('rent.shops.show', $shop) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        {{-- ── INSTALMENT MISSED ────────────────────────────────────── --}}
        @elseif($reportType === 'instalment_missed')
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 inline-block"></span>
                    Shops with Missed Instalments
                </h3>
                <div class="text-right">
                    <p class="text-sm font-semibold text-orange-600">Total Missed: Rs {{ number_format($summary['instalment_missed_amount'],0) }}</p>
                    <p class="text-xs text-slate-400">{{ $instalmentMissedShops->count() }} shops</p>
                </div>
            </div>
            @if($instalmentMissedShops->isEmpty())
                <p class="text-green-600 text-sm text-center py-8 font-medium">✅ All instalments are up to date!</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Market</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Shop #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Owner/Buyer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Monthly</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Missed Months</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-orange-500 uppercase">Missed Amount</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($instalmentMissedShops as $shop)
                        <tr class="hover:bg-orange-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $shop->market->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-bold text-orange-700">{{ $shop->shop_number }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $shop->owner->name ?? ($shop->customers->first()->name ?? '—') }}</td>
                            <td class="px-4 py-3 text-slate-600">Rs {{ number_format($shop->monthly_instalment ?? 0,0) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $shop->_instStatus['months_missed'] }} month(s)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-orange-600">
                                Rs {{ number_format($shop->_instStatus['missed_amount'],0) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('shops.show', $shop) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-orange-50 border-t-2 border-orange-200">
                        <tr>
                            <td colspan="5" class="px-4 py-3 font-semibold text-orange-700">Total Missed</td>
                            <td class="px-4 py-3 text-right font-bold text-orange-700">Rs {{ number_format($summary['instalment_missed_amount'],0) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif

        {{-- ── INSTALMENT PAID ──────────────────────────────────────── --}}
        @elseif($reportType === 'instalment_paid')
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                    Shops with Instalments Up-to-Date
                </h3>
                <span class="text-sm text-slate-500">{{ $instalmentPaidShops->count() }} shop(s)</span>
            </div>
            @if($instalmentPaidShops->isEmpty())
                <p class="text-slate-400 text-sm text-center py-8">No shops with fully paid instalments this period.</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Market</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Shop #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Owner/Buyer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Months Paid</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Total Paid</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Balance Left</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($instalmentPaidShops as $shop)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $shop->market->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-bold text-emerald-700">{{ $shop->shop_number }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $shop->owner->name ?? ($shop->customers->first()->name ?? '—') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle"></i>
                                    {{ $shop->_instStatus['months_paid'] }} / {{ $shop->_instStatus['months_due'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-emerald-600 font-semibold">Rs {{ number_format($shop->_instStatus['paid_amount'],0) }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $shop->_instStatus['balance'] > 0 ? 'text-slate-600' : 'text-green-600' }}">
                                {{ $shop->_instStatus['balance'] > 0 ? 'Rs '.number_format($shop->_instStatus['balance'],0) : '🎉 Fully Paid' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('shops.show', $shop) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        {{-- ── INSTALMENT DUE BALANCE ───────────────────────────────── --}}
        @elseif($reportType === 'instalment_due')
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span>
                    Instalment Due Amounts (Remaining Balance)
                </h3>
                <div class="text-right">
                    <p class="text-sm font-semibold text-purple-600">Total Outstanding: Rs {{ number_format($summary['instalment_due_amount'],0) }}</p>
                    <p class="text-xs text-slate-400">{{ $instalmentDueShops->count() }} shops</p>
                </div>
            </div>
            @if($instalmentDueShops->isEmpty())
                <p class="text-green-600 text-sm text-center py-8 font-medium">✅ All instalments are fully paid!</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Market</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Shop #</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Owner/Buyer</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Total Price</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Paid So Far</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-purple-500 uppercase">Balance Due</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Progress</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($instalmentDueShops as $shop)
                        @php
                            $pct = $shop->total_amount > 0 ? min(100, ($shop->_instStatus['paid_amount'] / $shop->total_amount) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-purple-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-slate-700">{{ $shop->market->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-bold text-purple-700">{{ $shop->shop_number }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $shop->owner->name ?? ($shop->customers->first()->name ?? '—') }}</td>
                            <td class="px-4 py-3 text-slate-600">Rs {{ number_format($shop->total_amount ?? 0,0) }}</td>
                            <td class="px-4 py-3 text-emerald-600 font-semibold">Rs {{ number_format($shop->_instStatus['paid_amount'],0) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-purple-600">
                                Rs {{ number_format($shop->_instStatus['balance'],0) }}
                            </td>
                            <td class="px-4 py-3 min-w-[100px]">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-slate-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 whitespace-nowrap">{{ number_format($pct,0) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('shops.show', $shop) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-purple-50 border-t-2 border-purple-200">
                        <tr>
                            <td colspan="5" class="px-4 py-3 font-semibold text-purple-700">Total Outstanding Balance</td>
                            <td class="px-4 py-3 text-right font-bold text-purple-700">Rs {{ number_format($summary['instalment_due_amount'],0) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif

        @endif
        </div>{{-- end .p-5 --}}
    </div>

</x-app-layout>
