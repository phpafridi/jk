<?php
namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\OwnerLedger;
use App\Models\Market;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OwnerLedgerController extends Controller
{
    public function index()
    {
        // Load from the OWNERS table (not users)
        $owners        = Owner::orderBy('name')->get();
        $selectedOwner = null;
        $ledgers       = (new OwnerLedger)->newQuery()->whereRaw('0=1')->paginate(20);
        $balance       = 0;

        if (request('owner_id')) {
            $selectedOwner = Owner::find(request('owner_id'));
            if ($selectedOwner) {
                $ledgers = OwnerLedger::with(['market', 'shop'])
                    ->where('owner_id', $selectedOwner->id)
                    ->latest('date')
                    ->paginate(20);

                $all     = OwnerLedger::where('owner_id', $selectedOwner->id)->get();
                $balance = $all->where('transaction_type', 'credit')->sum('amount')
                         - $all->where('transaction_type', 'debit')->sum('amount');
            }
        }

        $markets = Market::orderBy('name')->get();
        $shops   = Shop::with('market')->orderBy('shop_number')->get();

        return view('owners.index', compact('owners', 'selectedOwner', 'ledgers', 'balance', 'markets', 'shops'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_id'         => 'required|exists:owners,id',
            'market_id'        => 'nullable|exists:markets,id',
            'shop_id'          => 'nullable|exists:shops,id',
            'transaction_type' => 'required|in:debit,credit',
            'amount'           => 'required|numeric|min:0.01',
            'date'             => 'required|date',
            'description'      => 'nullable|string',
            'reference'        => 'nullable|string|max:255',
            'payment_method'   => 'nullable|string|max:100',
        ]);
        $ledger = OwnerLedger::create($data);

        // Save attached invoice / photo
        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');
            $path = $file->store('owner-invoices', 'public');
            $ledger->update(['invoice_path' => $path, 'invoice_name' => $file->getClientOriginalName()]);
        }

        return redirect()->route('owners.index', ['owner_id' => $data['owner_id']])->with('success', 'Entry added.');
    }

    public function destroy(OwnerLedger $ownerLedger)
    {
        $ownerId = $ownerLedger->owner_id;
        if ($ownerLedger->invoice_path) {
            Storage::disk('public')->delete($ownerLedger->invoice_path);
        }
        $ownerLedger->delete();
        return redirect()->route('owners.index', ['owner_id' => $ownerId])->with('success', 'Entry deleted.');
    }
}
