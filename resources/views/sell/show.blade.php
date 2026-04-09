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
                </div>
                <h2 class="text-2xl font-bold">
                    Rs {{ number_format($entry->total, 0) }}
                </h2>
                <p class="text-{{ $color }}-200 text-sm mt-1">
                    <i class="fas fa-calendar mr-1"></i>{{ $entry->date->format('d M Y') }}
                    @if($entry->sellMarket)
                        &nbsp;·&nbsp;<i class="fas fa-building mr-1"></i>{{ $entry->sellMarket->name }}
                    @endif
                    @if($entry->shop_or_item_number)
                        &nbsp;·&nbsp;# {{ $entry->shop_or_item_number }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2 shrink-0">
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
                <div class="flex justify-between">
                    <span class="text-slate-500">Name</span>
                    <span class="font-medium text-slate-800">{{ $entry->seller_name ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">CNIC</span>
                    <span class="font-mono text-slate-700">{{ $entry->seller_cnic ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Phone</span>
                    <span class="text-slate-700">{{ $entry->seller_phone ?: '—' }}</span>
                </div>
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
                <div class="flex justify-between">
                    <span class="text-slate-500">Name</span>
                    <span class="font-medium text-slate-800">{{ $entry->buyer_name ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">CNIC</span>
                    <span class="font-mono text-slate-700">{{ $entry->buyer_cnic ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Phone</span>
                    <span class="text-slate-700">{{ $entry->buyer_phone ?: '—' }}</span>
                </div>
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
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Per Sqft Rate</p>
                <p class="font-semibold text-slate-800">Rs {{ number_format($entry->per_sqft_rate, 0) }}</p>
            </div>
            @endif
            @if($entry->sqft)
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Area</p>
                <p class="font-semibold text-slate-800">{{ number_format($entry->sqft, 2) }} sqft</p>
            </div>
            @endif
            @if($entry->shop_or_item_number)
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Shop / Plot #</p>
                <p class="font-semibold text-slate-800">{{ $entry->shop_or_item_number }}</p>
            </div>
            @endif
            @endif

            @if($entry->entry_type === 'car')
            @if($entry->car_make)
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Make</p>
                <p class="font-semibold text-slate-800">{{ $entry->car_make }}</p>
            </div>
            @endif
            @if($entry->car_model)
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Model</p>
                <p class="font-semibold text-slate-800">{{ $entry->car_model }}</p>
            </div>
            @endif
            @if($entry->car_year)
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Year</p>
                <p class="font-semibold text-slate-800">{{ $entry->car_year }}</p>
            </div>
            @endif
            @if($entry->car_registration)
            <div class="bg-slate-50 rounded-xl p-3">
                <p class="text-xs text-slate-500 mb-1">Registration</p>
                <p class="font-semibold text-slate-800 font-mono">{{ $entry->car_registration }}</p>
            </div>
            @endif
            @endif

            <div class="bg-indigo-50 rounded-xl p-3 col-span-2 sm:col-span-1">
                <p class="text-xs text-indigo-500 mb-1">Total Amount</p>
                <p class="font-bold text-indigo-700 text-lg">Rs {{ number_format($entry->total, 0) }}</p>
            </div>
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
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                    {{ $doc->type === 'image' ? 'bg-blue-100' : 'bg-slate-100' }}">
                    <i class="fas {{ $doc->type === 'image' ? 'fa-image text-blue-600' : 'fa-file-pdf text-slate-500' }} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-800 truncate">{{ $doc->name }}</p>
                    <a href="{{ Storage::url($doc->path) }}" target="_blank"
                       class="text-xs text-indigo-600 hover:underline">
                        {{ $doc->type === 'image' ? 'View Image' : 'Open File' }}
                    </a>
                </div>
                @can('manage sell purchase')
                <form method="POST" action="{{ route('sell.documents.destroy', $doc) }}" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="text-red-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </form>
                @endcan
            </div>
            @endforeach
        </div>
        @endif

        @can('manage sell purchase')
        <form method="POST" action="{{ route('sell.documents.store', $entry) }}" enctype="multipart/form-data" class="mt-5 pt-5 border-t border-slate-100">
            @csrf
            <p class="text-xs font-medium text-slate-600 mb-2">Upload More Files</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="file" name="documents[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                       class="flex-1 text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100">
                <button type="submit" class="px-5 py-2 btn-primary text-white rounded-xl text-sm font-medium shrink-0">
                    <i class="fas fa-upload mr-1"></i> Upload
                </button>
            </div>
        </form>
        @endcan
    </div>
</x-app-layout>
