<?php
namespace App\Http\Controllers;

use App\Models\SellPurchaseEntry;
use App\Models\SellMarket;
use App\Models\SellShop;
use App\Models\Customer;
use App\Models\EntryDocument;
use Illuminate\Http\Request;

class SellMarketController extends Controller
{
    // ── Markets ──────────────────────────────────────────────
    public function index()
    {
        $markets = SellMarket::withCount('shops')->latest()->paginate(20);
        return view('sell.markets.index', compact('markets'));
    }

    public function storeMarket(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        SellMarket::create($data);
        return redirect()->route('sell.markets.index')->with('success', 'Market created.');
    }

    public function updateMarket(Request $request, SellMarket $sellMarket)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        $sellMarket->update($data);
        return redirect()->route('sell.markets.index')->with('success', 'Market updated.');
    }

    public function destroyMarket(SellMarket $sellMarket)
    {
        $sellMarket->delete();
        return redirect()->route('sell.markets.index')->with('success', 'Market deleted.');
    }

    public function showMarket(SellMarket $sellMarket)
    {
        $shops = $sellMarket->shops()->latest()->paginate(20);
        return view('sell.markets.show', compact('sellMarket', 'shops'));
    }

    // ── Shops ─────────────────────────────────────────────────
    public function storeShop(Request $request, SellMarket $sellMarket)
    {
        $data = $request->validate([
            'shop_number' => 'required|string|max:255',
            'type'        => 'required|in:shop,plot',
            'status'      => 'nullable|in:available,sold,inactive',
            'area_sqft'   => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);
        $sellMarket->shops()->create($data);
        return redirect()->route('sell.markets.show', $sellMarket)->with('success', 'Shop/Plot added.');
    }

    public function updateShop(Request $request, SellShop $sellShop)
    {
        $data = $request->validate([
            'shop_number' => 'required|string|max:255',
            'type'        => 'required|in:shop,plot',
            'status'      => 'nullable|in:available,sold,inactive',
            'area_sqft'   => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);
        $sellShop->update($data);
        return redirect()->route('sell.markets.show', $sellShop->sellMarket)->with('success', 'Updated.');
    }

    public function destroyShop(SellShop $sellShop)
    {
        $market = $sellShop->sellMarket;
        $sellShop->delete();
        return redirect()->route('sell.markets.show', $market)->with('success', 'Deleted.');
    }

    // ── Sell/Purchase Entries ────────────────────────────────
    public function entries()
    {
        $query = SellPurchaseEntry::with(['sellMarket']);
        if (request('type'))      $query->where('entry_type', request('type'));
        if (request('market_id')) $query->where('sell_market_id', request('market_id'));
        if (request('search')) {
            $s = request('search');
            $query->where(fn($q) => $q
                ->where('buyer_name', 'like', "%$s%")
                ->orWhere('seller_name', 'like', "%$s%")
                ->orWhere('shop_or_item_number', 'like', "%$s%"));
        }
        $entries   = $query->latest()->paginate(20);
        $markets   = SellMarket::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get(['id','name','phone','cnic']);
        return view('sell.index', compact('entries', 'markets', 'customers'));
    }

    public function storeEntry(Request $request)
    {
        $data = $request->validate([
            'entry_type'          => 'required|in:car,shop,plot',
            'transaction_type'    => 'required|in:sell,purchase',
            'sell_market_id'      => 'nullable|exists:sell_markets,id',
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
            'seller_customer_id'  => 'nullable|exists:customers,id',
            'buyer_customer_id'   => 'nullable|exists:customers,id',
        ]);

        $entry = SellPurchaseEntry::create($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('sell-purchase/docs', 'public');
                $entry->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => in_array($file->extension(), ['jpg','jpeg','png','gif']) ? 'image' : 'document',
                ]);
            }
        }

        return redirect()->route('sell.index')->with('success', 'Entry saved.');
    }

    public function destroyEntry(SellPurchaseEntry $entry)
    {
        $entry->delete();
        return redirect()->route('sell.index')->with('success', 'Entry deleted.');
    }
}
