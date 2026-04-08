<?php
namespace App\Http\Controllers;

use App\Models\ConstructionItem;
use App\Models\Market;
use Illuminate\Http\Request;

class ConstructionController extends Controller
{
    public function index()
    {
        $query = ConstructionItem::with('market');
        if (request('search')) {
            $s = request('search');
            $query->where(fn($q) => $q
                ->where('item_name', 'like', "%$s%")
                ->orWhere('project_name', 'like', "%$s%"));
        }
        if (request('market_id')) {
            $query->where('market_id', request('market_id'));
        }
        // Get total BEFORE paginating (from the same filtered query)
        $total   = (clone $query)->sum('total');
        $items   = $query->latest()->paginate(20);
        $markets = Market::orderBy('name')->get();
        return view('construction.index', compact('items', 'total', 'markets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'market_id'    => 'nullable|exists:markets,id',
            'project_name' => 'required|string|max:255',
            'item_name'    => 'required|string|max:255',
            'quantity'     => 'required|numeric|min:0',
            'unit'         => 'required|string|max:50',
            'measurement'  => 'nullable|string|max:100',
            'unit_price'   => 'required|numeric|min:0',
            'total'        => 'required|numeric|min:0',
            'date'         => 'required|date',
            'notes'        => 'nullable|string',
        ]);
        ConstructionItem::create($data);
        return redirect()->route('construction.index')->with('success', 'Item added.');
    }

    public function destroy(ConstructionItem $item)
    {
        $item->delete();
        return redirect()->route('construction.index')->with('success', 'Item deleted.');
    }
}
