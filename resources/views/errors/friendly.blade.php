<x-app-layout>
    <x-slot name="header">Error</x-slot>

    <div class="max-w-lg mx-auto mt-12 text-center">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-10">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <p class="text-5xl font-black text-slate-200 mb-2">{{ $status }}</p>
            <h2 class="text-xl font-bold text-slate-800 mb-3">{{ $title }}</h2>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">{{ $message }}</p>
            <div class="flex gap-3 justify-center">
                <a href="javascript:history.back()"
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Go Back
                </a>
                <a href="{{ url('/') }}"
                   class="px-5 py-2.5 rounded-xl btn-primary text-white text-sm font-medium transition-colors">
                    <i class="fas fa-home mr-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
