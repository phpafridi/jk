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

        <!-- LEFT: Shop Info + Add Payment -->
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
                    @if($shop->customers->isNotEmpty())
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Customer</p>
                            @foreach($shop->customers->take(2) as $c)
                            <p class="text-sm font-medium text-slate-800">{{ $c->name }}</p>
                            @endforeach
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
                        <input type="number" name="amount" required min="0.01" step="0.01"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
            @endcan
        </div>

        <!-- RIGHT: Payment Ledger + Documents -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Payment Ledger -->
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
                            @foreach($shop->payments->sortBy('payment_date') as $payment)
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

            <!-- Documents & Files -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-paperclip text-purple-500"></i> Documents & Files
                    </h3>
                    <span class="text-sm text-slate-500">{{ $shop->documents->count() }} file(s)</span>
                </div>

                @can('manage shops')
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <form method="POST" action="{{ route('shops.documents.store', $shop) }}" enctype="multipart/form-data">
                        @csrf
                        <p class="text-xs font-medium text-slate-600 mb-2">Upload receipts, agreements, or images</p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <label class="flex-1 flex flex-col items-center justify-center gap-1 border-2 border-dashed border-slate-300 hover:border-purple-400 rounded-xl px-4 py-4 cursor-pointer transition-colors bg-white">
                                <i class="fas fa-cloud-upload-alt text-slate-400 text-2xl"></i>
                                <span class="text-xs text-slate-500 text-center">Click to choose files or drag & drop</span>
                                <span class="text-xs text-slate-400">JPG, PNG, PDF, DOC up to 10MB each</span>
                                <input type="file" name="documents[]" multiple
                                       accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                                       class="hidden" onchange="updateFileLabel(this)">
                                <span id="file-label" class="text-xs text-purple-600 font-medium hidden mt-1"></span>
                            </label>
                            <button type="submit"
                                    class="sm:w-32 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-medium flex items-center justify-center gap-2 shrink-0 transition-colors">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
                @endcan

                @if($shop->documents->isEmpty())
                <div class="p-12 text-center">
                    <i class="fas fa-folder-open text-slate-200 text-6xl mb-3"></i>
                    <p class="text-slate-400 text-sm font-medium">No files uploaded yet</p>
                    <p class="text-slate-300 text-xs mt-1">Upload receipts, contracts or photos above</p>
                </div>
                @else
                <div class="p-5">
                    @php
                        $images = $shop->documents->where('type','image');
                        $pdfs   = $shop->documents->where('type','!=','image');
                    @endphp

                    {{-- Image Grid --}}
                    @if($images->isNotEmpty())
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
                        <i class="fas fa-images mr-1"></i> Images ({{ $images->count() }})
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-5">
                        @foreach($images as $img)
                        <div class="group relative rounded-xl overflow-hidden border border-slate-200 aspect-square bg-slate-100">
                            <a href="{{ asset('storage/' . $img->path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $img->path) }}" alt="{{ $img->name }}"
                                     class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                                    <i class="fas fa-search-plus text-white text-xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </div>
                            </a>
                            @can('manage shops')
                            <button type="button" onclick="openDeleteModal('{{ route('shops.documents.destroy', $img) }}', '{{ addslashes($img->name) }}')" style="position:absolute;top:4px;right:4px;z-index:20;width:24px;height:24px;background:#ef4444;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.4);color:white"><i class="fas fa-trash"></i></button>
                            @endcan
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent px-2 py-1.5 translate-y-full group-hover:translate-y-0 transition-transform">
                                <p class="text-white text-xs truncate">{{ $img->name }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Document List --}}
                    @if($pdfs->isNotEmpty())
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
                        <i class="fas fa-file-alt mr-1"></i> Documents ({{ $pdfs->count() }})
                    </p>
                    <div class="space-y-2">
                        @foreach($pdfs as $doc)
                        @php
                            $ext = strtolower(pathinfo($doc->name, PATHINFO_EXTENSION));
                            $iconClass = match($ext) {
                                'pdf'         => 'fa-file-pdf text-red-500',
                                'doc','docx'  => 'fa-file-word text-blue-500',
                                default       => 'fa-file-alt text-slate-400',
                            };
                        @endphp
                        <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                <i class="fas {{ $iconClass }} text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $doc->name }}</p>
                                <p class="text-xs text-slate-400 uppercase">{{ $ext }} file</p>
                            </div>
                            <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" download
                               class="flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors shrink-0">
                                <i class="fas fa-download"></i> Download
                            </a>
                            @can('manage shops')
                            <button type="button" onclick="openDeleteModal('{{ route('shops.documents.destroy', $doc) }}', '{{ addslashes($doc->name) }}')" class="text-red-400 hover:text-red-600 text-xs w-6 h-6 flex items-center justify-center flex-shrink-0"><i class="fas fa-trash"></i></button>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif
            </div>

        </div>{{-- end right col --}}
    </div>

    <script>
    function updateFileLabel(input) {
        const label = document.getElementById('file-label');
        if (input.files.length > 0) {
            label.textContent = input.files.length === 1
                ? input.files[0].name
                : input.files.length + ' files selected';
            label.classList.remove('hidden');
        } else {
            label.classList.add('hidden');
        }
    }
    </script>

    {{-- Delete Confirm Modal --}}
    <div id="modal-delete-doc" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-trash text-red-500"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Delete File</h3>
                    <p class="text-xs text-slate-500" id="delete-doc-name"></p>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-4">Type <span class="font-bold text-red-600">123</span> to confirm deletion. This cannot be undone.</p>
            <input type="text" id="delete-confirm-input" placeholder="Type 123 here..."
                   class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-red-400"
                   oninput="document.getElementById('btn-confirm-delete').disabled = this.value !== '123'">
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="button" id="btn-confirm-delete" disabled onclick="submitDelete()"
                        class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-sm font-medium hover:bg-red-600 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
    <form id="delete-doc-form" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>
    <script>
        let deleteActionUrl = '';
        function openDeleteModal(url, name) {
            deleteActionUrl = url;
            document.getElementById('delete-doc-name').textContent = name;
            document.getElementById('delete-confirm-input').value = '';
            document.getElementById('btn-confirm-delete').disabled = true;
            document.getElementById('modal-delete-doc').classList.remove('hidden');
            setTimeout(() => document.getElementById('delete-confirm-input').focus(), 100);
        }
        function closeDeleteModal() {
            document.getElementById('modal-delete-doc').classList.add('hidden');
            deleteActionUrl = '';
        }
        function submitDelete() {
            document.getElementById('delete-doc-form').action = deleteActionUrl;
            document.getElementById('delete-doc-form').submit();
        }
        document.getElementById('modal-delete-doc').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>

</x-app-layout>
