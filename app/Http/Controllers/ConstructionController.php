<?php
namespace App\Http\Controllers;

use App\Models\ConstructionItem;
use App\Models\ConstructionProject;
use App\Models\Market;
use Illuminate\Http\Request;

class ConstructionController extends Controller
{
    public function index()
    {
        $query = ConstructionProject::with('market');

        if (request('market_id')) {
            $query->where('market_id', request('market_id'));
        }
        if (request('search')) {
            $s = request('search');
            $query->where('name', 'like', "%$s%");
        }

        $projectModels = $query->latest()->get();

        // Attach totals from construction_items
        $projects = $projectModels->map(function ($proj) {
            $items = ConstructionItem::where('project_name', $proj->name)->get();
            return [
                'id'           => $proj->id,
                'project_name' => $proj->name,
                'market'       => $proj->market,
                'total'        => $items->sum('total'),
                'items_count'  => $items->count(),
                'last_date'    => $items->max('date'),
                'market_id'    => $proj->market_id,
                'notes'        => $proj->notes,
                'created_at'   => $proj->created_at,
            ];
        });

        $grandTotal = $projects->sum('total');
        $markets    = Market::orderBy('name')->get();

        return view('construction.index', compact('projects', 'grandTotal', 'markets'));
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:construction_projects,name',
            'market_id' => 'nullable|exists:markets,id',
            'notes'     => 'nullable|string',
        ]);

        ConstructionProject::create([
            'name'      => $request->name,
            'market_id' => $request->market_id,
            'notes'     => $request->notes,
        ]);

        return redirect()->route('construction.index')->with('success', 'Project "' . $request->name . '" created.');
    }

    public function destroyProject(ConstructionProject $project)
    {
        // Delete all items under this project too
        ConstructionItem::where('project_name', $project->name)->delete();
        $project->delete();
        return redirect()->route('construction.index')->with('success', 'Project deleted.');
    }

    public function show(string $projectName)
    {
        $projectName = urldecode($projectName);
        $project = ConstructionProject::where('name', $projectName)->first();

        $query = ConstructionItem::with('market')
            ->where('project_name', $projectName);

        $items   = $query->latest()->paginate(50);
        $total   = ConstructionItem::where('project_name', $projectName)->sum('total');
        $market  = $project ? $project->market : optional($items->first())->market;
        $markets = Market::orderBy('name')->get();

        return view('construction.show_project', compact('projectName', 'project', 'items', 'total', 'market', 'markets'));
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

        // Auto-inherit market_id from project if not provided
        if (empty($data['market_id'])) {
            $proj = ConstructionProject::where('name', $data['project_name'])->first();
            if ($proj && $proj->market_id) {
                $data['market_id'] = $proj->market_id;
            }
        }

        ConstructionItem::create($data);

        if ($request->redirect_project) {
            return redirect()->route('construction.show', urlencode($request->redirect_project))->with('success', 'Transaction added.');
        }
        return redirect()->route('construction.index')->with('success', 'Transaction added.');
    }

    public function destroy(ConstructionItem $item)
    {
        $project = $item->project_name;
        $item->delete();
        return redirect()->route('construction.show', urlencode($project))->with('success', 'Item deleted.');
    }
}
