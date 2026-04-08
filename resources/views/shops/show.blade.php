<x-app-layout>
    <x-slot name="header">Shop Ledger</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('markets.index') }}" class="hover:text-indigo-600">Markets</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('markets.show', $shop->market) }}" class="hover:text-indigo-600">{{ $shop->market->name }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">Shop # {{ $shop->shop_number }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT: Shop Info + Stats -->
        <div class="space-y-4">
            <!-- Shop Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-5 text-white">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-store text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-lg">Shop # {{ $shop->shop_number }}</p>
                            <p class="text-indigo-200 text-xs">{{ $shop->market->name }}</p>
                        </div>
                    </div>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">{{ ucfirst($shop->type) }}</span>
                </div>
                <div class="p-4 space-y-3">
                    @if($shop->owner)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-user text-indigo-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Owner</p>
                            <p class="text-sm font-medium text-slate-800">{{ $shop->owner->name }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-green-50 rounded-xl p-3 text-center">
                            <p class="text-xs text-green-600 font-medium">Total Paid</p>
                            <p class="text-base font-bold text-green-700">Rs {{ number_format($totalPaid, 0) }}</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-3 text-center">
                            <p class="text-xs text-red-600 font-medium">Balance Due</p>
                            <p class="text-base font-bold text-red-700">Rs {{ number_format(max(0, $balance), 0) }}</p>
                        </div>
                    </div>
                    @if($shop->total_amount > 0)
                    <div>
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Progress</span>
                            @php $pct = $shop->total_amount > 0 ? min(100, ($totalPaid / $shop->total_amount) * 100) : 0; @endphp
                            <span>{{ number_format($pct, 1) }}%</span>
                        </div>
                        <div class="bg-slate-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Total: Rs {{ number_format($shop->total_amount, 0) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Add Payment -->
            @can('manage shops')
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-green-500"></i> Add Payment
                </h3>
                <form method="POST" action="{{ route('shops.payments.store', $shop) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Amount (Rs) *</label>
                        <input type="number" name="amount" required min="0.01" step="0.01" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Method</label>
                        <select name="payment_method" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Notes</label>
                        <input type="text" name="notes" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Optional...">
                    </div>
                    <button type="submit" class="w-full py-2.5 btn-primary text-white rounded-xl text-sm font-medium">
                        <i class="fas fa-save mr-1"></i> Record Payment
                    </button>
                </form>
            </div>

            <!-- Upload Documents -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-paperclip text-indigo-500"></i> Documents & Images
                </h3>
                <form method="POST" action="{{ route('shops.documents.store', $shop) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-indigo-400 transition-colors">
                        <i class="fas fa-cloud-upload-alt text-slate-400 text-2xl mb-2"></i>
                        <p class="text-xs text-slate-500 mb-2">Upload images, PDFs, or documents</p>
                        <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" class="w-full text-xs text-slate-600">
                    </div>
                    <button type="submit" class="w-full mt-3 py-2 border border-indigo-300 text-indigo-600 rounded-xl text-sm font-medium hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-upload mr-1"></i> Upload Files
                    </button>
                </form>

                <!-- Existing Documents -->
                @if($shop->documents->isNotEmpty())
                <div class="mt-4 space-y-2">
                    @foreach($shop->documents as $doc)
                    <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-xl">
                        <i class="fas {{ $doc->type === 'image' ? 'fa-image text-blue-500' : 'fa-file-alt text-slate-400' }} text-sm flex-shrink-0"></i>
                        @if($doc->type === 'image')
                        <a href="{{ Storage::url($doc->path) }}" target="_blank" class="flex-1 text-xs text-slate-600 truncate hover:text-indigo-600">{{ $doc->name }}</a>
                        @else
                        <a href="{{ Storage::url($doc->path) }}" download class="flex-1 text-xs text-slate-600 truncate hover:text-indigo-600">{{ $doc->name }}</a>
                        @endif
                        <form method="POST" action="{{ route('shops.documents.destroy', $doc) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endcan
        </div>

        <!-- RIGHT: Payment Ledger -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-book text-indigo-500"></i> Payment Ledger
                    </h3>
                    <span class="text-sm text-slate-500">{{ $shop->payments->count() }} entries</span>
                </div>

                @if($shop->payments->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-receipt text-slate-300 text-4xl mb-3"></i>
                    <p class="text-slate-400 text-sm">No payments recorded yet</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Receipt #</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Method</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Notes</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php $running = 0; @endphp
                            @foreach($shop->payments->sortBy('payment_date') as $payment)
                            @php $running += $payment->amount; @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded-lg font-mono">{{ $payment->receipt_number }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-xs px-2 py-1 rounded-full font-medium
                                        {{ $payment->payment_method === 'cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($payment->payment_method) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-500 text-xs max-w-[150px] truncate">{{ $payment->notes ?? '—' }}</td>
                                <td class="px-5 py-3 text-right font-semibold text-green-600 whitespace-nowrap">Rs {{ number_format($payment->amount, 0) }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('payments.receipt', $payment) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                        <i class="fas fa-print"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                            <tr>
                                <td colspan="4" class="px-5 py-3 font-semibold text-slate-700 text-sm">Total Collected</td>
                                <td class="px-5 py-3 text-right font-bold text-green-600 text-base">Rs {{ number_format($totalPaid, 0) }}</td>
                                <td></td>
                            </tr>
                            @if($shop->total_amount > 0)
                            <tr>
                                <td colspan="4" class="px-5 py-3 font-semibold text-slate-700 text-sm">Balance Due</td>
                                <td class="px-5 py-3 text-right font-bold text-red-600 text-base">Rs {{ number_format(max(0, $balance), 0) }}</td>
                                <td></td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
