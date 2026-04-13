<?php
namespace App\Http\Controllers;

use App\Models\RentEntry;
use App\Models\RentMarket;
use App\Models\Shop;
use App\Models\Customer;
use App\Models\Market;
use Illuminate\Http\Request;

class RentController extends Controller
{
    public function index()
    {
        $query = RentEntry::with(['rentShop.rentMarket', 'customer']);
        if (request('search')) {
            $query->where('shop_number', 'like', '%' . request('search') . '%');
        }
        if (request('market_id')) {
            $query->whereHas('rentShop', fn($q) => $q->where('rent_market_id', request('market_id')));
        }
        $entries = $query->latest()->paginate(20);

        // Market breakdown with pending/paid stats
        $rentMarkets = RentMarket::withCount('shops')
            ->with(['shops.rentEntries'])
            ->get()
            ->map(function($market) {
                $pendingShops  = 0;
                $pendingAmount = 0;
                $pendingMonths = 0;
                $paidAmount    = 0;
                $shopDetails   = [];

                foreach ($market->shops as $shop) {
                    $shopPending  = 0;
                    $shopMonths   = 0;
                    $shopPaid     = 0;
                    foreach ($shop->rentEntries as $entry) {
                        $due = $entry->rent - $entry->amount_paid;
                        if ($due > 0) {
                            $shopPending  += $due;
                            $shopMonths++;
                        }
                        $shopPaid += $entry->amount_paid;
                    }
                    if ($shopPending > 0) {
                        $pendingShops++;
                        $shopDetails[] = [
                            'shop_number'    => $shop->shop_number,
                            'pending_amount' => $shopPending,
                            'pending_months' => $shopMonths,
                            'paid_amount'    => $shopPaid,
                        ];
                    }
                    $pendingAmount += $shopPending;
                    $pendingMonths += $shopMonths;
                    $paidAmount    += $shopPaid;
                }

                return [
                    'id'             => $market->id,
                    'name'           => $market->name,
                    'location'       => $market->location,
                    'total_shops'    => $market->shops_count,
                    'pending_shops'  => $pendingShops,
                    'pending_amount' => $pendingAmount,
                    'pending_months' => $pendingMonths,
                    'paid_amount'    => $paidAmount,
                    'shop_details'   => $shopDetails,
                ];
            })
            ->filter(fn($m) => $m['total_shops'] > 0);

        $rentMarketsAll   = RentMarket::orderBy('name')->get();
        $customers        = Customer::orderBy('name')->get(['id','name','phone','cnic']);

        return view('rent.index', compact('entries', 'rentMarketsAll', 'rentMarkets', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shop_id'     => 'required|exists:shops,id',
            'shop_number' => 'required|string|max:255',
            'rent'        => 'required|numeric|min:0',
            'date'        => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'received_by' => 'nullable|string|max:255',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);
        RentEntry::create($data);
        return redirect()->route('rent.index')->with('success', 'Rent entry added.');
    }

    public function destroy(RentEntry $rentEntry)
    {
        $rentEntry->delete();
        return redirect()->route('rent.index')->with('success', 'Entry deleted.');
    }
}
