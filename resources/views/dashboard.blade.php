<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">

        <a href="{{ route('markets.index') }}" class="card-stat bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-indigo-300 group">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-600 transition-colors">
                    <i class="fas fa-store text-indigo-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs text-slate-400 font-medium">Instalment</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['markets']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Active Markets</p>
            <div class="mt-2 text-xs text-indigo-600 font-medium">{{ number_format($stats['shops']) }} shops total</div>
        </a>

        <a href="{{ route('rent.index') }}" class="card-stat bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-emerald-300 group">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-600 transition-colors">
                    <i class="fas fa-key text-emerald-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs text-slate-400 font-medium">Rent</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">Rs {{ number_format($stats['rent_this_month'], 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">This Month</p>
            <div class="mt-2 text-xs text-emerald-600 font-medium">Collected rent</div>
        </a>

        <a href="{{ route('sell.index') }}" class="card-stat bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-amber-300 group">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center group-hover:bg-amber-600 transition-colors">
                    <i class="fas fa-exchange-alt text-amber-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs text-slate-400 font-medium">Sell/Purchase</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">Rs {{ number_format($stats['total_income'], 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Collections</p>
            <div class="mt-2 text-xs text-amber-600 font-medium">Rs {{ number_format($stats['pending'], 0) }} pending</div>
        </a>

        <a href="{{ route('construction.index') }}" class="card-stat bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-rose-300 group">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center group-hover:bg-rose-600 transition-colors">
                    <i class="fas fa-hard-hat text-rose-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs text-slate-400 font-medium">Construction</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">Rs {{ number_format($stats['construction_total'], 0) }}</p>
            <p class="text-xs text-slate-500 mt-1">Total Spent</p>
            <div class="mt-2 text-xs text-rose-600 font-medium">All projects</div>
        </a>

        <a href="{{ route('owners.index') }}" class="card-stat bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-purple-300 group col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center group-hover:bg-purple-600 transition-colors">
                    <i class="fas fa-user-tie text-purple-600 group-hover:text-white transition-colors"></i>
                </div>
                <span class="text-xs text-slate-400 font-medium">Owners</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['owners']) }}</p>
            <p class="text-xs text-slate-500 mt-1">Registered Owners</p>
            <div class="mt-2 text-xs text-purple-600 font-medium">{{ number_format($stats['customers']) }} customers</div>
        </a>
    </div>

    <!-- Quick Actions + Recent Payments -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fas fa-bolt text-indigo-500"></i> Quick Actions
            </h2>
            <div class="space-y-2">
                @can('manage markets')
                <button onclick="document.getElementById('modal-add-market').classList.remove('hidden')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium transition-colors">
                    <i class="fas fa-plus-circle"></i> Add New Market
                </button>
                @endcan
                <a href="{{ route('rent.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-medium transition-colors">
                    <i class="fas fa-receipt"></i> Add Rent Entry
                </a>
                <a href="{{ route('sell.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium transition-colors">
                    <i class="fas fa-handshake"></i> New Sale / Purchase
                </a>
                <a href="{{ route('construction.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-medium transition-colors">
                    <i class="fas fa-hammer"></i> Construction Entry
                </a>
                <a href="{{ route('customers.index') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-sm font-medium transition-colors">
                    <i class="fas fa-user-plus"></i> Add Customer
                </a>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-indigo-500"></i> Recent Payments
            </h2>
            @if($recentPayments->isEmpty())
                <div class="text-center py-10 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm">No payments recorded yet</p>
                </div>
            @else
            <div class="space-y-3">
                @foreach($recentPayments as $payment)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                    <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">
                            Shop {{ $payment->shop->shop_number ?? 'N/A' }}
                            <span class="text-slate-400 font-normal">— {{ $payment->shop->market->name ?? '' }}</span>
                        </p>
                        <p class="text-xs text-slate-500">{{ $payment->payment_date->format('d M Y') }} · {{ $payment->receipt_number }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-green-600">Rs {{ number_format($payment->amount, 0) }}</p>
                        <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="text-xs text-indigo-500 hover:underline">
                            <i class="fas fa-file-pdf"></i> Receipt
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Add Market Modal -->
    @can('manage markets')
    <div id="modal-add-market" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-store text-indigo-500 mr-2"></i>Add New Market</h3>
                <button onclick="document.getElementById('modal-add-market').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('markets.store') }}" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market Name *</label>
                    <input type="text" name="name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="e.g. Al-Hafeez Market">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="City / Area">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Market Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-market').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Create Market</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

</x-app-layout>
