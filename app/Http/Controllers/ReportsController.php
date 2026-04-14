<?php
namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Shop;
use App\Models\RentMarket;
use App\Models\RentShop;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->get('type', 'rent_available');

        // ── RENT REPORTS ────────────────────────────────────────────────

        // 1. Rented shops
        $rentedShops = RentShop::with(['rentMarket'])
            ->where('status', 'rented')
            ->latest()
            ->get();

        // 2. Available rent shops
        $availableRentShops = RentShop::with(['rentMarket'])
            ->where('status', 'available')
            ->latest()
            ->get();

        // 3. Rent shops that MISSED payment (due amount > 0)
        $rentMissedShops = RentShop::with(['rentMarket', 'rentEntries'])
            ->get()
            ->filter(function ($shop) {
                $status = $shop->rentStatus();
                $shop->_status = $status;
                return $status['months_missed'] > 0 || $status['missed_amount'] > 0;
            })
            ->sortByDesc(fn($s) => $s->_status['missed_amount'])
            ->values();

        // 4. Rent shops that are UP TO DATE (paid)
        $rentPaidShops = RentShop::with(['rentMarket', 'rentEntries'])
            ->where('status', 'rented')
            ->get()
            ->filter(function ($shop) {
                $status = $shop->rentStatus();
                $shop->_status = $status;
                return $status['months_missed'] == 0 && ($status['months_due'] > 0 || $status['months_paid'] > 0);
            })
            ->values();

        // ── INSTALMENT REPORTS ──────────────────────────────────────────

        // 5. Active instalment shops
        $instalmentShops = Shop::with(['market', 'payments', 'owner', 'customers'])
            ->where('type', 'instalment')
            ->where('status', 'active')
            ->get()
            ->each(function ($shop) {
                $shop->_instStatus = $shop->instalmentStatus();
            });

        // 6. Instalment shops that MISSED payment
        $instalmentMissedShops = $instalmentShops->filter(function ($shop) {
            return $shop->_instStatus['months_missed'] > 0;
        })->sortByDesc(fn($s) => $s->_instStatus['missed_amount'])->values();

        // 7. Instalment shops that are UP TO DATE
        $instalmentPaidShops = $instalmentShops->filter(function ($shop) {
            return $shop->_instStatus['months_missed'] == 0
                && $shop->_instStatus['months_due'] > 0;
        })->values();

        // 8. Instalment shops with balance due (not fully paid off)
        $instalmentDueShops = $instalmentShops->filter(function ($shop) {
            return $shop->_instStatus['balance'] > 0;
        })->sortByDesc(fn($s) => $s->_instStatus['balance'])->values();

        // ── SUMMARY TOTALS ──────────────────────────────────────────────
        $summary = [
            'total_instalment_markets'  => Market::count(),
            'total_instalment_shops'    => Shop::where('type', 'instalment')->count(),
            'total_rent_markets'        => RentMarket::count(),
            'total_rent_shops'          => RentShop::count(),
            'total_rented'              => $rentedShops->count(),
            'total_available_rent'      => $availableRentShops->count(),
            'rent_missed_shops'         => $rentMissedShops->count(),
            'rent_missed_amount'        => $rentMissedShops->sum(fn($s) => $s->_status['missed_amount']),
            'rent_paid_shops'           => $rentPaidShops->count(),
            'instalment_active'         => $instalmentShops->count(),
            'instalment_missed_shops'   => $instalmentMissedShops->count(),
            'instalment_missed_amount'  => $instalmentMissedShops->sum(fn($s) => $s->_instStatus['missed_amount']),
            'instalment_paid_shops'     => $instalmentPaidShops->count(),
            'instalment_due_amount'     => $instalmentDueShops->sum(fn($s) => $s->_instStatus['balance']),
        ];

        return view('reports.index', compact(
            'reportType',
            'rentedShops',
            'availableRentShops',
            'rentMissedShops',
            'rentPaidShops',
            'instalmentShops',
            'instalmentMissedShops',
            'instalmentPaidShops',
            'instalmentDueShops',
            'summary'
        ));
    }
}
