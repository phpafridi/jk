<x-app-layout>
    <x-slot name="header">Sell / Purchase — Entry Detail</x-slot>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('sell.index') }}" class="hover:text-indigo-600 transition-colors">Sell / Purchase</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">Entry #{{ $entry->id }}</span>
    </nav>

    <!-- Header Card -->
    @php
        $colorMap = ['shop'=>'indigo','plot'=>'green','car'=>'blue'];
        $color = $colorMap[$entry->entry_type] ?? 'slate';
        $iconMap = ['shop'=>'fa-store','plot'=>'fa-map','car'=>'fa-car'];
        $icon = $iconMap[$entry->entry_type] ?? 'fa-exchange-alt';
        $amountPaid = (float)($entry->amount_paid ?? 0);
        $totalAmt   = (float)$entry->total;
        $remaining  = max(0, $totalAmt - $amountPaid);
        $paidPct    = $totalAmt > 0 ? min(100, round($amountPaid / $totalAmt * 100)) : 0;
        $pmLabels   = ['cash'=>'Cash','bank_transfer'=>'Bank Transfer','cheque'=>'Cheque','online'=>'Online','other'=>'Other'];
    @endphp
    <div class="bg-gradient-to-br from-{{ $color }}-600 to-{{ $color }}-800 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <i class="fas {{ $icon }}" style="font-size:200px;position:absolute;right:-20px;bottom:-40px;"></i>
        </div>
        <div class="relative flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-white/20">
                        <i class="fas {{ $icon }} mr-1"></i>{{ ucfirst($entry->entry_type) }}
                    </span>
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $entry->transaction_type==='sell' ? 'bg-red-400/60' : 'bg-emerald-400/60' }}">
                        {{ ucfirst($entry->transaction_type) }}
                    </span>
                    @if($remaining > 0)
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-amber-400/60">
                        <i class="fas fa-exclamation-circle mr-1"></i>Partial Payment
                    </span>
                    @else
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-emerald-400/60">
                        <i class="fas fa-check-circle mr-1"></i>Fully Paid
                    </span>
                    @endif
                </div>
                <h2 class="text-2xl font-bold">Rs {{ number_format($totalAmt, 0) }}</h2>
                <p class="text-{{ $color }}-200 text-sm mt-1">
                    <i class="fas fa-calendar mr-1"></i>{{ $entry->date->format('d M Y') }}
                    @if($entry->sellMarket)
                        &nbsp;·&nbsp;<i class="fas fa-building mr-1"></i>{{ $entry->sellMarket->name }}
                    @endif
                    @if($entry->shop_or_item_number)
                        &nbsp;·&nbsp;# {{ $entry->shop_or_item_number }}
                    @endif
                </p>
                <!-- Payment progress bar -->
                <div class="mt-3 w-64">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-white/80">Paid: Rs {{ number_format($amountPaid, 0) }}</span>
                        <span class="text-white/60">{{ $paidPct }}%</span>
                    </div>
                    <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $remaining > 0 ? 'bg-amber-400' : 'bg-emerald-400' }}" style="width: {{ $paidPct }}%"></div>
                    </div>
                    @if($remaining > 0)
                    <p class="text-xs text-white/70 mt-1">Remaining: Rs {{ number_format($remaining, 0) }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('sell.receipt', $entry) }}" target="_blank"
                   class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-medium transition-colors">
                    <i class="fas fa-print"></i> Receipt
                </a>
                @can('manage sell purchase')
                <form method="POST" action="{{ route('sell.destroy', $entry) }}" onsubmit="return confirm('Delete this entry?')">
                    @csrf @method('DELETE')
                    <button class="flex items-center gap-2 px-4 py-2 bg-red-500/80 hover:bg-red-600 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                @endcan
                <a href="{{ route('sell.index') }}" class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-sm font-medium transition-colors">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Payment Summary Card -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">Total Amount</p>
            <p class="text-lg font-bold text-slate-800">Rs {{ number_format($totalAmt, 0) }}</p>
        </div>
        <div class="bg-emerald-50 rounded-2xl border border-emerald-200 shadow-sm p-4 text-center">
            <p class="text-xs text-emerald-600 mb-1">Amount Paid</p>
            <p class="text-lg font-bold text-emerald-700">Rs {{ number_format($amountPaid, 0) }}</p>
        </div>
        <div class="bg-{{ $remaining > 0 ? 'amber' : 'slate' }}-50 rounded-2xl border border-{{ $remaining > 0 ? 'amber' : 'slate' }}-200 shadow-sm p-4 text-center">
            <p class="text-xs text-{{ $remaining > 0 ? 'amber' : 'slate' }}-600 mb-1">Remaining</p>
            <p class="text-lg font-bold text-{{ $remaining > 0 ? 'amber' : 'slate' }}-700">Rs {{ number_format($remaining, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">Payment Method</p>
            <p class="text-sm font-semibold text-slate-700 flex items-center justify-center gap-1">
                @php
                    $pmIcon = ['cash'=>'fa-money-bill-wave','bank_transfer'=>'fa-university','cheque'=>'fa-file-invoice','online'=>'fa-mobile-alt','other'=>'fa-ellipsis-h'];
                @endphp
                <i class="fas {{ $pmIcon[$entry->payment_method ?? 'cash'] ?? 'fa-money-bill-wave' }} text-indigo-500"></i>
                {{ $pmLabels[$entry->payment_method ?? 'cash'] ?? 'Cash' }}
            </p>
            @if($entry->received_by)
            <p class="text-xs text-slate-400 mt-1">by {{ $entry->received_by }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Seller -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-user-minus text-red-600 text-xs"></i>
                </span>
                Seller
            </h3>
            @if($entry->sellerCustomer)
            <a href="{{ route('customers.show', $entry->sellerCustomer) }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline mb-3">
                <i class="fas fa-link"></i> View Customer Profile
            </a>
            @endif
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Name</span><span class="font-medium text-slate-800">{{ $entry->seller_name ?: '—' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">CNIC</span><span class="font-mono text-slate-700">{{ $entry->seller_cnic ?: '—' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Phone</span><span class="text-slate-700">{{ $entry->seller_phone ?: '—' }}</span></div>
            </div>
        </div>

        <!-- Buyer -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-user-plus text-green-600 text-xs"></i>
                </span>
                Buyer
            </h3>
            @if($entry->buyerCustomer)
            <a href="{{ route('customers.show', $entry->buyerCustomer) }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline mb-3">
                <i class="fas fa-link"></i> View Customer Profile
            </a>
            @endif
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Name</span><span class="font-medium text-slate-800">{{ $entry->buyer_name ?: '—' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">CNIC</span><span class="font-mono text-slate-700">{{ $entry->buyer_cnic ?: '—' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Phone</span><span class="text-slate-700">{{ $entry->buyer_phone ?: '—' }}</span></div>
            </div>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center">
                <i class="fas fa-file-invoice-dollar text-amber-600 text-xs"></i>
            </span>
            Transaction Details
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            @if($entry->entry_type !== 'car')
                @if($entry->per_sqft_rate)
                <div class="bg-slate-50 rounded-xl p-3"><p class="text-xs text-slate-500 mb-1">Per Sqft Rate</p><p class="font-semibold text-slate-800">Rs {{ number_format($entry->per_sqft_rate, 0) }}</p></div>
                @endif
                @if($entry->sqft)
                <div class="bg-slate-50 rounded-xl p-3"><p class="text-xs text-slate-500 mb-1">Area</p><p class="font-semibold text-slate-800">{{ number_format($entry->sqft, 2) }} sqft</p></div>
                @endif
                @if($entry->shop_or_item_number)
                <div class="bg-slate-50 rounded-xl p-3"><p class="text-xs text-slate-500 mb-1">Shop / Plot #</p><p class="font-semibold text-slate-800">{{ $entry->shop_or_item_number }}</p></div>
                @endif
            @endif

            @if($entry->entry_type === 'car')
                @if($entry->car_make)<div class="bg-slate-50 rounded-xl p-3"><p class="text-xs text-slate-500 mb-1">Make</p><p class="font-semibold text-slate-800">{{ $entry->car_make }}</p></div>@endif
                @if($entry->car_model)<div class="bg-slate-50 rounded-xl p-3"><p class="text-xs text-slate-500 mb-1">Model</p><p class="font-semibold text-slate-800">{{ $entry->car_model }}</p></div>@endif
                @if($entry->car_year)<div class="bg-slate-50 rounded-xl p-3"><p class="text-xs text-slate-500 mb-1">Year</p><p class="font-semibold text-slate-800">{{ $entry->car_year }}</p></div>@endif
                @if($entry->car_registration)<div class="bg-slate-50 rounded-xl p-3"><p class="text-xs text-slate-500 mb-1">Registration</p><p class="font-semibold text-slate-800 font-mono">{{ $entry->car_registration }}</p></div>@endif
            @endif

            <div class="bg-indigo-50 rounded-xl p-3">
                <p class="text-xs text-indigo-500 mb-1">Total Amount</p>
                <p class="font-bold text-indigo-700 text-lg">Rs {{ number_format($totalAmt, 0) }}</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3">
                <p class="text-xs text-emerald-500 mb-1">Amount Paid</p>
                <p class="font-bold text-emerald-700 text-lg">Rs {{ number_format($amountPaid, 0) }}</p>
            </div>
            @if($remaining > 0)
            <div class="bg-amber-50 rounded-xl p-3">
                <p class="text-xs text-amber-500 mb-1">Remaining</p>
                <p class="font-bold text-amber-700 text-lg">Rs {{ number_format($remaining, 0) }}</p>
            </div>
            @endif
        </div>

        <!-- Payment method row -->
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm border-t border-slate-100 pt-4">
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Payment Method</p>
                <p class="font-semibold text-slate-800">
                    <i class="fas {{ $pmIcon[$entry->payment_method ?? 'cash'] ?? 'fa-money-bill-wave' }} text-indigo-400 mr-1"></i>
                    {{ $pmLabels[$entry->payment_method ?? 'cash'] ?? 'Cash' }}
                </p>
            </div>
            @if($entry->received_by)
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Received By</p>
                <p class="font-semibold text-slate-800"><i class="fas fa-user text-slate-400 mr-1"></i>{{ $entry->received_by }}</p>
            </div>
            @endif
        </div>

        @if($entry->notes)
        <div class="mt-4 p-3 bg-slate-50 rounded-xl">
            <p class="text-xs text-slate-500 mb-1">Notes</p>
            <p class="text-sm text-slate-700">{{ $entry->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Documents / Receipts -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-paperclip text-purple-600 text-xs"></i>
                </span>
                Attached Documents & Receipts
            </h3>
            <span class="text-xs text-slate-400">{{ $entry->documents->count() }} file(s)</span>
        </div>

        @if($entry->documents->isEmpty())
        <div class="text-center py-8 text-slate-400">
            <i class="fas fa-folder-open text-4xl mb-2"></i>
            <p class="text-sm">No documents attached to this entry.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($entry->documents as $doc)
            <div class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $doc->type === 'image' ? 'bg-blue-100' : 'bg-slate-100' }}">
                    <i class="fas {{ $doc->type === 'image' ? 'fa-image text-blue-600' : 'fa-file-pdf text-slate-500' }} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-800 truncate">{{ $doc->name }}</p>
                    <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" class="text-xs text-indigo-600 hover:underline">
                        {{ $doc->type === 'image' ? 'View Image' : 'Open File' }}
                    </a>
                </div>
                @can('manage sell purchase')
                <button type="button" onclick="openDeleteModal('{{ route('sell.documents.destroy', $doc) }}', '{{ addslashes($doc->name) }}')" class="text-red-400 hover:text-red-600 text-xs w-6 h-6 flex items-center justify-center flex-shrink-0"><i class="fas fa-trash"></i></button>
                @endcan
            </div>
            @endforeach
        </div>
        @endif

        @can('manage sell purchase')
        <form method="POST" action="{{ route('sell.documents.store', $entry) }}" enctype="multipart/form-data" class="mt-5 pt-5 border-t border-slate-100 space-y-3">
            @csrf
            <p class="text-xs font-semibold text-slate-600">Upload More Files</p>
            <select name="doc_type" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700">
                <option value="cnic">🪪 CNIC</option>
                <option value="mou">🤝 MOU</option>
                <option value="agreement">📋 Agreement / Contract</option>
                <option value="photo">🖼 Photo</option>
                <option value="other" selected>📎 Other</option>
            </select>
            <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-indigo-400 transition-colors cursor-pointer" onclick="document.getElementById('sell-doc-upload').click()">
                <input type="file" id="sell-doc-upload" name="documents[]" multiple
                       accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                       class="hidden" onchange="updateSellFileLabel(this)">
                <i class="fas fa-cloud-upload-alt text-2xl text-slate-300 mb-1 block" id="sell-upload-icon"></i>
                <span id="sell-file-label" class="text-sm text-slate-500">Click to choose files</span>
                <p class="text-xs text-slate-400 mt-1">Images, PDF, DOC — max 20MB each</p>
            </div>
            <button type="submit" class="w-full px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium">
                <i class="fas fa-upload mr-1"></i> Upload
            </button>
        </form>
        @endcan
    </div>

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
            <p class="text-sm text-slate-600 mb-4">Type <span class="font-bold text-red-600">123</span> to confirm deletion.</p>
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
    <form id="delete-doc-form" method="POST" class="hidden">@csrf @method('DELETE')</form>
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
        function closeDeleteModal() { document.getElementById('modal-delete-doc').classList.add('hidden'); }
        function submitDelete() {
            document.getElementById('delete-doc-form').action = deleteActionUrl;
            document.getElementById('delete-doc-form').submit();
        }
        document.getElementById('modal-delete-doc').addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });

        function updateSellFileLabel(input) {
            const label = document.getElementById('sell-file-label');
            const icon  = document.getElementById('sell-upload-icon');
            if (input.files.length > 0) {
                label.textContent = input.files.length === 1 ? input.files[0].name : input.files.length + ' files selected';
                label.classList.add('text-indigo-600','font-semibold');
                label.classList.remove('text-slate-500');
                icon.classList.add('text-indigo-400');
                icon.classList.remove('text-slate-300');
            } else {
                label.textContent = 'Click to choose files';
                label.classList.remove('text-indigo-600','font-semibold');
                label.classList.add('text-slate-500');
                icon.classList.remove('text-indigo-400');
                icon.classList.add('text-slate-300');
            }
        }
    </script>
</x-app-layout>
