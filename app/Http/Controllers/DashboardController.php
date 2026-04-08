<?php
namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Shop;
use App\Models\RentEntry;
use App\Models\ShopPayment;
use App\Models\ConstructionItem;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'markets'            => Market::count(),
            'shops'              => Shop::count(),
            'rent_this_month'    => RentEntry::whereMonth('date', now()->month)
                                       ->whereYear('date', now()->year)
                                       ->sum('amount_paid'),
            'total_income'       => ShopPayment::sum('amount'),
            'pending'            => (float) DB::table('shops')
                                       ->whereNull('deleted_at')
                                       ->selectRaw('SUM(total_amount - paid_amount) as bal')
                                       ->value('bal'),
            'construction_total' => ConstructionItem::sum('total'),
            'owners'             => User::count(),
            'customers'          => Customer::count(),
        ];

        $recentPayments = ShopPayment::with(['shop.market', 'shop.owner', 'recordedBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recentPayments'));
    }
}
