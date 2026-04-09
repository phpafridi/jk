<?php
namespace App\Http\Controllers;

use App\Models\ConstructionItem;
use App\Models\Market;
use Illuminate\Http\Request;

class ConstructionController extends Controller
{
    public function index()
    {
        // Group items by project_name to show project cards
        $query = ConstructionItem::with('market');
        if (request('market_id')) {
            $query->where('market_id', request('market_id'));
        }
        if (request('search')) {
            $s = request('search');
            $query->where(fn($q) => $q
                ->where('project_name', 'like', "%$s%")
                ->orWhere('item_name',   'like', "%$s%"));
        }

        // Build project summary cards
        $projects = $query->get()
            ->groupBy('project_name')
            ->map(function($items, $name) {
                return [
                    'project_name' => $name,
                    'market'       => $items->first()->market,
                    'total'        => $items->sum('total'),
                    'items_count'  => $items->count(),
                    'last_date'    => $items->max('date'),
                    'market_id'    => $items->first()->market_id,
                ];
            })
            ->values();

        $grandTotal = $projects->sum('total');
        $markets    = Market::orderBy('name')->get();

        return view('construction.index', compact('projects', 'grandTotal', 'markets'));
    }

    public function show(string $projectName)
    {
        $query = ConstructionItem::with('market')
            ->where('project_name', $projectName);

        if (request('market_id')) {
            $query->where('market_id', request('market_id'));
        }

        $items      = $query->latest()->paginate(50);
        $total      = ConstructionItem::where('project_name', $projectName)->sum('total');
        $market     = optional($items->first())->market;
        $markets    = Market::orderBy('name')->get();

        return view('construction.show_project', compact('projectName', 'items', 'total', 'market', 'markets'));
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

        // Redirect back to project if we came from one
        if ($request->redirect_project) {
            return redirect()->route('construction.show', urlencode($request->redirect_project))->with('success', 'Item added.');
        }
        return redirect()->route('construction.index')->with('success', 'Item added.');
    }

    public function destroy(ConstructionItem $item)
    {
        $project = $item->project_name;
        $item->delete();
        // If items still exist for this project, go back to it
        $stillHas = ConstructionItem::where('project_name', $project)->exists();
        if ($stillHas) {
            return redirect()->route('construction.show', urlencode($project))->with('success', 'Item deleted.');
        }
        return redirect()->route('construction.index')->with('success', 'Item deleted.');
    }
}
