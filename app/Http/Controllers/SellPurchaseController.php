<?php
namespace App\Http\Controllers;

use App\Models\SellPurchaseEntry;
use App\Models\Market;
use Illuminate\Http\Request;

class SellPurchaseController extends Controller
{
    public function index()
    {
        $query = SellPurchaseEntry::with('market');
        if (request('search')) {
            $s = request('search');
            $query->where(fn($q) => $q
                ->where('buyer_name', 'like', "%$s%")
                ->orWhere('seller_name', 'like', "%$s%")
                ->orWhere('shop_or_item_number', 'like', "%$s%"));
        }
        if (request('type')) {
            $query->where('entry_type', request('type'));
        }
        $entries = $query->latest()->paginate(20);
        $markets = Market::orderBy('name')->get();
        return view('sell.index', compact('entries', 'markets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entry_type'          => 'required|in:car,shop,plot',
            'transaction_type'    => 'required|in:sell,purchase',
            'market_id'           => 'nullable|exists:markets,id',
            'date'                => 'required|date',
            'shop_or_item_number' => 'nullable|string|max:255',
            'per_sqft_rate'       => 'nullable|numeric|min:0',
            'sqft'                => 'nullable|numeric|min:0',
            'total'               => 'required|numeric|min:0',
            'seller_name'         => 'nullable|string|max:255',
            'seller_cnic'         => 'nullable|string|max:50',
            'seller_phone'        => 'nullable|string|max:50',
            'buyer_name'          => 'nullable|string|max:255',
            'buyer_cnic'          => 'nullable|string|max:50',
            'buyer_phone'         => 'nullable|string|max:50',
            'car_make'            => 'nullable|string|max:100',
            'car_model'           => 'nullable|string|max:100',
            'car_year'            => 'nullable|string|max:10',
            'car_registration'    => 'nullable|string|max:50',
            'notes'               => 'nullable|string',
        ]);
        SellPurchaseEntry::create($data);
        return redirect()->route('sell.index')->with('success', 'Entry added.');
    }

    public function destroy(SellPurchaseEntry $entry)
    {
        $entry->delete();
        return redirect()->route('sell.index')->with('success', 'Entry deleted.');
    }
}
