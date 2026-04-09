<x-app-layout>
    <x-slot name="header">Sell / Purchase</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search buyer, seller, number..."
                   class="border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-52">
            <select name="type" class="border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Types</option>
                <option value="car"  {{ request('type')==='car'  ? 'selected' : '' }}>Car</option>
                <option value="shop" {{ request('type')==='shop' ? 'selected' : '' }}>Shop</option>
                <option value="plot" {{ request('type')==='plot' ? 'selected' : '' }}>Plot</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-sm text-slate-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
        @can('manage sell purchase')
        <button onclick="document.getElementById('modal-add-sell').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> New Entry
        </button>
        @endcan
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($entries->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-exchange-alt text-slate-300 text-5xl mb-3"></i>
            <p class="text-slate-400">No sell/purchase entries found</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Seller</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Buyer</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Market</th>
                        <th class="text-right px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($entries as $entry)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs px-2 py-1 rounded-full font-medium w-fit
                                    {{ $entry->entry_type==='car' ? 'bg-blue-100 text-blue-700' : ($entry->entry_type==='plot' ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700') }}">
                                    <i class="fas {{ $entry->entry_type==='car' ? 'fa-car' : ($entry->entry_type==='plot' ? 'fa-map' : 'fa-store') }} mr-1"></i>
                                    {{ ucfirst($entry->entry_type) }}
                                </span>
                                <span class="text-xs px-2 py-1 rounded-full font-medium w-fit
                                    {{ $entry->transaction_type==='sell' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($entry->transaction_type) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $entry->date->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800">{{ $entry->seller_name ?? '—' }}</p>
                            <p class="text-xs text-slate-400">{{ $entry->seller_phone ?? $entry->seller_cnic ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800">{{ $entry->buyer_name ?? '—' }}</p>
                            <p class="text-xs text-slate-400">{{ $entry->buyer_phone ?? $entry->buyer_cnic ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-slate-800">Rs {{ number_format($entry->total, 0) }}</td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $entry->market->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            @can('manage sell purchase')
                            <form method="POST" action="{{ route('sell.destroy', $entry) }}" onsubmit="return confirm('Delete?')">
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
            </table>
        </div>
        <div class="px-5 py-4">{{ $entries->links() }}</div>
        @endif
    </div>

    @can('manage sell purchase')
    <div id="modal-add-sell" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay" x-data="{ entryType: 'shop' }">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-exchange-alt text-amber-500 mr-2"></i>New Sell / Purchase Entry</h3>
                <button onclick="document.getElementById('modal-add-sell').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data" class="p-5 space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Entry Type *</label>
                    <div class="flex gap-3">
                        <label class="flex-1">
                            <input type="radio" name="entry_type" value="shop" x-model="entryType" class="sr-only" checked>
                            <div :class="entryType==='shop' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600'"
                                 class="border-2 rounded-xl p-3 text-center text-sm font-medium cursor-pointer transition-all hover:border-indigo-300">
                                <i class="fas fa-store text-lg block mb-1"></i> Shop
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="entry_type" value="plot" x-model="entryType" class="sr-only">
                            <div :class="entryType==='plot' ? 'border-green-500 bg-green-50 text-green-700' : 'border-slate-200 text-slate-600'"
                                 class="border-2 rounded-xl p-3 text-center text-sm font-medium cursor-pointer transition-all hover:border-green-300">
                                <i class="fas fa-map text-lg block mb-1"></i> Plot
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="entry_type" value="car" x-model="entryType" class="sr-only">
                            <div :class="entryType==='car' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600'"
                                 class="border-2 rounded-xl p-3 text-center text-sm font-medium cursor-pointer transition-all hover:border-blue-300">
                                <i class="fas fa-car text-lg block mb-1"></i> Car
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Transaction *</label>
                        <select name="transaction_type" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="sell">Sell</option>
                            <option value="purchase">Purchase</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div x-show="entryType !== 'car'" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Market</label>
                            <select name="market_id" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">— Select Market —</option>
                                @foreach($markets as $market)
                                <option value="{{ $market->id }}">{{ $market->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Shop / Plot Number</label>
                            <input type="text" name="shop_or_item_number" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Per Sqft Rate</label>
                            <input type="number" name="per_sqft_rate" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Sqft Area</label>
                            <input type="number" name="sqft" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Total (Rs) *</label>
                            <input type="number" name="total" required min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                        </div>
                    </div>
                </div>

                <div x-show="entryType === 'car'" class="space-y-4" style="display:none;">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Car Make</label>
                            <input type="text" name="car_make" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Toyota, Honda...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Car Model</label>
                            <input type="text" name="car_model" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Corolla, Civic...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Year</label>
                            <input type="text" name="car_year" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="2020">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Registration</label>
                            <input type="text" name="car_registration" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="ABC-1234">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Total Price (Rs) *</label>
                        <input type="number" name="total" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                    </div>
                </div>

                {{-- SELLER --}}
                <div class="border border-slate-200 rounded-xl p-4 space-y-3">
                    <p class="text-sm font-semibold text-slate-700"><i class="fas fa-user-minus text-red-500 mr-1"></i> Seller Details</p>

                    {{-- Quick-pick from Customer only --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Pick from Customer</label>
                        <div class="relative">
                            <input type="text" id="sell-seller-cust-search" autocomplete="off" placeholder="Search customer..."
                                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   oninput="liveSearch(this,'sell-seller-cust-dd','sell-seller-cust-id',sellCustomers,'seller')"
                                   onfocus="liveSearch(this,'sell-seller-cust-dd','sell-seller-cust-id',sellCustomers,'seller')">
                            <input type="hidden" name="seller_customer_id" id="sell-seller-cust-id">
                            <div id="sell-seller-cust-dd" class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-xl mt-1 max-h-48 overflow-y-auto"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                            <input type="text" name="seller_name" id="seller_name" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">CNIC</label>
                            <input type="text" name="seller_cnic" id="seller_cnic" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="00000-0000000-0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                            <input type="text" name="seller_phone" id="seller_phone" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- BUYER --}}
                <div class="border border-slate-200 rounded-xl p-4 space-y-3">
                    <p class="text-sm font-semibold text-slate-700"><i class="fas fa-user-plus text-green-500 mr-1"></i> Buyer Details</p>

                    {{-- Quick-pick from Customer only --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Pick from Customer</label>
                        <div class="relative">
                            <input type="text" id="sell-buyer-cust-search" autocomplete="off" placeholder="Search customer..."
                                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   oninput="liveSearch(this,'sell-buyer-cust-dd','sell-buyer-cust-id',sellCustomers,'buyer')"
                                   onfocus="liveSearch(this,'sell-buyer-cust-dd','sell-buyer-cust-id',sellCustomers,'buyer')">
                            <input type="hidden" name="buyer_customer_id" id="sell-buyer-cust-id">
                            <div id="sell-buyer-cust-dd" class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-xl mt-1 max-h-48 overflow-y-auto"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                            <input type="text" name="buyer_name" id="buyer_name" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">CNIC</label>
                            <input type="text" name="buyer_cnic" id="buyer_cnic" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="00000-0000000-0">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Phone</label>
                            <input type="text" name="buyer_phone" id="buyer_phone" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Attach Documents / Images</label>
                    <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                           class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-amber-50 file:text-amber-700 file:font-medium hover:file:bg-amber-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-sell').classList.add('hidden')"
                            class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    const sellCustomers = <?php echo json_encode($customers->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'phone'=>$c->phone??'','cnic'=>$c->cnic??''])->values()); ?>;

    function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/'/g,"\\'"); }

    function liveSearch(input, ddId, hiddenId, dataset, role){
        const q  = input.value.trim().toLowerCase();
        const dd = document.getElementById(ddId);
        const filtered = q.length===0 ? dataset.slice(0,25)
            : dataset.filter(r=>r.name.toLowerCase().includes(q)||r.phone.includes(q)||r.cnic.includes(q));
        if(!filtered.length){
            dd.innerHTML='<div class="px-4 py-3 text-sm text-slate-400">No results</div>';
        } else {
            dd.innerHTML = filtered.map(r=>`
                <div class="flex items-center justify-between px-4 py-2.5 hover:bg-indigo-50 cursor-pointer border-b border-slate-50 last:border-0"
                     onmousedown="pickPerson('${ddId}','${hiddenId}','${input.id}',${r.id},'${esc(r.name)}','${esc(r.phone)}','${esc(r.cnic)}','${role}')">
                    <div>
                        <p class="font-medium text-slate-800 text-sm">${r.name}</p>
                        ${r.cnic?`<p class="text-xs text-slate-400 font-mono">${r.cnic}</p>`:''}
                    </div>
                    ${r.phone?`<span class="text-xs text-indigo-600 font-medium">${r.phone}</span>`:''}
                </div>`).join('');
        }
        dd.classList.remove('hidden');
    }

    function pickPerson(ddId, hiddenId, inputId, id, name, phone, cnic, role){
        document.getElementById(hiddenId).value  = id;
        document.getElementById(inputId).value   = name + (phone?' — '+phone:'');
        document.getElementById(ddId).classList.add('hidden');
        // Auto-fill the manual fields
        const nameEl  = document.getElementById(role+'_name');
        const cnicEl  = document.getElementById(role+'_cnic');
        const phoneEl = document.getElementById(role+'_phone');
        if(nameEl)  nameEl.value  = name;
        if(cnicEl)  cnicEl.value  = cnic;
        if(phoneEl) phoneEl.value = phone;
    }

    document.addEventListener('click', e=>{
        document.querySelectorAll('[id$="-dd"]').forEach(dd=>{
            const inputId = dd.id.replace('-dd','-search');
            const input   = document.getElementById(inputId);
            if(input && !input.contains(e.target) && !dd.contains(e.target))
                dd.classList.add('hidden');
        });
    });
    </script>
</x-app-layout>
