<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e293b">

    <!-- PWA -->
    <link rel="manifest" href="{{ url('/manifest.json') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    <title>{{ $title ?? config('app.name', 'JK') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { width: 260px; transition: transform 0.3s ease; }
        @media (max-width: 1024px) {
            .sidebar { position: fixed; left: 0; top: 0; bottom: 0; z-index: 50; transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
        }
        .nav-item { transition: all 0.2s; }
        .nav-item:hover, .nav-item.active { background: rgba(99,102,241,.15); color: #6366f1; }
        .nav-item.active { border-right: 3px solid #6366f1; }
        .card-stat { transition: transform 0.2s, box-shadow 0.2s; }
        .card-stat:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,.1); }
        .modal-overlay { backdrop-filter: blur(4px); }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .btn-primary:hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Overlay (mobile) -->
    <div x-show="sidebarOpen" @click="sidebarOpen=false"
         class="fixed inset-0 bg-black/40 z-40 lg:hidden modal-overlay"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none;"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'open' : ''" class="sidebar bg-slate-900 text-slate-300 flex flex-col h-screen lg:sticky lg:top-0 shadow-2xl">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                <i class="fas fa-building text-white text-sm"></i>
            </div>
            <div>
                <p class="font-bold text-white text-sm leading-none">JK</p>

            </div>
            <button @click="sidebarOpen=false" class="ml-auto lg:hidden text-slate-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

            <!-- HOME Section -->
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mb-2">Home</p>

            <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-chart-pie w-5 text-center"></i> Dashboard
            </a>
            <a href="{{ route('markets.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('markets.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-store w-5 text-center"></i> Instalment Markets
            </a>
            <a href="{{ route('rent.markets.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('rent.markets.*') || request()->routeIs('rent.shops.*') || request()->routeIs('rent.entries.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-key w-5 text-center"></i> Rent Markets
            </a>
            <a href="{{ route('sell.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('sell.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-exchange-alt w-5 text-center"></i> Sell / Purchase
            </a>
            <a href="{{ route('construction.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('construction.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-hard-hat w-5 text-center"></i> Construction
            </a>
            <a href="{{ route('owners.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('owners.index') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-book w-5 text-center"></i> Owners Ledger
            </a>
            <a href="{{ route('owner-management.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('owner-management.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-user-tie w-5 text-center"></i> Manage Owners
            </a>

            <a href="{{ route('reports.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reports.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-chart-bar w-5 text-center"></i> Reports
            </a>

            <a href="{{ route('calculator') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('calculator') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-calculator w-5 text-center"></i> Calculator
            </a>

            <!-- CUSTOMERS Section -->
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mt-5 mb-2">Customers</p>

            <a href="{{ route('customers.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('customers.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-users w-5 text-center"></i> All Customers
            </a>

            @can('manage users')
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mt-5 mb-2">Admin</p>
            <a href="{{ route('users.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('users.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-user-shield w-5 text-center"></i> User Management
            </a>
            @endcan

            <!-- Install App (mobile only: Android & iOS) -->
            <div id="sidebar-install-section" style="display:none;">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-3 mt-5 mb-2">App</p>
                <button id="sidebar-install-btn"
                        onclick="sidebarInstallClick()"
                        class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium hover:text-white text-left"
                        style="display:none;">
                    <i class="fas fa-download w-5 text-center"></i> Install App
                </button>
                <div id="sidebar-install-ios" style="display:none;"
                     class="px-3 py-2.5 rounded-lg text-sm text-slate-400 leading-snug">
                    <i class="fas fa-download w-5 text-center mr-1"></i>
                    <span class="font-medium text-slate-300">Install App</span><br>
                    <span class="text-xs ml-6">Tap <strong>Share</strong> → <strong>Add to Home Screen</strong></span>
                </div>
            </div>
        </nav>

        <!-- User Info -->
        <div class="border-t border-slate-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->getRoleNames()->first() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 main-content">

        <!-- Top Bar -->
        <header class="bg-white border-b border-slate-200 px-4 py-3 flex items-center gap-4 sticky top-0 z-30 shadow-sm">
            <button @click="sidebarOpen=true" class="lg:hidden text-slate-500 hover:text-indigo-600 transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <div class="flex-1">
                @isset($header)
                    <h1 class="text-lg font-semibold text-slate-800">{{ $header }}</h1>
                @endisset
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden sm:block text-sm text-slate-500">{{ now()->format('D, M d Y') }}</span>

                {{-- Bell Notification Icon --}}
                @php
                    // Per-market missed instalment notifications
                    $bellInstMarkets = \App\Models\Market::with('shops')->get()->map(function($m) {
                        $missed = 0; $amount = 0; $months = 0;
                        foreach($m->shops as $s) {
                            $st = $s->instalmentStatus();
                            if($st['months_missed'] > 0) { $missed++; $amount += $st['missed_amount']; $months += $st['months_missed']; }
                        }
                        return ['name'=>$m->name,'id'=>$m->id,'shops'=>$missed,'amount'=>$amount,'months'=>$months];
                    })->filter(fn($x)=>$x['shops']>0);

                    // Per-market missed rent notifications
                    $bellRentMarkets = \App\Models\RentMarket::with(['shops.rentEntries'])->get()->map(function($m) {
                        $missed = 0; $amount = 0; $months = 0;
                        foreach($m->shops as $s) {
                            $st = $s->rentStatus();
                            if($st['months_missed'] > 0) { $missed++; $amount += $st['missed_amount']; $months += $st['months_missed']; }
                        }
                        return ['name'=>$m->name,'id'=>$m->id,'shops'=>$missed,'amount'=>$amount,'months'=>$months];
                    })->filter(fn($x)=>$x['shops']>0);

                    $bellTotal = $bellInstMarkets->count() + $bellRentMarkets->count();
                @endphp
                <div class="relative" x-data="{ bellOpen: false }">
                    <button @click="bellOpen = !bellOpen"
                            class="relative w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-bell text-base"></i>
                        @if($bellTotal > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center leading-none">
                            {{ $bellTotal > 99 ? '99+' : $bellTotal }}
                        </span>
                        @endif
                    </button>

                    {{-- Dropdown panel --}}
                    <div x-show="bellOpen" @click.away="bellOpen=false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-11 w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden"
                         style="display:none;">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <p class="font-semibold text-slate-800 text-sm">Pending Payments</p>
                            @if($bellTotal > 0)
                            <span class="text-xs bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">{{ $bellTotal }} alert{{ $bellTotal > 1 ? 's' : '' }}</span>
                            @else
                            <span class="text-xs text-emerald-600 font-medium">All clear ✓</span>
                            @endif
                        </div>

                        <div class="max-h-96 overflow-y-auto divide-y divide-slate-50">
                            {{-- Per-market instalment missed --}}
                            @foreach($bellInstMarkets as $bim)
                            <a href="{{ route('markets.show', $bim['id']) }}" @click="bellOpen=false"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-indigo-50 transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas fa-store text-indigo-600 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $bim['name'] }}</p>
                                    <p class="text-xs text-indigo-600">{{ $bim['shops'] }} shop(s) — {{ $bim['months'] }} instalment month(s) missed</p>
                                    <p class="text-xs font-semibold text-red-600">Rs {{ number_format($bim['amount'], 0) }} overdue</p>
                                </div>
                                <i class="fas fa-chevron-right text-slate-300 text-xs ml-auto mt-1 flex-shrink-0"></i>
                            </a>
                            @endforeach

                            {{-- Per-market rent missed --}}
                            @foreach($bellRentMarkets as $brm)
                            <a href="{{ route('rent.markets.show', $brm['id']) }}" @click="bellOpen=false"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-amber-50 transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas fa-key text-amber-600 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">{{ $brm['name'] }}</p>
                                    <p class="text-xs text-amber-600">{{ $brm['shops'] }} shop(s) — {{ $brm['months'] }} rent month(s) missed</p>
                                    <p class="text-xs font-semibold text-red-600">Rs {{ number_format($brm['amount'], 0) }} overdue</p>
                                </div>
                                <i class="fas fa-chevron-right text-slate-300 text-xs ml-auto mt-1 flex-shrink-0"></i>
                            </a>
                            @endforeach

                            @if($bellTotal == 0)
                            <div class="px-4 py-6 text-center">
                                <i class="fas fa-check-circle text-emerald-400 text-2xl mb-2"></i>
                                <p class="text-sm text-emerald-600 font-medium">No pending payments</p>
                                <p class="text-xs text-slate-400 mt-1">Everything is up to date</p>
                            </div>
                            @endif
                        </div>

                        <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50">
                            <a href="{{ route('dashboard') }}" @click="bellOpen=false" class="text-xs text-indigo-600 hover:underline font-medium">View full dashboard →</a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile.edit') }}" class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold hover:shadow-lg transition-shadow">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show=false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mx-4 mt-4 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            <span class="text-sm">{{ session('success') }}</span>
            <button @click="show=false" class="ml-auto text-green-600 hover:text-green-800">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        @endif

        @if(session('error') || $errors->any())
        <div class="mx-4 mt-4 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 flex items-start gap-2 shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <div class="text-sm">
                @if(session('error')){{ session('error') }}@endif
                @if($errors->any())
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        @endif

        <!-- Page Content -->
        <main class="flex-1 p-4 md:p-6">
            {{ $slot }}
        </main>

        <!-- Mobile Bottom Nav -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-30 safe-area-inset-bottom">
            <div class="flex justify-around py-2">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-500' }}">
                    <i class="fas fa-home text-lg"></i>
                    <span class="text-xs">Home</span>
                </a>
                <a href="{{ route('markets.index') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('markets.*') ? 'text-indigo-600' : 'text-slate-500' }}">
                    <i class="fas fa-store text-lg"></i>
                    <span class="text-xs">Markets</span>
                </a>
                <a href="{{ route('customers.index') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('customers.*') ? 'text-indigo-600' : 'text-slate-500' }}">
                    <i class="fas fa-users text-lg"></i>
                    <span class="text-xs">Customers</span>
                </a>
                <a href="{{ route('owners.index') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('owners.*') ? 'text-indigo-600' : 'text-slate-500' }}">
                    <i class="fas fa-user-tie text-lg"></i>
                    <span class="text-xs">Owners</span>
                </a>
                <button @click="sidebarOpen=true" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg text-slate-500">
                    <i class="fas fa-bars text-lg"></i>
                    <span class="text-xs">More</span>
                </button>
            </div>
        </nav>
        <div class="h-16 lg:hidden"></div><!-- spacer for bottom nav -->
    </div>
