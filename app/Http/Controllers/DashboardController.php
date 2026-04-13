<?php
namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Shop;
use App\Models\RentEntry;
use App\Models\RentShop;
use App\Models\RentMarket;
use App\Models\ShopPayment;
use App\Models\ConstructionItem;
use App\Models\Customer;
use App\Models\Owner;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Instalment pending: correct via instalmentStatus() ──────────
        $instalmentShops  = Shop::whereNull('deleted_at')
            ->whereRaw('paid_amount < total_amount')
            ->with([])
            ->get();

        $totalInstalmentPending = 0;
        $totalInstalmentShops   = 0;

        // ── Rent pending: correct via rentStatus() ──────────────────────
        $rentShopsAll = RentShop::with('rentEntries')->where('status', 'rented')->get();

        $totalRentPending = 0;
        $totalRentPendingShops = 0;

        // ── Rent by market ───────────────────────────────────────────────
        $rentMarkets = RentMarket::withCount('shops')
            ->with(['shops' => function($q) {
                $q->with('rentEntries');
            }])
            ->get()
            ->map(function($market) {
                $pendingShops  = 0;
                $pendingAmount = 0;
                $pendingMonths = 0;
                $paidAmount    = 0;

                foreach ($market->shops as $shop) {
                    $status = $shop->rentStatus();
                    if ($status['months_missed'] > 0 || $status['missed_amount'] > 0) {
                        $pendingShops++;
                        $pendingAmount += $status['missed_amount'];
                        $pendingMonths += $status['months_missed'];
                    }
                    $paidAmount += $status['paid_amount'];
                }

                return [
                    'id'             => $market->id,
                    'name'           => $market->name,
                    'total_shops'    => $market->shops_count,
                    'pending_shops'  => $pendingShops,
                    'pending_amount' => $pendingAmount,
                    'pending_months' => $pendingMonths,
                    'paid_amount'    => $paidAmount,
                ];
            })
            ->filter(fn($m) => $m['total_shops'] > 0);

        // Calculate global rent pending from markets
        $totalRentPending      = $rentMarkets->sum('pending_amount');
        $totalRentPendingShops = $rentMarkets->sum('pending_shops');

        // ── Instalment markets ──────────────────────────────────────────
        $instalmentMarkets = Market::withCount('shops')
            ->with(['shops'])
            ->get()
            ->map(function($market) {
                $pendingAmount = 0;
                $pendingShops  = 0;
                $paidAmount    = 0;
                $missedMonths  = 0;
                foreach ($market->shops as $shop) {
                    $status = $shop->instalmentStatus();
                    $paidAmount += $status['paid_amount'];
                    if ($status['missed_amount'] > 0 || $status['balance'] > 0) {
                        $pendingShops++;
                        $pendingAmount += $status['missed_amount'] ?: $status['balance'];
                        $missedMonths  += $status['months_missed'];
                    }
                }
                return [
                    'id'             => $market->id,
                    'name'           => $market->name,
                    'pending_shops'  => $pendingShops,
                    'pending_amount' => $pendingAmount,
                    'paid_amount'    => $paidAmount,
                    'pending_months' => $missedMonths,
                    'total_shops'    => $market->shops_count,
                ];
            })
            ->filter(fn($m) => $m['total_shops'] > 0);

        // ── Notifications: missed instalments + rent ─────────────────────
        $notifications = collect();
        foreach ($instalmentMarkets as $im) {
            if ($im['pending_shops'] > 0) {
                $notifications->push([
                    'type'    => 'instalment',
                    'message' => $im['name'] . ': ' . $im['pending_shops'] . ' shop(s) missed ' . $im['pending_months'] . ' instalment(s)',
                    'amount'  => $im['pending_amount'],
                    'id'      => $im['id'],
                ]);
            }
        }
        foreach ($rentMarkets as $rm) {
            if ($rm['pending_shops'] > 0) {
                $notifications->push([
                    'type'    => 'rent',
                    'message' => $rm['name'] . ': ' . $rm['pending_shops'] . ' shop(s) missed ' . $rm['pending_months'] . ' month(s) rent',
                    'amount'  => $rm['pending_amount'],
                    'id'      => $rm['id'],
                ]);
            }
        }

        $totalInstalmentPending = $instalmentMarkets->sum('pending_amount');
        $totalInstalmentShops   = $instalmentMarkets->sum('pending_shops');

        $stats = [
            'markets'             => Market::count(),
            'shops'               => Shop::count(),
            'rent_this_month'     => RentEntry::whereMonth('date', now()->month)
                                        ->whereYear('date', now()->year)
                                        ->sum('amount_paid'),
            'total_income'        => ShopPayment::sum('amount'),
            'pending'             => $totalInstalmentPending,
            'pending_shops_count' => $totalInstalmentShops,
            'rent_pending'        => $totalRentPending,
            'rent_pending_count'  => $totalRentPendingShops,
            'construction_total'  => ConstructionItem::sum('total'),
            'owners'              => Owner::count(),
            'customers'           => Customer::count(),
        ];

        $recentPayments = ShopPayment::with(['shop.market', 'shop.owner', 'recordedBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recentPayments', 'rentMarkets', 'instalmentMarkets', 'notifications'));
    }
}
