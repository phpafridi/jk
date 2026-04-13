<x-app-layout>
    <x-slot name="header">Shop #{{ $rentShop->shop_number }}</x-slot>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('rent.markets.index') }}" class="hover:text-emerald-600">Rent Markets</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('rent.markets.show', $rentShop->rentMarket) }}" class="hover:text-emerald-600">{{ $rentShop->rentMarket->name }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">Shop #{{ $rentShop->shop_number }}</span>
    </nav>

    {{-- Shop Info Card --}}
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"><i class="fas fa-key text-white" style="font-size:180px;position:absolute;right:-20px;bottom:-40px;"></i></div>
        <div class="relative flex items-start justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold">Shop #{{ $rentShop->shop_number }}</h2>
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                        {{ $rentShop->status === 'rented' ? 'bg-blue-500/30 text-blue-100' : ($rentShop->status === 'inactive' ? 'bg-slate-500/30 text-slate-200' : 'bg-green-500/30 text-green-100') }}">
                        {{ ucfirst($rentShop->status) }}
                    </span>
                </div>
                <p class="text-emerald-200 text-sm"><i class="fas fa-store mr-1"></i>{{ $rentShop->rentMarket->name }}</p>
                @if($rentShop->tenant_name)
                <p class="text-emerald-100 text-sm mt-1"><i class="fas fa-user mr-1"></i>{{ $rentShop->tenant_name }}
                    @if($rentShop->tenant_phone) · {{ $rentShop->tenant_phone }}@endif
                    @if($rentShop->tenant_cnic) · {{ $rentShop->tenant_cnic }}@endif
                </p>
                @endif
            </div>
            <div class="flex gap-3 flex-wrap">
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-emerald-200">Monthly Rent</p>
                    <p class="text-lg font-bold">Rs {{ number_format($rentShop->rent_amount ?? 0, 0) }}</p>
                </div>
                @if($rentShop->rent_start_date)
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-emerald-200">Renting Since</p>
                    <p class="text-sm font-bold">{{ $rentShop->rent_start_date->format('M Y') }}</p>
                    <p class="text-xs text-emerald-200">{{ $rentStatus['months_due'] }} month(s) billed</p>
                </div>
                @endif
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-emerald-200">Total Collected</p>
                    <p class="text-lg font-bold">Rs {{ number_format($totalPaid, 0) }}</p>
                </div>
                @if($rentStatus['months_missed'] > 0)
                <div class="bg-red-500/40 backdrop-blur rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-red-100">Missed</p>
                    <p class="text-lg font-bold text-red-100">{{ $rentStatus['months_missed'] }} months</p>
                    <p class="text-xs text-red-100">Rs {{ number_format($rentStatus['missed_amount'], 0) }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Add Rent Entry --}}
        @can('manage rent')
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-500"></i> Add Rent Entry
                </h3>
                <form method="POST" action="{{ route('rent.entries.store', $rentShop) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="shop_number" value="{{ $rentShop->shop_number }}">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Rent Amount (Rs) *</label>
                        <input type="number" name="rent" required min="0" step="0.01"
                               value="{{ $rentShop->rent_amount ?? '' }}"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Amount Paid (Rs)</label>
                        <input type="number" name="amount_paid" min="0" step="0.01"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Date *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Customer</label>
                        <div class="relative">
                            <input type="text" id="customer-search" autocomplete="off" placeholder="Search customer..."
                                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   oninput="customerSearch(this)" onfocus="customerSearch(this)">
                            <input type="hidden" name="customer_id" id="customer-id">
                            <div id="customer-dd" class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-xl mt-1 max-h-48 overflow-y-auto"></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="cash">💵 Cash</option>
                            <option value="bank_transfer">🏦 Bank Transfer</option>
                            <option value="cheque">📃 Cheque</option>
                            <option value="online">📱 Online</option>
                            <option value="other">📎 Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Received By</label>
                        <input type="text" name="received_by"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Name">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 btn-primary text-white rounded-xl text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i> Save Entry
                    </button>
                </form>
            </div>

            {{-- Upload Files --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-upload text-emerald-500"></i> Upload Files
                </h3>
                <form method="POST" action="{{ route('rent.shops.documents.store', $rentShop) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Document Type</label>
                        <select name="doc_type" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-700">
                            <option value="cnic">🪪 CNIC</option>
                            <option value="mou">🤝 MOU</option>
                            <option value="agreement">📋 Agreement / Contract</option>
                            <option value="photo">🖼 Photo</option>
                            <option value="other" selected>📎 Other</option>
                        </select>
                    </div>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-emerald-400 transition-colors cursor-pointer" onclick="document.getElementById('rent-file-upload').click()">
                        <input type="file" name="documents[]" multiple id="rent-file-upload"
                               accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                               class="hidden" onchange="updateRentFileLabel(this)">
                        <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 mb-2 block" id="rent-upload-icon"></i>
                        <span id="rent-file-label" class="text-sm text-slate-500">Click to choose files</span>
                        <p class="text-xs text-slate-400 mt-1">Images, PDF, DOC — max 20MB each</p>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-slate-700 hover:bg-slate-800 text-white rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-upload mr-1"></i> Upload
                    </button>
                </form>
            </div>
        </div>
        @endcan

        {{-- RIGHT: Rent Entries + Documents --}}
        <div class="{{ auth()->user()->can('manage rent') ? 'lg:col-span-2' : 'lg:col-span-3' }} space-y-6">

            {{-- Rent Entries Table --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-list text-emerald-500"></i> Rent Collection History
                        <span class="text-xs bg-emerald-100 text-emerald-700 rounded-full px-2 py-0.5">{{ $rentShop->rentEntries->count() }}</span>
                    </h3>
                    <div class="text-sm text-slate-500">
                        Total Charged: <span class="font-semibold text-slate-700">Rs {{ number_format($totalRent, 0) }}</span>
                        · Paid: <span class="font-semibold text-emerald-600">Rs {{ number_format($totalPaid, 0) }}</span>
                        @if($totalRent > $totalPaid)
                        · <span class="font-semibold text-red-500">Due: Rs {{ number_format($totalRent - $totalPaid, 0) }}</span>
                        @endif
                    </div>
                </div>
                @if($rentShop->rentEntries->isEmpty())
                <div class="p-10 text-center">
                    <i class="fas fa-coins text-slate-300 text-4xl mb-2"></i>
                    <p class="text-slate-400 text-sm">No rent entries yet</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Receipt #</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Rent</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Paid</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Received By</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Method</th>
                                <th class="text-right px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rentShop->rentEntries as $entry)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $entry->date->format('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs font-mono bg-sky-50 text-sky-700 px-2 py-0.5 rounded-lg">
                                        {{ $entry->receipt_number ?? 'RNT-' . str_pad($entry->id, 6, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-700">Rs {{ number_format($entry->rent, 0) }}</td>
                                <td class="px-5 py-3 text-right font-semibold {{ $entry->amount_paid >= $entry->rent ? 'text-emerald-600' : 'text-orange-500' }}">
                                    Rs {{ number_format($entry->amount_paid, 0) }}
                                </td>
                                <td class="px-5 py-3 text-slate-600 text-xs">{{ $entry->customer->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-500 text-xs">{{ $entry->received_by ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-500 text-xs">
                                    @php $pm=['cash'=>'💵 Cash','bank_transfer'=>'🏦 Bank','cheque'=>'📃 Cheque','online'=>'📱 Online','other'=>'Other']; @endphp
                                    {{ $pm[$entry->payment_method ?? 'cash'] ?? '💵 Cash' }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('rent.entries.receipt', $entry) }}" target="_blank"
                                           class="text-sky-500 hover:text-sky-700 p-1.5 rounded-lg hover:bg-sky-50 transition-colors" title="Print Receipt">
                                            <i class="fas fa-print text-xs"></i>
                                        </a>
                                        @can('manage rent')
                                        <form method="POST" action="{{ route('rent.entries.destroy', $entry) }}" onsubmit="return confirm('Delete entry?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Documents Album --}}
            @if($rentShop->documents->isNotEmpty())
            @php
                $images = $rentShop->documents->where('type','image');
                $docs   = $rentShop->documents->where('type','document');
            @endphp

            {{-- Image Album --}}
            @if($images->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-images text-emerald-500"></i> Photo Album
                    <span class="text-xs bg-emerald-100 text-emerald-700 rounded-full px-2 py-0.5">{{ $images->count() }}</span>
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($images as $img)
                    <div class="relative group rounded-xl overflow-hidden bg-slate-100 aspect-square">
                        <img src="{{ asset('storage/' . $img->path) }}" alt="{{ $img->name }}"
                             class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-200"
                             onclick="openLightbox('{{ asset('storage/' . $img->path) }}','{{ addslashes($img->name) }}')">
                        @can('manage rent')
                        <button type="button" onclick="openDeleteModal('{{ route('rent.shops.documents.destroy', $img) }}', '{{ addslashes($img->name) }}')" style="position:absolute;top:4px;right:4px;z-index:20;width:24px;height:24px;background:#ef4444;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.4);color:white"><i class="fas fa-trash"></i></button>
                        @endcan
                        <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <p class="text-white text-xs truncate">{{ $img->name }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Documents List --}}
            @if($docs->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-file-alt text-emerald-500"></i> Documents
                    <span class="text-xs bg-emerald-100 text-emerald-700 rounded-full px-2 py-0.5">{{ $docs->count() }}</span>
                </h3>
                <div class="space-y-2">
                    @foreach($docs as $doc)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ str_ends_with($doc->name,'.pdf') ? 'fa-file-pdf text-red-500' : 'fa-file-word text-blue-500' }} text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-700">{{ $doc->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ asset('storage/' . $doc->path) }}" target="_blank"
                               class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                            @can('manage rent')
                            <button type="button" onclick="openDeleteModal('{{ route('rent.shops.documents.destroy', $doc) }}', '{{ addslashes($doc->name) }}')" class="text-red-400 hover:text-red-600 text-xs w-6 h-6 flex items-center justify-center flex-shrink-0"><i class="fas fa-trash"></i></button>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @else
            <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-8 text-center">
                <i class="fas fa-folder-open text-slate-300 text-4xl mb-2"></i>
                <p class="text-slate-400 text-sm">No files uploaded yet</p>
                <p class="text-slate-300 text-xs mt-1">Use the upload panel to add photos or documents</p>
            </div>
            @endif

        </div>
    </div>

    {{-- Lightbox --}}
    <div id="lightbox" class="hidden fixed inset-0 z-[100] bg-black/90 flex items-center justify-center p-4" onclick="closeLightbox()">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white/70 hover:text-white w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
            <i class="fas fa-times"></i>
        </button>
        <img id="lightbox-img" src="" alt="" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl object-contain" onclick="event.stopPropagation()">
        <p id="lightbox-caption" class="absolute bottom-4 text-white/70 text-sm"></p>
    </div>

    <script>
    const shopCustomers = <?php echo json_encode($customers->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'phone'=>$c->phone??'','cnic'=>$c->cnic??''])->values()); ?>;

    function customerSearch(input) {
        const q  = input.value.trim().toLowerCase();
        const dd = document.getElementById('customer-dd');
        const filtered = q.length === 0 ? shopCustomers.slice(0,25)
            : shopCustomers.filter(r => r.name.toLowerCase().includes(q) || r.phone.includes(q) || r.cnic.includes(q));
        dd.innerHTML = filtered.length === 0
            ? '<div class="px-4 py-3 text-sm text-slate-400">No results</div>'
            : filtered.map(r => `<div class="flex items-center justify-between px-4 py-2.5 hover:bg-emerald-50 cursor-pointer border-b border-slate-50 last:border-0"
                 onmousedown="pickCustomer(${r.id},'${r.name.replace(/'/g,"\\'")}','${r.phone}')">
                <div><p class="font-medium text-slate-800 text-sm">${r.name}</p>${r.cnic?`<p class="text-xs text-slate-400 font-mono">${r.cnic}</p>`:''}</div>
                ${r.phone?`<span class="text-xs text-emerald-600 font-medium">${r.phone}</span>`:''}
            </div>`).join('');
        dd.classList.remove('hidden');
    }

    function pickCustomer(id, name, phone) {
        document.getElementById('customer-id').value     = id;
        document.getElementById('customer-search').value = name + (phone ? ' — ' + phone : '');
        document.getElementById('customer-dd').classList.add('hidden');
    }

    function updateRentFileLabel(input) {
        const label = document.getElementById('rent-file-label');
        const icon  = document.getElementById('rent-upload-icon');
        if (input.files.length > 0) {
            const names = Array.from(input.files).map(f => f.name).join(', ');
            label.textContent = input.files.length === 1 ? input.files[0].name : input.files.length + ' files: ' + names;
            label.classList.add('text-emerald-600', 'font-semibold');
            label.classList.remove('text-slate-500');
            icon.classList.add('text-emerald-400');
            icon.classList.remove('text-slate-300');
        } else {
            label.textContent = 'Click to choose files';
            label.classList.remove('text-emerald-600', 'font-semibold');
            label.classList.add('text-slate-500');
            icon.classList.remove('text-emerald-400');
            icon.classList.add('text-slate-300');
        }
    }

    function openLightbox(src, name) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox-caption').textContent = name;
        document.getElementById('lightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if(e.key === 'Escape') closeLightbox(); });

    document.addEventListener('click', e => {
        const dd  = document.getElementById('customer-dd');
        const inp = document.getElementById('customer-search');
        if (inp && !inp.contains(e.target) && !dd.contains(e.target)) dd.classList.add('hidden');
    });
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