</div>

<!-- PWA Service Worker + Install Prompt -->
<script>
// ── Service Worker ───────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        const swUrl   = '{{ url("/sw.js") }}';
        const swScope = '{{ rtrim(parse_url(config("app.url"), PHP_URL_PATH) ?: "/", "/") }}/';
        navigator.serviceWorker.register(swUrl, { scope: swScope })
            .then(reg => console.log('[PWA] SW registered, scope:', reg.scope))
            .catch(err => console.error('[PWA] SW failed:', err));
    });
}

// ── Install Prompt (Android / Chrome / Edge) ─────────────────────────
let _deferredPrompt = null;

function _updateSidebarInstallBtn() {
    const section   = document.getElementById('sidebar-install-section');
    const btn       = document.getElementById('sidebar-install-btn');
    const iosDiv    = document.getElementById('sidebar-install-ios');
    const isIos     = /iphone|ipad|ipod/i.test(navigator.userAgent);
    const isAndroid = /android/i.test(navigator.userAgent);
    const isStandalone = window.navigator.standalone === true
                      || window.matchMedia('(display-mode: standalone)').matches;

    // Hide everything if already installed or not on mobile
    if (isStandalone || (!isIos && !isAndroid)) return;

    // Always show the section on mobile
    if (section) section.style.display = 'block';

    if (isIos) {
        if (iosDiv) iosDiv.style.display = 'block';
    } else {
        // Android: always show button; clicking will prompt if available
        if (btn) btn.style.display = 'flex';
    }
}

