<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e293b">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <title>{{ $title ?? config('app.name', 'PropManager') }}</title>

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
                <p class="font-bold text-white text-sm leading-none">PropManager</p>
                <p class="text-xs text-slate-400 mt-0.5">Property Management</p>
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
            <a href="{{ route('rent.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('rent.*') ? 'active text-indigo-400' : 'hover:text-white' }}">
                <i class="fas fa-key w-5 text-center"></i> Rent
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

<!-- PWA Service Worker -->
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(console.error);
}
</script>
</body>
</html>
