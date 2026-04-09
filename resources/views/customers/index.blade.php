<x-app-layout>
    <x-slot name="header">Customers</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, CNIC, phone..."
                   class="border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-60">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-sm text-slate-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
        @can('manage customers')
        <button onclick="document.getElementById('modal-add-customer').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-user-plus"></i> Add Customer
        </button>
        @endcan
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($customers as $customer)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden">
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-slate-800 truncate">{{ $customer->name }}</h3>
                        @if($customer->phone)
                        <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                            <i class="fas fa-phone text-indigo-400"></i> {{ $customer->phone }}
                        </p>
                        @endif
                    </div>
                </div>
                @if($customer->cnic)
                <p class="text-xs text-slate-500 flex items-center gap-1 mb-1">
                    <i class="fas fa-id-card text-slate-400"></i> {{ $customer->cnic }}
                </p>
                @endif
                @if($customer->shop)
                <p class="text-xs text-indigo-600 flex items-center gap-1 mb-1">
                    <i class="fas fa-store text-indigo-400"></i>
                    {{ $customer->shop->market->name ?? '' }} – Shop #{{ $customer->shop->shop_number }}
                </p>
                @endif
                @if($customer->address)
                <p class="text-xs text-slate-400 line-clamp-1 mt-1">
                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $customer->address }}
                </p>
                @endif
            </div>
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex gap-2">
                <a href="{{ route('customers.show', $customer) }}"
                   class="flex-1 text-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-medium transition-colors">
                    <i class="fas fa-eye mr-1"></i> View Profile
                </a>
                @can('manage customers')
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Delete customer?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <i class="fas fa-users text-slate-300 text-5xl mb-3"></i>
            <p class="text-slate-400">No customers found</p>
        </div>
        @endforelse
    </div>

    @if($customers->hasPages())
    <div class="mt-6">{{ $customers->links() }}</div>
    @endif

    <!-- Add Customer Modal -->
    @can('manage customers')
    <div id="modal-add-customer" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-user-plus text-indigo-500 mr-2"></i>Add Customer</h3>
                <button onclick="document.getElementById('modal-add-customer').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">CNIC</label>
                        <input type="text" name="cnic" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="00000-0000000-0">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                        <input type="text" name="address" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Link to Shop</label>
                        <select name="shop_id" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Optional —</option>
                            @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->market->name }} #{{ $shop->shop_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Documents / Files</label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-indigo-400 transition-colors">
                            <i class="fas fa-cloud-upload-alt text-slate-400 text-2xl mb-2"></i>
                            <p class="text-xs text-slate-500 mb-2">CNIC copies, agreements, photos</p>
                            <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" class="w-full text-xs text-slate-600">
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-customer').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Add Customer</button>
                </div>
            </form>
        </div>
    </div>
    @endcan
</x-app-layout>
