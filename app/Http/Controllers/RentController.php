<?php
namespace App\Http\Controllers;

use App\Models\RentEntry;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class RentController extends Controller
{
    public function index()
    {
        $query = RentEntry::with(['shop.market', 'owner']);
        if (request('search')) {
            $query->where('shop_number', 'like', '%' . request('search') . '%');
        }
        $entries = $query->latest()->paginate(20);
        $shops   = Shop::with('market')->orderBy('shop_number')->get();
        $owners  = User::orderBy('name')->get(); // login users only
        return view('rent.index', compact('entries', 'shops', 'owners'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shop_id'     => 'required|exists:shops,id',
            'shop_number' => 'required|string|max:255',
            'rent'        => 'required|numeric|min:0',
            'date'        => 'required|date',
            'owner_id'    => 'nullable|exists:users,id',
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