function sidebarInstallClick() {
    if (_deferredPrompt) {
        _deferredPrompt.prompt();
        _deferredPrompt.userChoice.then(() => {
            _deferredPrompt = null;
            document.getElementById('sidebar-install-section')?.remove();
        });
    } else {
        // Prompt not available yet — show browser tip
        alert('To install: tap the browser menu (⋮) and choose "Add to Home Screen".');
    }
}

window.addEventListener('beforeinstallprompt', (e) => {
    console.log('[PWA] beforeinstallprompt fired ✓');
    e.preventDefault();
    _deferredPrompt = e;
    _updateSidebarInstallBtn();
    const dismissed = localStorage.getItem('pwa-dismissed');
    if (dismissed && Date.now() - dismissed < 3 * 24 * 60 * 60 * 1000) return;
    _showInstallBanner('android');
});

window.addEventListener('appinstalled', () => {
    console.log('[PWA] App installed ✓');
    document.getElementById('pwa-install-banner')?.remove();
    document.getElementById('sidebar-install-section')?.remove();
    _deferredPrompt = null;
});

// ── Always show install button on mobile (Android & iOS) on load ─────
document.addEventListener('DOMContentLoaded', _updateSidebarInstallBtn);

// ── iOS Safari: no beforeinstallprompt, show manual instructions ──────
(function () {
    const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
    const isInStandalone = window.navigator.standalone === true;
    _updateSidebarInstallBtn();
    if (!isIos || isInStandalone) return;
    const dismissed = localStorage.getItem('pwa-ios-dismissed');
    if (dismissed && Date.now() - dismissed < 3 * 24 * 60 * 60 * 1000) return;
    // Small delay so page loads first
    setTimeout(() => _showInstallBanner('ios'), 2000);
})();

