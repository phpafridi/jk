<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\SellPurchaseController;
use App\Http\Controllers\ConstructionController;
use App\Http\Controllers\OwnerLedgerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\OwnerManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Markets
    Route::get('/markets',             [MarketController::class, 'index'])->name('markets.index')->middleware('can:view markets');
    Route::post('/markets',            [MarketController::class, 'store'])->name('markets.store')->middleware('can:manage markets');
    Route::get('/markets/{market}',    [MarketController::class, 'show'])->name('markets.show')->middleware('can:view markets');
    Route::put('/markets/{market}',    [MarketController::class, 'update'])->name('markets.update')->middleware('can:manage markets');
    Route::delete('/markets/{market}', [MarketController::class, 'destroy'])->name('markets.destroy')->middleware('can:manage markets');

    // Shops
    Route::post('/markets/{market}/shops',      [ShopController::class, 'store'])->name('shops.store')->middleware('can:manage shops');
    Route::get('/shops/{shop}',                 [ShopController::class, 'show'])->name('shops.show')->middleware('can:view shops');
    Route::put('/shops/{shop}',                 [ShopController::class, 'update'])->name('shops.update')->middleware('can:manage shops');
    Route::delete('/shops/{shop}',              [ShopController::class, 'destroy'])->name('shops.destroy')->middleware('can:manage shops');
    Route::post('/shops/{shop}/payments',       [ShopController::class, 'addPayment'])->name('shops.payments.store')->middleware('can:manage shops');
    Route::get('/payments/{payment}/receipt',   [ShopController::class, 'printReceipt'])->name('payments.receipt')->middleware('can:view shops');
    Route::post('/shops/{shop}/documents',      [ShopController::class, 'uploadDocument'])->name('shops.documents.store')->middleware('can:manage shops');
    Route::delete('/shop-documents/{document}', [ShopController::class, 'deleteDocument'])->name('shops.documents.destroy')->middleware('can:manage shops');

    // Rent
    Route::get('/rent',                [RentController::class, 'index'])->name('rent.index')->middleware('can:view rent');
    Route::post('/rent',               [RentController::class, 'store'])->name('rent.store')->middleware('can:manage rent');
    Route::delete('/rent/{rentEntry}', [RentController::class, 'destroy'])->name('rent.destroy')->middleware('can:manage rent');

    // Sell / Purchase
    Route::get('/sell-purchase',            [SellPurchaseController::class, 'index'])->name('sell.index')->middleware('can:view sell purchase');
    Route::post('/sell-purchase',           [SellPurchaseController::class, 'store'])->name('sell.store')->middleware('can:manage sell purchase');
    Route::delete('/sell-purchase/{entry}', [SellPurchaseController::class, 'destroy'])->name('sell.destroy')->middleware('can:manage sell purchase');

    // Construction
    Route::get('/construction',           [ConstructionController::class, 'index'])->name('construction.index')->middleware('can:view construction');
    Route::post('/construction',          [ConstructionController::class, 'store'])->name('construction.store')->middleware('can:manage construction');
    Route::delete('/construction/{item}', [ConstructionController::class, 'destroy'])->name('construction.destroy')->middleware('can:manage construction');

    // Owner Ledger
    Route::get('/owners',                        [OwnerLedgerController::class, 'index'])->name('owners.index')->middleware('can:view owners');
    Route::post('/owners',                       [OwnerLedgerController::class, 'store'])->name('owners.store')->middleware('can:manage owners');
    Route::delete('/owner-ledger/{ownerLedger}', [OwnerLedgerController::class, 'destroy'])->name('owners.destroy')->middleware('can:manage owners');

    // Customers
    Route::get('/customers',                            [CustomerController::class, 'index'])->name('customers.index')->middleware('can:view customers');
    Route::post('/customers',                           [CustomerController::class, 'store'])->name('customers.store')->middleware('can:manage customers');
    Route::get('/customers/{customer}',                 [CustomerController::class, 'show'])->name('customers.show')->middleware('can:view customers');
    Route::put('/customers/{customer}',                 [CustomerController::class, 'update'])->name('customers.update')->middleware('can:manage customers');
    Route::delete('/customers/{customer}',              [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('can:manage customers');
    Route::delete('/customer-documents/{document}',     [CustomerController::class, 'deleteDocument'])->name('customers.documents.destroy')->middleware('can:manage customers');

    // Owner Management (standalone owners, separate from users)
    Route::get('/owner-management',          [OwnerManagementController::class, 'index'])->name('owner-management.index')->middleware('can:view owners');
    Route::post('/owner-management',         [OwnerManagementController::class, 'store'])->name('owner-management.store')->middleware('can:manage owners');
    Route::put('/owner-management/{owner}',  [OwnerManagementController::class, 'update'])->name('owner-management.update')->middleware('can:manage owners');
    Route::delete('/owner-management/{owner}',[OwnerManagementController::class, 'destroy'])->name('owner-management.destroy')->middleware('can:manage owners');

    // JSON search endpoints for select inputs
    Route::get('/search/owners',    [OwnerManagementController::class, 'search'])->name('search.owners');
    Route::get('/search/customers', [OwnerManagementController::class, 'searchCustomers'])->name('search.customers');

    // User Management
    Route::get('/users',                   [UserManagementController::class, 'index'])->name('users.index')->middleware('can:manage users');
    Route::post('/users',                  [UserManagementController::class, 'store'])->name('users.store')->middleware('can:manage users');
    Route::patch('/users/{user}/role',     [UserManagementController::class, 'updateRole'])->name('users.role')->middleware('can:manage users');
    Route::patch('/users/{user}/password', [UserManagementController::class, 'updatePassword'])->name('users.password')->middleware('can:manage users');
    Route::delete('/users/{user}',         [UserManagementController::class, 'destroy'])->name('users.destroy')->middleware('can:manage users');
});

require __DIR__.'/auth.php';
