<x-app-layout>
    <x-slot name="header">Customer Profile</x-slot>

    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('customers.index') }}" class="hover:text-indigo-600">Customers</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">{{ $customer->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: Profile Info -->
        <div class="space-y-4">

            <!-- Profile Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-6 text-center">
                    <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3 text-3xl font-bold text-white">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                    <h2 class="text-xl font-bold text-white">{{ $customer->name }}</h2>
                    @if($customer->phone)
                    <p class="text-indigo-200 text-sm mt-1"><i class="fas fa-phone mr-1"></i>{{ $customer->phone }}</p>
                    @endif
                </div>
                <div class="p-4 space-y-3">
                    @if($customer->cnic)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <i class="fas fa-id-card text-indigo-500 w-4"></i>
                        <div>
                            <p class="text-xs text-slate-500">CNIC</p>
                            <p class="text-sm font-medium text-slate-800">{{ $customer->cnic }}</p>
                        </div>
                    </div>
                    @endif
                    @if($customer->email)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <i class="fas fa-envelope text-indigo-500 w-4"></i>
                        <div>
                            <p class="text-xs text-slate-500">Email</p>
                            <p class="text-sm font-medium text-slate-800">{{ $customer->email }}</p>
                        </div>
                    </div>
                    @endif
                    @if($customer->address)
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl">
                        <i class="fas fa-map-marker-alt text-indigo-500 w-4 mt-0.5"></i>
                        <div>
                            <p class="text-xs text-slate-500">Address</p>
                            <p class="text-sm font-medium text-slate-800">{{ $customer->address }}</p>
                        </div>
                    </div>
                    @endif
                    @if($customer->shop)
                    <div class="flex items-center gap-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                        <i class="fas fa-store text-indigo-600 w-4"></i>
                        <div>
                            <p class="text-xs text-indigo-500">Linked Shop</p>
                            <a href="{{ route('shops.show', $customer->shop) }}" class="text-sm font-semibold text-indigo-700 hover:underline">
                                {{ $customer->shop->market->name ?? '' }} – Shop #{{ $customer->shop->shop_number }}
                            </a>
                        </div>
                    </div>
                    @endif
                    @if($customer->linkedUser)
                    <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-xl border border-purple-100">
                        <i class="fas fa-user-circle text-purple-600 w-4"></i>
                        <div>
                            <p class="text-xs text-purple-500">Linked User</p>
                            <p class="text-sm font-semibold text-purple-700">{{ $customer->linkedUser->name }}</p>
                        </div>
                    </div>
                    @endif
                    @if($customer->notes)
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-100">
                        <p class="text-xs text-amber-600 font-medium mb-1">Notes</p>
                        <p class="text-sm text-amber-800">{{ $customer->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-paperclip text-indigo-500"></i> Documents
                </h3>
                @if($customer->documents->isEmpty())
                    <p class="text-xs text-slate-400 text-center py-3">No documents uploaded</p>
                @else
                <div class="space-y-2">
                    @foreach($customer->documents as $doc)
                    <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-xl">
                        <i class="fas {{ $doc->type === 'image' ? 'fa-image text-blue-500' : 'fa-file-alt text-slate-400' }} text-sm flex-shrink-0"></i>
                        @if($doc->type === 'image')
                        <a href="{{ Storage::url($doc->path) }}" target="_blank" class="flex-1 text-xs text-slate-600 truncate hover:text-indigo-600">{{ $doc->name }}</a>
                        @else
                        <a href="{{ Storage::url($doc->path) }}" download class="flex-1 text-xs text-slate-600 truncate hover:text-indigo-600">{{ $doc->name }}</a>
                        @endif
                        @can('manage customers')
                        <form method="POST" action="{{ route('customers.documents.destroy', $doc) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-times"></i></button>
                        </form>
                        @endcan
                    </div>
                    @endforeach
                </div>
                @endif

                @can('manage customers')
                <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data" class="mt-3">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $customer->name }}">
                    <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" class="w-full text-xs text-slate-600 mb-2">
                    <button type="submit" class="w-full py-2 border border-indigo-300 text-indigo-600 rounded-xl text-xs font-medium hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-upload mr-1"></i> Upload Files
                    </button>
                </form>
                @endcan
            </div>
        </div>

        <!-- Right: Shop Ledger -->
        <div class="lg:col-span-2 space-y-4">
            @if($customer->shop)
            <!-- Shop Payment Summary -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-book text-indigo-500"></i>
                    Payment Ledger — Shop #{{ $customer->shop->shop_number }}
                    <a href="{{ route('shops.show', $customer->shop) }}" class="ml-auto text-xs text-indigo-600 hover:underline font-normal">
                        Open Full Ledger →
                    </a>
                </h3>

                <div class="grid grid-cols-3 gap-3 mb-5">
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-blue-600 font-medium">Total</p>
                        <p class="text-base font-bold text-blue-700">Rs {{ number_format($customer->shop->total_amount, 0) }}</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-green-600 font-medium">Paid</p>
                        <p class="text-base font-bold text-green-700">Rs {{ number_format($customer->shop->paid_amount, 0) }}</p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-red-600 font-medium">Balance</p>
                        <p class="text-base font-bold text-red-700">Rs {{ number_format(max(0, $customer->shop->total_amount - $customer->shop->paid_amount), 0) }}</p>
                    </div>
                </div>

                @if($customer->shop->payments->isEmpty())
                <p class="text-center text-slate-400 text-sm py-6">No payment records</p>
                @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Date</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Receipt</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Method</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Amount</th>
                                <th class="text-right px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($customer->shop->payments->take(10) as $payment)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-slate-600">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-mono bg-indigo-50 text-indigo-700 px-2 py-1 rounded-lg">{{ $payment->receipt_number }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ ucfirst($payment->payment_method) }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-green-600">Rs {{ number_format($payment->amount, 0) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="text-xs text-indigo-500 hover:underline">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($customer->shop->payments->count() > 10)
                <p class="text-center text-xs text-slate-400 mt-3">Showing 10 of {{ $customer->shop->payments->count() }} entries</p>
                @endif
                @endif
            </div>
            @else
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <i class="fas fa-store-slash text-slate-300 text-4xl mb-3"></i>
                <p class="text-slate-400 text-sm">No shop linked to this customer</p>
                @can('manage customers')
                <button onclick="document.getElementById('modal-edit-customer').classList.remove('hidden')"
                        class="mt-3 px-4 py-2 btn-primary text-white rounded-xl text-sm font-medium">
                    Link a Shop
                </button>
                @endcan
            </div>
            @endif
        </div>
    </div>

    <!-- Edit Customer Modal -->
    @can('manage customers')
    <div id="modal-edit-customer" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-pen text-indigo-500 mr-2"></i>Edit Customer</h3>
                <button onclick="document.getElementById('modal-edit-customer').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required value="{{ $customer->name }}" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ $customer->phone }}" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">CNIC</label>
                        <input type="text" name="cnic" value="{{ $customer->cnic }}" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ $customer->email }}" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                        <input type="text" name="address" value="{{ $customer->address }}" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Link to Shop</label>
                        <select name="shop_id" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— None —</option>
                            @foreach(\App\Models\Shop::with('market')->get() as $shop)
                            <option value="{{ $shop->id }}" {{ $customer->shop_id == $shop->id ? 'selected' : '' }}>
                                {{ $shop->market->name }} #{{ $shop->shop_number }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Link to User</label>
                        <select name="linked_user_id" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— None —</option>
                            @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ $customer->linked_user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $customer->notes }}</textarea>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-customer').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endcan
</x-app-layout>
