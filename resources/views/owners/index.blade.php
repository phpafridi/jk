<x-app-layout>
    <x-slot name="header">Owner Ledger</x-slot>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-purple-50 border border-purple-200 rounded-xl text-purple-700 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- LEFT PANEL --}}
        <div class="space-y-4">

            {{-- Owner Search --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-user-tie text-purple-500"></i> Select Owner
                </h3>
                <form method="GET" id="owner-ledger-form">
                    <div class="relative">
                        <input type="text" id="ledger-owner-search" autocomplete="off"
                               placeholder="Search owner name or phone..."
                               value="{{ $selectedOwner ? $selectedOwner->name.($selectedOwner->phone?' — '.$selectedOwner->phone:'') : '' }}"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                               oninput="ledgerOwnerSearch(this)"
                               onfocus="ledgerOwnerSearch(this)">
                        <input type="hidden" name="owner_id" id="ledger-owner-id" value="{{ request('owner_id') }}">
                        <div id="ledger-owner-dd" class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-xl mt-1 max-h-56 overflow-y-auto"></div>
                    </div>
                </form>
                @if($owners->isEmpty())
                <p class="text-xs text-slate-400 mt-3 text-center">
                    No owners found. <a href="{{ route('owner-management.index') }}" class="text-purple-600 underline">Add owners here</a>.
                </p>
                @endif
            </div>

            {{-- Owner Info + Balance --}}
            @if($selectedOwner)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                        {{ strtoupper(substr($selectedOwner->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">{{ $selectedOwner->name }}</p>
                        @if($selectedOwner->phone)<p class="text-xs text-slate-400">{{ $selectedOwner->phone }}</p>@endif
                        @if($selectedOwner->cnic)<p class="text-xs text-slate-300 font-mono">{{ $selectedOwner->cnic }}</p>@endif
                    </div>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl {{ $balance >= 0 ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                    <span class="text-sm font-medium {{ $balance >= 0 ? 'text-green-700' : 'text-red-700' }}">Net Balance</span>
                    <span class="text-sm font-bold {{ $balance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        Rs {{ number_format(abs($balance), 0) }}
                        {{ $balance >= 0 ? '(CR)' : '(DR)' }}
                    </span>
                </div>
            </div>

            {{-- Add Ledger Entry --}}
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
                        <select name="transaction_type" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="debit">Debit (Owner took money)</option>
                            <option value="credit">Credit (Money returned)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Amount (Rs) *</label>
                        <input type="number" name="amount" required min="0.01" step="0.01"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="0">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Date *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Project / Purpose</label>
                        <input type="text" name="description"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="e.g. Ground Floor Construction, Shop A rent...">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Market</label>
                        <select name="market_id" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">— None —</option>
                            @foreach($markets as $market)
                            <option value="{{ $market->id }}">{{ $market->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Shop</label>
                        <select name="shop_id" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">— None —</option>
                            @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->market->name ?? '' }} #{{ $shop->shop_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Reference / Cheque No.</label>
                        <input type="text" name="reference"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="Optional">
                    </div>

                    <button type="submit" class="w-full py-2.5 btn-primary text-white rounded-xl text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i> Add Entry
                    </button>
                </form>
            </div>
            @endcan
            @endif
        </div>

        {{-- RIGHT: Ledger Table --}}
        <div class="lg:col-span-3">
            @if(!$selectedOwner)
            <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
                <i class="fas fa-user-tie text-slate-300 text-5xl mb-3"></i>
                <p class="text-slate-500 font-medium mb-1">No owner selected</p>
                <p class="text-slate-400 text-sm">Search and select an owner from the left to view their ledger</p>
            </div>
            @elseif($ledgers->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <i class="fas fa-book-open text-slate-300 text-4xl mb-3"></i>
                <p class="text-slate-500 font-medium">No ledger entries yet for {{ $selectedOwner->name }}</p>
                <p class="text-slate-400 text-sm mt-1">Add the first entry using the form on the left</p>
            </div>
            @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Ledger: {{ $selectedOwner->name }}</h3>
                    <span class="text-sm text-slate-500">{{ $ledgers->total() }} entries</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Project / Description</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Market / Shop</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Debit</th>
                                <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Credit</th>
                                <th class="text-right px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($ledgers as $ledger)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $ledger->date->format('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <p class="text-slate-800 font-medium">{{ $ledger->description ?? '—' }}</p>
                                    @if($ledger->reference)
                                    <p class="text-xs text-slate-400">Ref: {{ $ledger->reference }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    {{ $ledger->market->name ?? '' }}
                                    @if($ledger->shop) · Shop #{{ $ledger->shop->shop_number }} @endif
                                </td>
                                <td class="px-5 py-3 text-right font-semibold {{ $ledger->transaction_type === 'debit' ? 'text-red-600' : 'text-slate-300' }}">
                                    {{ $ledger->transaction_type === 'debit' ? 'Rs '.number_format($ledger->amount,0) : '—' }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold {{ $ledger->transaction_type === 'credit' ? 'text-green-600' : 'text-slate-300' }}">
                                    {{ $ledger->transaction_type === 'credit' ? 'Rs '.number_format($ledger->amount,0) : '—' }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @can('manage owners')
                                    <form method="POST" action="{{ route('owners.destroy', $ledger) }}" onsubmit="return confirm('Delete this entry?')">
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
                                <td colspan="3" class="px-5 py-3 font-semibold text-slate-700 text-sm">Net Balance</td>
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

    <script>
    const ledgerOwners = <?php echo json_encode($owners->map(fn($o)=>['id'=>$o->id,'name'=>$o->name,'phone'=>$o->phone??'','cnic'=>$o->cnic??''])->values()); ?>;

    function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/'/g,"\\'"); }

    function ledgerOwnerSearch(input) {
        const q  = input.value.trim().toLowerCase();
        const dd = document.getElementById('ledger-owner-dd');
        const filtered = q.length === 0 ? ledgerOwners.slice(0,25)
            : ledgerOwners.filter(r => r.name.toLowerCase().includes(q) || r.phone.includes(q) || r.cnic.includes(q));
        if (!filtered.length) {
            dd.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">No owners found</div>';
        } else {
            dd.innerHTML = filtered.map(r => `
                <div class="flex items-center justify-between px-4 py-2.5 hover:bg-purple-50 cursor-pointer border-b border-slate-50 last:border-0"
                     onmousedown="pickLedgerOwner(${r.id},'${esc(r.name)}','${esc(r.phone)}')">
                    <div>
                        <p class="font-medium text-slate-800 text-sm">${r.name}</p>
                        ${r.cnic ? `<p class="text-xs text-slate-400 font-mono">${r.cnic}</p>` : ''}
                    </div>
                    ${r.phone ? `<span class="text-xs text-purple-600 font-medium">${r.phone}</span>` : ''}
                </div>`).join('');
        }
        dd.classList.remove('hidden');
    }

    function pickLedgerOwner(id, name, phone) {
        document.getElementById('ledger-owner-id').value    = id;
        document.getElementById('ledger-owner-search').value = name + (phone ? ' — ' + phone : '');
        document.getElementById('ledger-owner-dd').classList.add('hidden');
        document.getElementById('owner-ledger-form').submit();
    }

    document.addEventListener('click', e => {
        const dd  = document.getElementById('ledger-owner-dd');
        const inp = document.getElementById('ledger-owner-search');
        if (inp && !inp.contains(e.target) && !dd.contains(e.target)) dd.classList.add('hidden');
    });
    </script>
</x-app-layout>