// ── Banner UI ────────────────────────────────────────────────────────
function _showInstallBanner(type) {
    if (document.getElementById('pwa-install-banner')) return;

    const isIos = type === 'ios';
    const banner = document.createElement('div');
    banner.id = 'pwa-install-banner';

    banner.innerHTML = `
        <style>
            @keyframes pwaUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
            #pwa-install-banner { position:fixed;bottom:72px;left:10px;right:10px;z-index:99999;animation:pwaUp .3s ease; }
            @media(min-width:600px){#pwa-install-banner{left:auto;right:20px;width:340px;bottom:20px;}}
        </style>
        <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:18px;
                    padding:14px 16px;display:flex;align-items:flex-start;gap:12px;
                    box-shadow:0 8px 32px rgba(99,102,241,.5);font-family:'Plus Jakarta Sans',sans-serif;">
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.2);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    ${isIos
                        ? '<path d="M12 3v13M8 9l4-6 4 6"/><rect x="3" y="19" width="18" height="2" rx="1"/>'
                        : '<path d="M12 3v13M8 10l4 5 4-5"/><rect x="3" y="19" width="18" height="2" rx="1"/>'}
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-weight:700;font-size:14px;margin:0 0 4px;">Install {{ config('app.name') }}</p>
                ${isIos
                    ? `<p style="font-size:12px;opacity:.9;margin:0;line-height:1.5;">
                            Tap the <strong>Share</strong> button
                            <svg style="display:inline;vertical-align:middle" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M8 12H3v9h18v-9h-5M12 3v13M9 6l3-3 3 3"/></svg>
                            then <strong>"Add to Home Screen"</strong>
                       </p>`
                    : `<p style="font-size:12px;opacity:.9;margin:0;">Add to home screen for fast access</p>`
                }
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0;">
                ${isIos
                    ? ''
                    : `<button id="pwa-install-btn" style="background:#fff;color:#6366f1;border:none;cursor:pointer;
                            font-weight:700;font-size:13px;padding:7px 14px;border-radius:9px;
                            font-family:inherit;white-space:nowrap;">Install</button>`
                }
                <button id="pwa-dismiss-btn" style="background:rgba(255,255,255,.18);color:#fff;border:none;
                        cursor:pointer;font-size:12px;padding:5px 10px;border-radius:8px;font-family:inherit;">
                    ${isIos ? 'Got it' : 'Not now'}
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(banner);

    document.getElementById('pwa-install-btn')?.addEventListener('click', async () => {
        if (!_deferredPrompt) return;
        _deferredPrompt.prompt();
        await _deferredPrompt.userChoice;
        _deferredPrompt = null;
        banner.remove();
    });

    document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
        localStorage.setItem(isIos ? 'pwa-ios-dismissed' : 'pwa-dismissed', Date.now());
        banner.remove();
    });
}
</script>
</body>
</html>
