<?php
namespace App\Http\Controllers;

use App\Models\RentEntry;
use App\Models\Shop;
use App\Models\Customer;
use App\Models\Market;
use Illuminate\Http\Request;

class RentController extends Controller
{
    public function index()
    {
        $query = RentEntry::with(['shop.market', 'customer']);
        if (request('search')) {
            $query->where('shop_number', 'like', '%' . request('search') . '%');
        }
        if (request('market_id')) {
            $query->whereHas('shop', fn($q) => $q->where('market_id', request('market_id')));
        }
        $entries   = $query->latest()->paginate(20);
        $markets   = Market::orderBy('name')->get();
        $shops     = Shop::with('market')->where('type', 'rent')->orderBy('shop_number')->get();
        $customers = Customer::orderBy('name')->get(['id','name','phone','cnic']);
        return view('rent.index', compact('entries', 'shops', 'markets', 'customers'));
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
