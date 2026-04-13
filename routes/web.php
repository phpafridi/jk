<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SellPurchaseController;
use App\Http\Controllers\ConstructionController;
use App\Http\Controllers\OwnerLedgerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\OwnerManagementController;
use App\Http\Controllers\RentMarketController;
use App\Http\Controllers\SellMarketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Calculator
    Route::get('/calculator', fn() => view('calculator'))->name('calculator');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Markets (instalment shops) ────────────────────────────
    Route::get('/markets',             [MarketController::class, 'index'])->name('markets.index')->middleware('can:view markets');
    Route::post('/markets',            [MarketController::class, 'store'])->name('markets.store')->middleware('can:manage markets');
    Route::get('/markets/{market}',    [MarketController::class, 'show'])->name('markets.show')->middleware('can:view markets');
    Route::put('/markets/{market}',    [MarketController::class, 'update'])->name('markets.update')->middleware('can:manage markets');
    Route::delete('/markets/{market}', [MarketController::class, 'destroy'])->name('markets.destroy')->middleware('can:manage markets');

    // Shops (instalment)
    Route::post('/markets/{market}/shops',      [ShopController::class, 'store'])->name('shops.store')->middleware('can:manage shops');
    Route::get('/shops/{shop}',                 [ShopController::class, 'show'])->name('shops.show')->middleware('can:view shops');
    Route::put('/shops/{shop}',                 [ShopController::class, 'update'])->name('shops.update')->middleware('can:manage shops');
    Route::delete('/shops/{shop}',              [ShopController::class, 'destroy'])->name('shops.destroy')->middleware('can:manage shops');
    Route::post('/shops/{shop}/payments',       [ShopController::class, 'addPayment'])->name('shops.payments.store')->middleware('can:manage shops');
    Route::get('/payments/{payment}/receipt',   [ShopController::class, 'printReceipt'])->name('payments.receipt')->middleware('can:view shops');
    Route::post('/shops/{shop}/documents',      [ShopController::class, 'uploadDocument'])->name('shops.documents.store')->middleware('can:manage shops');
    Route::delete('/shop-documents/{document}', [ShopController::class, 'deleteDocument'])->name('shops.documents.destroy')->middleware('can:manage shops');

    // ── Rent Markets ──────────────────────────────────────────
    Route::get('/rent-markets',                  [RentMarketController::class, 'index'])->name('rent.markets.index')->middleware('can:view rent');
    Route::post('/rent-markets',                 [RentMarketController::class, 'storeMarket'])->name('rent.markets.store')->middleware('can:manage rent');
    Route::put('/rent-markets/{rentMarket}',     [RentMarketController::class, 'updateMarket'])->name('rent.markets.update')->middleware('can:manage rent');
    Route::delete('/rent-markets/{rentMarket}',  [RentMarketController::class, 'destroyMarket'])->name('rent.markets.destroy')->middleware('can:manage rent');
    Route::get('/rent-markets/{rentMarket}',     [RentMarketController::class, 'showMarket'])->name('rent.markets.show')->middleware('can:view rent');

    // Rent Shops (inside a market)
    Route::post('/rent-markets/{rentMarket}/shops',  [RentMarketController::class, 'storeShop'])->name('rent.shops.store')->middleware('can:manage rent');
    Route::get('/rent-shops/{rentShop}',             [RentMarketController::class, 'showShop'])->name('rent.shops.show')->middleware('can:view rent');
    Route::put('/rent-shops/{rentShop}',             [RentMarketController::class, 'updateShop'])->name('rent.shops.update')->middleware('can:manage rent');
    Route::delete('/rent-shops/{rentShop}',          [RentMarketController::class, 'destroyShop'])->name('rent.shops.destroy')->middleware('can:manage rent');

    // Rent Entries (live inside shop detail page)
    Route::post('/rent-shops/{rentShop}/entries',    [RentMarketController::class, 'storeEntry'])->name('rent.entries.store')->middleware('can:manage rent');
    Route::delete('/rent-entries/{rentEntry}',       [RentMarketController::class, 'destroyEntry'])->name('rent.entries.destroy')->middleware('can:manage rent');
    Route::get('/rent-entries/{rentEntry}/receipt',  [RentMarketController::class, 'printEntryReceipt'])->name('rent.entries.receipt')->middleware('can:view rent');

    // Rent Shop Documents
    Route::post('/rent-shops/{rentShop}/documents',      [RentMarketController::class, 'uploadDocument'])->name('rent.shops.documents.store')->middleware('can:manage rent');
    Route::delete('/rent-shop-documents/{document}',     [RentMarketController::class, 'deleteDocument'])->name('rent.shops.documents.destroy')->middleware('can:manage rent');

    // ── Sell / Purchase (with built-in sell market management) ─
    Route::get('/sell-purchase',                              [SellPurchaseController::class, 'index'])->name('sell.index')->middleware('can:view sell purchase');
    Route::post('/sell-purchase',                             [SellPurchaseController::class, 'store'])->name('sell.store')->middleware('can:manage sell purchase');
    Route::get('/sell-purchase/{entry}',                      [SellPurchaseController::class, 'show'])->name('sell.show')->middleware('can:view sell purchase');
    Route::get('/sell-purchase/{entry}/receipt',              [SellPurchaseController::class, 'printReceipt'])->name('sell.receipt')->middleware('can:view sell purchase');
    Route::delete('/sell-purchase/{entry}',                   [SellPurchaseController::class, 'destroy'])->name('sell.destroy')->middleware('can:manage sell purchase');
    Route::post('/sell-purchase/{entry}/documents',           [SellPurchaseController::class, 'uploadDocument'])->name('sell.documents.store')->middleware('can:manage sell purchase');
    Route::delete('/sell-purchase-documents/{document}',      [SellPurchaseController::class, 'deleteDocument'])->name('sell.documents.destroy')->middleware('can:manage sell purchase');

    // Sell Markets (managed from WITHIN sell/purchase, no separate nav item needed)
    Route::get('/sell-markets',                          [SellMarketController::class, 'index'])->name('sell.markets.index')->middleware('can:view sell purchase');
    Route::get('/sell-markets/{sellMarket}',             [SellMarketController::class, 'showMarket'])->name('sell.markets.show')->middleware('can:view sell purchase');
    Route::post('/sell-markets',                         [SellMarketController::class, 'storeMarket'])->name('sell.markets.store')->middleware('can:manage sell purchase');
    Route::put('/sell-markets/{sellMarket}',             [SellMarketController::class, 'updateMarket'])->name('sell.markets.update')->middleware('can:manage sell purchase');
    Route::delete('/sell-markets/{sellMarket}',          [SellMarketController::class, 'destroyMarket'])->name('sell.markets.destroy')->middleware('can:manage sell purchase');

    // ── Construction (project-based) ──────────────────────────
    Route::get('/construction',                              [ConstructionController::class, 'index'])->name('construction.index')->middleware('can:view construction');
    Route::get('/construction/project/{project}',            [ConstructionController::class, 'show'])->name('construction.show')->middleware('can:view construction');
    Route::post('/construction/projects',                    [ConstructionController::class, 'storeProject'])->name('construction.projects.store')->middleware('can:manage construction');
    Route::delete('/construction/projects/{project}',        [ConstructionController::class, 'destroyProject'])->name('construction.projects.destroy')->middleware('can:manage construction');
    Route::post('/construction',                             [ConstructionController::class, 'store'])->name('construction.store')->middleware('can:manage construction');
    Route::delete('/construction/{item}',                    [ConstructionController::class, 'destroy'])->name('construction.destroy')->middleware('can:manage construction');

    // ── Owner Ledger (uses owners table) ──────────────────────
    Route::get('/owners',                        [OwnerLedgerController::class, 'index'])->name('owners.index')->middleware('can:view owners');
    Route::post('/owners',                       [OwnerLedgerController::class, 'store'])->name('owners.store')->middleware('can:manage owners');
    Route::delete('/owner-ledger/{ownerLedger}', [OwnerLedgerController::class, 'destroy'])->name('owners.destroy')->middleware('can:manage owners');

    // ── Owner Management (CRUD for owners) ────────────────────
    Route::get('/owner-management',                              [OwnerManagementController::class, 'index'])->name('owner-management.index')->middleware('can:view owners');
    Route::get('/owner-management/{owner}',                      [OwnerManagementController::class, 'show'])->name('owner-management.show')->middleware('can:view owners');
    Route::post('/owner-management',                             [OwnerManagementController::class, 'store'])->name('owner-management.store')->middleware('can:manage owners');
    Route::put('/owner-management/{owner}',                      [OwnerManagementController::class, 'update'])->name('owner-management.update')->middleware('can:manage owners');
    Route::delete('/owner-management/{owner}',                   [OwnerManagementController::class, 'destroy'])->name('owner-management.destroy')->middleware('can:manage owners');
    Route::post('/owner-management/{owner}/documents',           [OwnerManagementController::class, 'uploadDocument'])->name('owner-management.documents.store')->middleware('can:manage owners');
    Route::delete('/owner-documents/{document}',                 [OwnerManagementController::class, 'deleteDocument'])->name('owner-management.documents.destroy')->middleware('can:manage owners');

    // JSON search endpoints
    Route::get('/search/owners',    [OwnerManagementController::class, 'search'])->name('search.owners');
    Route::get('/search/customers', [OwnerManagementController::class, 'searchCustomers'])->name('search.customers');

    // ── Customers ─────────────────────────────────────────────
    Route::get('/customers',                         [CustomerController::class, 'index'])->name('customers.index')->middleware('can:view customers');
    Route::post('/customers',                        [CustomerController::class, 'store'])->name('customers.store')->middleware('can:manage customers');
    Route::post('/customers/quick-store',            [CustomerController::class, 'quickStore'])->name('customers.quick-store')->middleware('can:manage customers');
    Route::get('/customers/{customer}',              [CustomerController::class, 'show'])->name('customers.show')->middleware('can:view customers');
    Route::put('/customers/{customer}',              [CustomerController::class, 'update'])->name('customers.update')->middleware('can:manage customers');
    Route::delete('/customers/{customer}',           [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('can:manage customers');
    Route::post('/customers/{customer}/documents',   [CustomerController::class, 'uploadDocument'])->name('customers.documents.store')->middleware('can:manage customers');
    Route::delete('/customer-documents/{document}',  [CustomerController::class, 'deleteDocument'])->name('customers.documents.destroy')->middleware('can:manage customers');

    // ── User Management ───────────────────────────────────────
    Route::get('/users',                   [UserManagementController::class, 'index'])->name('users.index')->middleware('can:manage users');
    Route::post('/users',                  [UserManagementController::class, 'store'])->name('users.store')->middleware('can:manage users');
    Route::patch('/users/{user}/role',     [UserManagementController::class, 'updateRole'])->name('users.role')->middleware('can:manage users');
    Route::patch('/users/{user}/password', [UserManagementController::class, 'updatePassword'])->name('users.password')->middleware('can:manage users');
    Route::delete('/users/{user}',         [UserManagementController::class, 'destroy'])->name('users.destroy')->middleware('can:manage users');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
