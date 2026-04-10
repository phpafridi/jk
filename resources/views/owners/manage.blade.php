<x-app-layout>
    <x-slot name="header">Owner Management</x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <form method="GET" class="flex gap-2 flex-1 max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, CNIC..."
                   class="flex-1 border border-slate-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-sm text-slate-600">
                <i class="fas fa-search"></i>
            </button>
        </form>
        @can('manage owners')
        <button onclick="document.getElementById('modal-add-owner').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-5 py-2.5 btn-primary text-white rounded-xl text-sm font-medium shadow-sm">
            <i class="fas fa-plus"></i> Add Owner
        </button>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($owners->isEmpty())
        <div class="p-12 text-center">
            <i class="fas fa-user-tie text-slate-300 text-5xl mb-3"></i>
            <p class="text-slate-400 mb-2">No owners found</p>
            <p class="text-slate-300 text-sm">Owners are separate from system users. Add owners here to assign them to shops and rent entries.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Owner</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Phone</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">CNIC</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Shops</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Address</th>
                        <th class="text-right px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($owners as $owner)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($owner->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $owner->name }}</p>
                                    @if($owner->email)
                                    <p class="text-xs text-slate-400">{{ $owner->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600">
                            @if($owner->phone)
                            <a href="tel:{{ $owner->phone }}" class="hover:text-indigo-600">{{ $owner->phone }}</a>
                            @else <span class="text-slate-300">—</span> @endif
                        </td>
                        <td class="px-5 py-3 text-slate-500 text-xs font-mono">{{ $owner->cnic ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full font-medium">
                                {{ $owner->shops_count }} shop(s)
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-500 text-xs max-w-xs truncate">{{ $owner->address ?? '—' }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('owner-management.show', $owner) }}"
                                   class="text-violet-500 hover:text-violet-700 p-1.5 rounded-lg hover:bg-violet-50 transition-colors" title="View Profile">
                                    <i class="fas fa-user text-xs"></i>
                                </a>
                                @can('manage owners')
                                <button onclick="openEditOwner({{ $owner->id }}, '{{ addslashes($owner->name) }}', '{{ addslashes($owner->phone ?? '') }}', '{{ addslashes($owner->cnic ?? '') }}', '{{ addslashes($owner->address ?? '') }}', '{{ addslashes($owner->email ?? '') }}', '{{ addslashes($owner->notes ?? '') }}')"
                                        class="text-indigo-400 hover:text-indigo-600 p-1.5 rounded-lg hover:bg-indigo-50 transition-colors" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <form method="POST" action="{{ route('owner-management.destroy', $owner) }}" onsubmit="return confirm('Delete this owner?')">
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
        <div class="px-5 py-4">{{ $owners->links() }}</div>
        @endif
    </div>

    <!-- Add Owner Modal -->
    @can('manage owners')
    <div id="modal-add-owner" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-user-tie text-purple-500 mr-2"></i>Add New Owner</h3>
                <button onclick="document.getElementById('modal-add-owner').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('owner-management.store') }}" class="p-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Owner name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0300-1234567">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">CNIC</label>
                        <input type="text" name="cnic" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="00000-0000000-0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                        <input type="text" name="address" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-owner').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Add Owner</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Owner Modal -->
    <div id="modal-edit-owner" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl">
                <h3 class="font-semibold text-slate-800"><i class="fas fa-edit text-indigo-500 mr-2"></i>Edit Owner</h3>
                <button onclick="document.getElementById('modal-edit-owner').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="edit-owner-form" method="POST" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="name" id="edit-owner-name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" id="edit-owner-phone" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">CNIC</label>
                        <input type="text" name="cnic" id="edit-owner-cnic" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" id="edit-owner-email" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                        <input type="text" name="address" id="edit-owner-address" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" id="edit-owner-notes" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-owner').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-medium">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
    function openEditOwner(id, name, phone, cnic, address, email, notes) {
        document.getElementById('edit-owner-form').action = `/owner-management/${id}`;
        document.getElementById('edit-owner-name').value    = name;
        document.getElementById('edit-owner-phone').value   = phone;
        document.getElementById('edit-owner-cnic').value    = cnic;
        document.getElementById('edit-owner-address').value = address;
        document.getElementById('edit-owner-email').value   = email;
        document.getElementById('edit-owner-notes').value   = notes;
        document.getElementById('modal-edit-owner').classList.remove('hidden');
    }
    </script>
</x-app-layout>
