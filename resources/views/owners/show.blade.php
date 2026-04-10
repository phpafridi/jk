<x-app-layout>
    <x-slot name="header">Owner Profile</x-slot>

    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-5">
        <a href="{{ route('owner-management.index') }}" class="hover:text-indigo-600 transition-colors">Owners</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-medium">{{ $owner->name }}</span>
    </nav>

    @if(session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-4 py-3 text-sm">
        <i class="fas fa-check-circle text-green-500"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left Column ── --}}
        <div class="space-y-4">

            {{-- Profile Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-br from-violet-600 to-purple-800 p-6 text-center">
                    <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3 text-3xl font-bold text-white">
                        {{ substr($owner->name, 0, 1) }}
                    </div>
                    <h2 class="text-xl font-bold text-white">{{ $owner->name }}</h2>
                    @if($owner->phone)
                    <p class="text-violet-200 text-sm mt-1"><i class="fas fa-phone mr-1"></i>{{ $owner->phone }}</p>
                    @endif
                </div>

                <div class="p-4 space-y-2.5">
                    @if($owner->cnic)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <i class="fas fa-id-card text-violet-500 w-4"></i>
                        <div>
                            <p class="text-xs text-slate-500">CNIC</p>
                            <p class="text-sm font-medium text-slate-800 font-mono">{{ $owner->cnic }}</p>
                        </div>
                    </div>
                    @endif
                    @if($owner->email)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <i class="fas fa-envelope text-violet-500 w-4"></i>
                        <div>
                            <p class="text-xs text-slate-500">Email</p>
                            <p class="text-sm font-medium text-slate-800">{{ $owner->email }}</p>
                        </div>
                    </div>
                    @endif
                    @if($owner->address)
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl">
                        <i class="fas fa-map-marker-alt text-violet-500 w-4 mt-0.5"></i>
                        <div>
                            <p class="text-xs text-slate-500">Address</p>
                            <p class="text-sm font-medium text-slate-800">{{ $owner->address }}</p>
                        </div>
                    </div>
                    @endif
                    @if($owner->notes)
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-100">
                        <p class="text-xs text-amber-600 font-medium mb-1">Notes</p>
                        <p class="text-sm text-amber-800">{{ $owner->notes }}</p>
                    </div>
                    @endif
                </div>

                @can('manage owners')
                <div class="px-4 pb-4">
                    <button onclick="document.getElementById('modal-edit-owner').classList.remove('hidden')"
                            class="w-full py-2 border border-violet-300 text-violet-600 rounded-xl text-sm font-medium hover:bg-violet-50 transition-colors">
                        <i class="fas fa-edit mr-1"></i> Edit Owner
                    </button>
                </div>
                @endcan
            </div>

            {{-- Documents Panel --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-folder-open text-violet-500"></i>
                    Documents & Files
                    <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $owner->documents->count() }}</span>
                </h3>

                @php
                    $docGroups = $owner->documents->groupBy('doc_type');
                    $images    = $owner->documents->where('type','image');
                    $files     = $owner->documents->where('type','document');

                    $docTypeLabels = [
                        'cnic'      => ['label' => 'CNIC',      'icon' => 'fa-id-card',     'color' => 'blue'],
                        'mou'       => ['label' => 'MOU',       'icon' => 'fa-handshake',   'color' => 'green'],
                        'agreement' => ['label' => 'Agreement', 'icon' => 'fa-file-contract','color' => 'amber'],
                        'photo'     => ['label' => 'Photos',    'icon' => 'fa-image',        'color' => 'violet'],
                        'other'     => ['label' => 'Other',     'icon' => 'fa-paperclip',   'color' => 'slate'],
                    ];
                @endphp

                {{-- Grouped document badges --}}
                @if($owner->documents->isNotEmpty())
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @foreach($docGroups as $type => $docs)
                    @php $meta = $docTypeLabels[$type] ?? $docTypeLabels['other']; @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-{{ $meta['color'] }}-50 text-{{ $meta['color'] }}-700 text-xs rounded-full border border-{{ $meta['color'] }}-200">
                        <i class="fas {{ $meta['icon'] }} text-xs"></i>
                        {{ $meta['label'] }} ({{ $docs->count() }})
                    </span>
                    @endforeach
                </div>

                {{-- Image grid --}}
                @if($images->isNotEmpty())
                <div class="mb-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Photos & Images</p>
                    <div class="grid grid-cols-3 gap-1.5">
                        @foreach($images as $doc)
                        <div class="relative group aspect-square rounded-xl overflow-hidden bg-slate-100">
                            <a href="{{ Storage::url($doc->path) }}" target="_blank">
                                <img src="{{ Storage::url($doc->path) }}" alt="{{ $doc->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            </a>
                            <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-[9px] px-1 py-0.5 truncate">
                                {{ ($docTypeLabels[$doc->doc_type] ?? $docTypeLabels['other'])['label'] }}
                            </div>
                            @can('manage owners')
                            <form method="POST" action="{{ route('owner-management.documents.destroy', $doc) }}"
                                  onsubmit="return confirm('Delete this file?')"
                                  class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs flex items-center justify-center shadow">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- File list --}}
                @if($files->isNotEmpty())
                <div class="mb-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Documents</p>
                    <div class="space-y-1.5">
                        @foreach($files as $doc)
                        @php $meta = $docTypeLabels[$doc->doc_type] ?? $docTypeLabels['other']; @endphp
                        <div class="flex items-center gap-2 p-2.5 bg-slate-50 rounded-xl">
                            <span class="w-6 h-6 rounded-lg bg-{{ $meta['color'] }}-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $meta['icon'] }} text-{{ $meta['color'] }}-600 text-xs"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <a href="{{ Storage::url($doc->path) }}" target="_blank"
                                   class="text-xs text-slate-700 hover:text-violet-600 font-medium truncate block">{{ $doc->name }}</a>
                                <span class="text-[10px] text-slate-400">{{ $meta['label'] }}</span>
                            </div>
                            @can('manage owners')
                            <form method="POST" action="{{ route('owner-management.documents.destroy', $doc) }}"
                                  onsubmit="return confirm('Delete this document?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs w-6 h-6 flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @else
                <div class="text-center py-6">
                    <i class="fas fa-folder-open text-slate-200 text-3xl mb-2"></i>
                    <p class="text-xs text-slate-400">No documents uploaded yet</p>
                    <p class="text-[11px] text-slate-300 mt-1">Upload CNIC, MOU, agreements below</p>
                </div>
                @endif

                {{-- Upload Form --}}
                @can('manage owners')
                <form method="POST" action="{{ route('owner-management.documents.store', $owner) }}"
                      enctype="multipart/form-data" class="mt-3 border-t border-slate-100 pt-3 space-y-2">
                    @csrf
                    <label class="block text-xs font-semibold text-slate-600">Document Type</label>
                    <select name="doc_type"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-violet-500 text-slate-700">
                        <option value="cnic">🪪 CNIC</option>
                        <option value="mou">🤝 MOU (Memorandum of Understanding)</option>
                        <option value="agreement">📋 Agreement / Contract</option>
                        <option value="photo">🖼 Photo</option>
                        <option value="other">📎 Other</option>
                    </select>
                    <label class="block text-xs font-semibold text-slate-600">Select Files</label>
                    <input type="file" name="documents[]" multiple id="owner-file-input"
                           accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx"
                           class="hidden" onchange="updateOwnerLabel(this)">
                    <label for="owner-file-input"
                           class="flex flex-col items-center justify-center gap-1.5 border-2 border-dashed border-slate-300 hover:border-violet-400 rounded-xl p-4 cursor-pointer transition-colors text-center">
                        <i class="fas fa-cloud-upload-alt text-slate-300 text-2xl"></i>
                        <span id="owner-file-label" class="text-xs text-slate-500">Click to choose files (PDF, images, docs)</span>
                    </label>
                    <button type="submit"
                            class="w-full py-2 rounded-xl btn-primary text-white text-sm font-semibold flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-shadow">
                        <i class="fas fa-upload"></i> Upload Document
                    </button>
                </form>
                @endcan
            </div>
        </div>

        {{-- ── Right Column ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Linked Shops --}}
            @if($owner->shops->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-store text-violet-500"></i> Linked Shops ({{ $owner->shops->count() }})
                </h3>
                <div class="space-y-2">
                    @foreach($owner->shops as $shop)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-store text-violet-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $shop->market->name ?? 'Market' }} — Shop #{{ $shop->shop_number }}
                            </p>
                            <p class="text-xs text-slate-500">
                                Total: Rs {{ number_format($shop->total_amount, 0) }} &nbsp;·&nbsp;
                                Status: <span class="capitalize">{{ $shop->status ?? 'active' }}</span>
                            </p>
                        </div>
                        <a href="{{ route('shops.show', $shop) }}"
                           class="text-xs text-violet-600 hover:underline font-medium whitespace-nowrap">View →</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Ledger --}}
            @if($owner->ledgers->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-book text-violet-500"></i> Recent Ledger Entries
                    <a href="{{ route('owners.index', ['owner_id' => $owner->id]) }}"
                       class="ml-auto text-xs text-violet-600 hover:underline font-normal">View All →</a>
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left">
                                <th class="pb-2 text-xs text-slate-500 font-semibold">Date</th>
                                <th class="pb-2 text-xs text-slate-500 font-semibold">Description</th>
                                <th class="pb-2 text-xs text-slate-500 font-semibold text-right">Amount</th>
                                <th class="pb-2 text-xs text-slate-500 font-semibold text-center">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($owner->ledgers as $ledger)
                            <tr>
                                <td class="py-2.5 text-slate-600 text-xs whitespace-nowrap">{{ $ledger->date->format('d M Y') }}</td>
                                <td class="py-2.5 text-slate-700 text-xs">{{ $ledger->description ?? '—' }}</td>
                                <td class="py-2.5 text-right font-semibold text-xs whitespace-nowrap
                                    {{ $ledger->transaction_type === 'credit' ? 'text-green-600' : 'text-red-500' }}">
                                    Rs {{ number_format($ledger->amount, 0) }}
                                </td>
                                <td class="py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $ledger->transaction_type === 'credit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ ucfirst($ledger->transaction_type) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">
                <i class="fas fa-book-open text-slate-200 text-4xl mb-3"></i>
                <p class="text-slate-400 text-sm">No ledger entries yet</p>
                <a href="{{ route('owners.index') }}" class="mt-3 inline-block text-xs text-violet-600 hover:underline">Go to Owner Ledger →</a>
            </div>
            @endif

        </div>
    </div>

    {{-- Edit Modal --}}
    @can('manage owners')
    <div id="modal-edit-owner" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Edit Owner</h3>
                <button onclick="document.getElementById('modal-edit-owner').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('owner-management.update', $owner) }}" class="p-5 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ $owner->name }}" required
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ $owner->phone }}"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">CNIC</label>
                        <input type="text" name="cnic" value="{{ $owner->cnic }}"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500"
                               placeholder="00000-0000000-0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $owner->email }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Address</label>
                    <input type="text" name="address" value="{{ $owner->address }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">{{ $owner->notes }}</textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl btn-primary text-white text-sm font-semibold shadow-sm">
                        Save Changes
                    </button>
                    <button type="button"
                            onclick="document.getElementById('modal-edit-owner').classList.add('hidden')"
                            class="flex-1 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <script>
        function updateOwnerLabel(input) {
            const label = document.getElementById('owner-file-label');
            if (input.files.length > 0) {
                const names = Array.from(input.files).map(f => f.name).join(', ');
                label.textContent = input.files.length === 1 ? names : `${input.files.length} files selected`;
                label.classList.add('text-violet-600');
            }
        }
    </script>
</x-app-layout>
