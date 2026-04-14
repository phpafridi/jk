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
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Payment</th>
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
                        <td class="px-5 py-3">
                            @php $pmIcons = ['cash'=>'💵','bank_transfer'=>'🏦','cheque'=>'📃','online'=>'📱','other'=>'📎']; @endphp
                            <span class="text-xs text-slate-600">{{ $pmIcons[$item->payment_method ?? 'cash'] ?? '💵' }} {{ ucfirst(str_replace('_',' ',$item->payment_method ?? 'cash')) }}</span>
                            @if($item->vendor_name)<p class="text-xs text-slate-400 mt-0.5">{{ $item->vendor_name }}</p>@endif
                            @if($item->documents->isNotEmpty())
                            <div class="flex gap-1 mt-1 flex-wrap">
                                @foreach($item->documents as $doc)
                                <a href="{{ asset('storage/'.$doc->path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs bg-rose-50 text-rose-600 hover:bg-rose-100 px-2 py-0.5 rounded-lg">
                                    <i class="fas {{ $doc->type === 'image' ? 'fa-image' : 'fa-file-pdf' }} text-xs"></i>
                                    {{ Str::limit($doc->name, 12) }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </td>
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
                        <td colspan="3"></td>
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
            <form method="POST" action="{{ route('construction.store') }}" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
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

                {{-- Payment Details --}}
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 space-y-3">
                    <p class="text-xs font-semibold text-rose-700 uppercase tracking-wide"><i class="fas fa-credit-card mr-1"></i>Payment Details</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 bg-white">
                                <option value="cash">💵 Cash</option>
                                <option value="bank_transfer">🏦 Bank Transfer</option>
                                <option value="cheque">📃 Cheque</option>
                                <option value="online">📱 Online</option>
                                <option value="other">📎 Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Paid To / Vendor</label>
                            <input type="text" name="vendor_name"
                                   class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500" placeholder="Vendor or supplier name">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Paid By / Authorized By</label>
                        <input type="text" name="received_by"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500" placeholder="Who authorized this payment">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                {{-- Invoice Upload --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        <i class="fas fa-paperclip text-rose-400 mr-1"></i>Attach Invoices / Photos
                    </label>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-rose-400 transition-colors cursor-pointer"
                         onclick="document.getElementById('const-file-upload').click()">
                        <input type="file" id="const-file-upload" name="documents[]" multiple
                               accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                               class="hidden" onchange="updateConstFileLabel(this)">
                        <i class="fas fa-cloud-upload-alt text-2xl text-slate-300 mb-1 block" id="const-upload-icon"></i>
                        <span id="const-file-label" class="text-sm text-slate-500">Click to attach invoices or photos</span>
                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, PDF, DOC — max 20MB each</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-item').classList.add('hidden')"
                            class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">
                        <i class="fas fa-save mr-1"></i>Save Transaction
                    </button>
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
    function updateConstFileLabel(input) {
        const label = document.getElementById('const-file-label');
        const icon  = document.getElementById('const-upload-icon');
        if (input.files.length > 0) {
            label.textContent = input.files.length === 1 ? input.files[0].name : input.files.length + ' files selected';
            label.classList.add('text-rose-600','font-semibold');
            label.classList.remove('text-slate-500');
            icon.classList.add('text-rose-400');
            icon.classList.remove('text-slate-300');
        } else {
            label.textContent = 'Click to attach invoices or photos';
            label.classList.remove('text-rose-600','font-semibold');
            label.classList.add('text-slate-500');
            icon.classList.remove('text-rose-400');
            icon.classList.add('text-slate-300');
        }
    }
    </script>
</x-app-layout>
