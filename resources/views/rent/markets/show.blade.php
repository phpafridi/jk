<x-app-layout>
    <x-slot name="header">{{ $rentMarket->name }}</x-slot>

    @if(session('success'))
    <div class="mb-4 px-5 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('rent.markets.index') }}" class="hover:text-emerald-600">Rent Markets</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">{{ $rentMarket->name }}</span>
    </nav>

    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-6 mb-6 text-black relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"><i class="fas fa-key text-black" style="font-size:200px;position:absolute;right:-20px;bottom:-40px;"></i></div>
        <div class="relative">
            <h2 class="text-2xl font-bold">{{ $rentMarket->name }}</h2>
            @if($rentMarket->location)<p class="text-emerald-100 text-sm mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $rentMarket->location }}</p>@endif
            @if($rentMarket->description)<p class="text-emerald-100 text-sm mt-1">{{ $rentMarket->description }}</p>@endif
            <div class="flex gap-4 mt-4">
                <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-xs text-emerald-100">Total Shops</p>
                    <p class="text-xl font-bold">{{ $shops->total() }}</p>
                </div>
                @php
                    $totalPending = $shops->sum('pending_amount');
                    $totalMissed  = $shops->sum('pending_months');
                @endphp
                @if($totalPending > 0)
                <div class="bg-amber-500/30 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-xs text-amber-100">Total Pending</p>
                    <p class="text-xl font-bold">Rs {{ number_format($totalPending, 0) }}</p>
                    <p class="text-xs text-amber-100">{{ $totalMissed }} month(s)</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mb-5">
        <h3 class="font-semibold text-slate-800 flex items-center gap-2"><i class="fas fa-store-alt text-emerald-500"></i> Shops — click to manage rent & files</h3>
        @can('manage rent')
        <button onclick="document.getElementById('modal-add-shop').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> Add Shop
        </button>
        @endcan
    </div>

    @if($shops->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <i class="fas fa-store-slash text-slate-300 text-5xl mb-3"></i>
        <p class="text-slate-500">No shops yet. Add your first shop above.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($shops as $shop)
        <div class="bg-white rounded-2xl border {{ $shop->pending_amount > 0 ? 'border-amber-200' : 'border-slate-200' }} shadow-sm hover:shadow-md transition-all overflow-hidden">
            <!-- Header -->
            <a href="{{ route('rent.shops.show', $shop) }}" class="block px-4 py-3 border-b border-slate-100 flex items-center justify-between hover:bg-slate-50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-key text-emerald-600 text-sm"></i>
                    </div>
                    <span class="font-semibold text-slate-800">Shop # {{ $shop->shop_number }}</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ $shop->status === 'rented' ? 'bg-blue-100 text-blue-700' : ($shop->status === 'inactive' ? 'bg-slate-100 text-slate-500' : 'bg-green-100 text-green-700') }}">
                    {{ ucfirst($shop->status) }}
                </span>
            </a>

            <!-- Body -->
            <a href="{{ route('rent.shops.show', $shop) }}" class="block p-4 space-y-1.5">
                @if($shop->tenant_name)
                <p class="text-sm font-medium text-slate-700"><i class="fas fa-user text-emerald-400 mr-1"></i>{{ $shop->tenant_name }}</p>
                @endif
                @if($shop->rent_amount)
                <p class="text-sm text-slate-600"><i class="fas fa-coins text-emerald-400 mr-1"></i>Rs {{ number_format($shop->rent_amount, 0) }}/month</p>
                @endif
                @if($shop->rent_start_date)
                <p class="text-xs text-slate-500"><i class="fas fa-calendar-check text-emerald-400 mr-1"></i>Since {{ $shop->rent_start_date->format('d M Y') }}</p>
                @endif

                @if($shop->rent_status['has_start_date'])
                <div class="mt-2 {{ $shop->pending_months > 0 ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200' }} rounded-xl px-3 py-2">
                    @if($shop->pending_months > 0)
                    <p class="text-xs font-semibold text-red-700"><i class="fas fa-exclamation-circle mr-1"></i>{{ $shop->pending_months }} month(s) missed</p>
                    <p class="text-xs text-red-600">Rs {{ number_format($shop->pending_amount, 0) }} overdue</p>
                    @else
                    <p class="text-xs font-semibold text-emerald-700"><i class="fas fa-check-circle mr-1"></i>All rent paid ({{ $shop->months_due }} months)</p>
                    @endif
                </div>
                @elseif($shop->pending_amount > 0)
                <div class="mt-2 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                    <p class="text-xs text-amber-700">Rs {{ number_format($shop->pending_amount, 0) }} pending</p>
                </div>
                @endif

                @if($shop->notes)
                <p class="text-xs text-slate-400 mt-1">{{ $shop->notes }}</p>
                @endif
            </a>

            <!-- Footer -->
            <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <div class="flex gap-2" onclick="event.stopPropagation()">
                    @can('manage rent')
                    <button onclick="openEditShop({{ $shop->id }},'{{ addslashes($shop->shop_number) }}','{{ $shop->status }}',{{ $shop->rent_amount ?? 0 }},'{{ addslashes($shop->tenant_name ?? '') }}','{{ addslashes($shop->tenant_phone ?? '') }}','{{ addslashes($shop->tenant_cnic ?? '') }}','{{ addslashes($shop->notes ?? '') }}','{{ $shop->rent_start_date ? $shop->rent_start_date->format('Y-m-d') : '' }}')"
                            class="py-1.5 px-3 text-slate-600 hover:bg-slate-100 rounded-lg text-xs font-medium">
                        <i class="fas fa-pen mr-1"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('rent.shops.destroy', $shop) }}" onsubmit="return confirm('Delete shop?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash text-xs"></i></button>
                    </form>
                    @endcan
                </div>
                <a href="{{ route('rent.shops.show', $shop) }}" class="text-xs text-emerald-600 font-medium hover:underline">
                    View Details <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-5">{{ $shops->links() }}</div>
    @endif

    @can('manage rent')
    {{-- Add Shop Modal --}}
    <div id="modal-add-shop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-plus-circle text-emerald-500 mr-2"></i>Add Shop</h3>
                <button onclick="document.getElementById('modal-add-shop').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" action="{{ route('rent.shops.store', $rentMarket) }}" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Shop Number *</label>
                        <input type="text" name="shop_number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="e.g. R-01">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Property Dealer <span class="text-slate-400 text-xs">(optional)</span></label>
                    <input type="text" name="property_dealer" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Dealer / agent name">
                </div>

                <!-- Rent tracking -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 space-y-3">
                    <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide"><i class="fas fa-calendar-alt mr-1"></i>Rent Tracking</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Rent (Rs)</label>
                            <input type="number" name="rent_amount" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Rent Start Date</label>
                            <input type="date" name="rent_start_date" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="text-xs text-slate-400 mt-1">To track missed months</p>
                        </div>
                    </div>
                </div>

                <!-- Party / Customer search -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Party / Customer</label>
                    <div class="relative">
                        <input type="text" id="add-party-search" autocomplete="off"
                               placeholder="Search or type party name..."
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               oninput="rentPartySearch(this,'add')" onfocus="rentPartySearch(this,'add')">
                        <input type="hidden" name="tenant_name" id="add-party-name-hidden">
                        <input type="hidden" name="tenant_phone" id="add-party-phone-hidden">
                        <input type="hidden" name="tenant_cnic" id="add-party-cnic-hidden">
                        <div id="add-party-dd" class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-xl mt-1 max-h-52 overflow-y-auto"></div>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Select existing customer or just type a name directly</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" id="add-party-phone-display" name="_tenant_phone_display"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               oninput="document.getElementById('add-party-phone-hidden').value=this.value">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">CNIC</label>
                        <input type="text" id="add-party-cnic-display" name="_tenant_cnic_display"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="00000-0000000-0"
                               oninput="document.getElementById('add-party-cnic-hidden').value=this.value">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-shop').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Add Shop</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Shop Modal --}}
    <div id="modal-edit-shop" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-pen text-emerald-500 mr-2"></i>Edit Shop</h3>
                <button onclick="document.getElementById('modal-edit-shop').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i class="fas fa-times"></i></button>
            </div>
            <form method="POST" id="edit-shop-form" class="p-5 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" id="edit-rent-shop-base-url" value="{{ route('rent.shops.update', '__ID__') }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Shop Number *</label>
                        <input type="text" name="shop_number" id="edit-shop-number" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" id="edit-shop-status" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="available">Available</option>
                            <option value="rented">Rented</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Rent tracking -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 space-y-3">
                    <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide"><i class="fas fa-calendar-alt mr-1"></i>Rent Tracking</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Rent (Rs)</label>
                            <input type="number" name="rent_amount" id="edit-shop-rent" min="0" step="0.01" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Rent Start Date</label>
                            <input type="date" name="rent_start_date" id="edit-shop-start-date" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- Party / Customer search -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Party / Customer</label>
                    <div class="relative">
                        <input type="text" id="edit-party-search" autocomplete="off"
                               placeholder="Search or type party name..."
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               oninput="rentPartySearch(this,'edit')" onfocus="rentPartySearch(this,'edit')">
                        <input type="hidden" name="tenant_name" id="edit-party-name-hidden">
                        <input type="hidden" name="tenant_phone" id="edit-party-phone-hidden">
                        <input type="hidden" name="tenant_cnic" id="edit-party-cnic-hidden">
                        <div id="edit-party-dd" class="hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-xl mt-1 max-h-52 overflow-y-auto"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" id="edit-party-phone-display"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               oninput="document.getElementById('edit-party-phone-hidden').value=this.value">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">CNIC</label>
                        <input type="text" id="edit-party-cnic-display"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="00000-0000000-0"
                               oninput="document.getElementById('edit-party-cnic-hidden').value=this.value">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" id="edit-shop-notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-shop').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Update</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    // Customers for party search
    const rentPartyCustomers = <?php echo json_encode(($customers ?? collect())->map(fn($c)=>['id'=>$c->id,'name'=>$c->name,'phone'=>$c->phone??'','cnic'=>$c->cnic??''])->values()); ?>;

    function rentPartySearch(input, prefix) {
        const q  = input.value.trim().toLowerCase();
        const dd = document.getElementById(prefix + '-party-dd');
        const filtered = q.length === 0 ? rentPartyCustomers.slice(0, 25)
            : rentPartyCustomers.filter(r => r.name.toLowerCase().includes(q) || r.phone.includes(q) || r.cnic.includes(q));

        const rows = filtered.map(r => `
            <div class="flex items-center justify-between px-4 py-2.5 hover:bg-emerald-50 cursor-pointer border-b border-slate-50 last:border-0"
                 onmousedown="pickRentParty('${prefix}','${r.name.replace(/'/g,"\\'")}','${r.phone}','${r.cnic}')">
                <div>
                    <p class="font-medium text-slate-800 text-sm">${r.name}</p>
                    ${r.cnic ? `<p class="text-xs text-slate-400 font-mono">${r.cnic}</p>` : ''}
                </div>
                ${r.phone ? `<span class="text-xs text-emerald-600 font-medium">${r.phone}</span>` : ''}
            </div>`).join('');

        const createBtn = q.length > 1 ? `
            <div class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 cursor-pointer text-sm font-medium text-emerald-700 flex items-center gap-2 border-t border-emerald-100"
                 onmousedown="openRentCreateCustomer('${prefix}', '${q.replace(/'/g,"\\'")}')">
                <i class="fas fa-plus-circle"></i> Create new customer &ldquo;${q}&rdquo;
            </div>` : '';

        dd.innerHTML = (rows || `<div class="px-4 py-2.5 text-sm text-slate-400">No match found</div>`) + createBtn;
        dd.classList.remove('hidden');
    }

    function pickRentParty(prefix, name, phone, cnic) {
        document.getElementById(prefix + '-party-search').value        = name;
        document.getElementById(prefix + '-party-name-hidden').value   = name;
        document.getElementById(prefix + '-party-phone-hidden').value  = phone;
        document.getElementById(prefix + '-party-cnic-hidden').value   = cnic;
        document.getElementById(prefix + '-party-phone-display').value = phone;
        document.getElementById(prefix + '-party-cnic-display').value  = cnic;
        document.getElementById(prefix + '-party-dd').classList.add('hidden');
    }

    // Sync typed name to hidden field on blur
    document.addEventListener('DOMContentLoaded', function() {
        ['add','edit'].forEach(function(prefix) {
            var inp = document.getElementById(prefix + '-party-search');
            if (inp) {
                inp.addEventListener('blur', function() {
                    document.getElementById(prefix + '-party-name-hidden').value = this.value;
                });
            }
        });
    });

    document.addEventListener('click', function(e) {
        ['add-party-dd','edit-party-dd'].forEach(function(id) {
            var dd  = document.getElementById(id);
            var inp = document.getElementById(id.replace('-dd', '-search'));
            if (dd && inp && !inp.contains(e.target) && !dd.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });
    });

    // Track which prefix triggered the create flow
    let _rentCreatePrefix = null;

    function openRentCreateCustomer(prefix, name) {
        _rentCreatePrefix = prefix;
        document.getElementById('rent-new-customer-name').value = name;
        document.getElementById(prefix + '-party-dd').classList.add('hidden');
        document.getElementById('rent-modal-create-customer').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('rent-quick-customer-form');
        if (!form) return;
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn  = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';
            const fd = new FormData(form);
            try {
                const res  = await fetch('{{ route("customers.quick-store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                               'Accept': 'application/json' },
                    body: fd,
                });
                if (!res.ok) throw new Error();
                const c = await res.json();

                if (_rentCreatePrefix) {
                    pickRentParty(_rentCreatePrefix, c.name, c.phone, c.cnic);
                }
                document.getElementById('rent-modal-create-customer').classList.add('hidden');
                form.reset();

                const flash = document.createElement('div');
                flash.className = 'fixed top-4 right-4 z-[100] bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium';
                flash.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Customer "' + c.name + '" created and selected!';
                document.body.appendChild(flash);
                setTimeout(() => flash.remove(), 3000);
            } catch {
                alert('Could not save customer. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Save Customer';
            }
        });
    });

    function openEditShop(id, number, status, rent, tenant, phone, cnic, notes, startDate) {
        document.getElementById('edit-shop-number').value       = number;
        document.getElementById('edit-shop-status').value       = status;
        document.getElementById('edit-shop-rent').value         = rent;
        document.getElementById('edit-shop-notes').value        = notes;
        document.getElementById('edit-shop-start-date').value   = startDate || '';
        // Populate party fields
        document.getElementById('edit-party-search').value       = tenant;
        document.getElementById('edit-party-name-hidden').value  = tenant;
        document.getElementById('edit-party-phone-display').value = phone;
        document.getElementById('edit-party-phone-hidden').value  = phone;
        document.getElementById('edit-party-cnic-display').value  = cnic;
        document.getElementById('edit-party-cnic-hidden').value   = cnic;
        var base = document.getElementById('edit-rent-shop-base-url').value;
        document.getElementById('edit-shop-form').action = base.replace('__ID__', id);
        document.getElementById('modal-edit-shop').classList.remove('hidden');
    }
    </script>

    {{-- Quick Create Customer Modal (Rent) --}}
    <div id="rent-modal-create-customer" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-user-plus text-emerald-500 mr-2"></i>New Customer</h3>
                <button type="button" onclick="document.getElementById('rent-modal-create-customer').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="rent-quick-customer-form" class="p-5 space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="name" id="rent-new-customer-name" required
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-slate-700 mb-1">Father / Husband Name</label>
                        <input type="text" name="father_name"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">CNIC</label>
                        <input type="text" name="cnic" placeholder="00000-0000000-0"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-slate-700 mb-1">Address</label>
                        <input type="text" name="address"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('rent-modal-create-customer').classList.add('hidden')"
                            class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">
                        <i class="fas fa-save mr-1"></i>Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
