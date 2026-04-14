<?php
namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function index()
    {
        $markets = Market::withCount('shops')->latest()->paginate(20);
        return view('markets.index', compact('markets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:markets,name',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('markets', 'public');
        }
        Market::create($data);
        return redirect()->route('markets.index')->with('success', 'Market created.');
    }

    public function show(Market $market)
    {
        $shops = $market->shops()->with(['owner', 'payments'])->latest()->paginate(20);
        return view('markets.show', compact('market', 'shops'));
    }

    public function update(Request $request, Market $market)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:markets,name,'.$market->id,
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:5120',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('markets', 'public');
        }
        $market->update($data);
        return redirect()->route('markets.index')->with('success', 'Market updated.');
    }

    public function destroy(Market $market)
    {
        $market->delete();
        return redirect()->route('markets.index')->with('success', 'Market deleted.');
    }
}
