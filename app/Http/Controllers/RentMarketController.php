<?php
namespace App\Http\Controllers;

use App\Models\RentEntry;
use App\Models\RentMarket;
use App\Models\RentShop;
use App\Models\EntryDocument;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RentMarketController extends Controller
{
    // ── Markets ──────────────────────────────────────────────
    public function index()
    {
        $markets = RentMarket::withCount('shops')->latest()->paginate(20);
        return view('rent.markets.index', compact('markets'));
    }

    public function storeMarket(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        RentMarket::create($data);
        return redirect()->route('rent.markets.index')->with('success', 'Rent market created.');
    }

    public function updateMarket(Request $request, RentMarket $rentMarket)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        $rentMarket->update($data);
        return redirect()->route('rent.markets.index')->with('success', 'Market updated.');
    }

    public function destroyMarket(RentMarket $rentMarket)
    {
        $rentMarket->delete();
        return redirect()->route('rent.markets.index')->with('success', 'Market deleted.');
    }

    public function showMarket(RentMarket $rentMarket)
    {
        $shops = $rentMarket->shops()->latest()->paginate(20);
        return view('rent.markets.show', compact('rentMarket', 'shops'));
    }

    // ── Shops ─────────────────────────────────────────────────
    public function storeShop(Request $request, RentMarket $rentMarket)
    {
        $data = $request->validate([
            'shop_number'   => 'required|string|max:255',
            'tenant_name'   => 'nullable|string|max:255',
            'tenant_phone'  => 'nullable|string|max:50',
            'tenant_cnic'   => 'nullable|string|max:50',
            'status'        => 'nullable|in:available,rented,inactive',
            'rent_amount'   => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);
        $rentMarket->shops()->create($data);
        return redirect()->route('rent.markets.show', $rentMarket)->with('success', 'Shop added.');
    }

    public function updateShop(Request $request, RentShop $rentShop)
    {
        $data = $request->validate([
            'shop_number'   => 'required|string|max:255',
            'tenant_name'   => 'nullable|string|max:255',
            'tenant_phone'  => 'nullable|string|max:50',
            'tenant_cnic'   => 'nullable|string|max:50',
            'status'        => 'nullable|in:available,rented,inactive',
            'rent_amount'   => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);
        $rentShop->update($data);
        return redirect()->route('rent.shops.show', $rentShop)->with('success', 'Shop updated.');
    }

    public function destroyShop(RentShop $rentShop)
    {
        $market = $rentShop->rentMarket;
        $rentShop->delete();
        return redirect()->route('rent.markets.show', $market)->with('success', 'Shop deleted.');
    }

    // ── Shop Detail (rent entries + documents live here) ─────
    public function showShop(RentShop $rentShop)
    {
        $rentShop->load(['rentMarket', 'rentEntries' => function($q){ $q->latest()->with('customer'); }, 'documents']);
        $customers = Customer::orderBy('name')->get(['id','name','phone','cnic']);
        $totalRent   = $rentShop->rentEntries->sum('rent');
        $totalPaid   = $rentShop->rentEntries->sum('amount_paid');
        return view('rent.markets.show_shop', compact('rentShop', 'customers', 'totalRent', 'totalPaid'));
    }

    // ── Rent Entries ─────────────────────────────────────────
    public function storeEntry(Request $request, RentShop $rentShop)
    {
        $data = $request->validate([
            'shop_number'  => 'required|string|max:255',
            'rent'         => 'required|numeric|min:0',
            'date'         => 'required|date',
            'customer_id'  => 'nullable|exists:customers,id',
            'received_by'  => 'nullable|string|max:255',
            'amount_paid'  => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);
        $data['rent_shop_id'] = $rentShop->id;
        RentEntry::create($data);
        return redirect()->route('rent.shops.show', $rentShop)->with('success', 'Rent entry added.');
    }

    public function destroyEntry(RentEntry $rentEntry)
    {
        $shop = $rentEntry->rentShop;
        $rentEntry->delete();
        return redirect()->route('rent.shops.show', $shop)->with('success', 'Entry deleted.');
    }

    // ── Shop Documents ────────────────────────────────────────
    public function uploadDocument(Request $request, RentShop $rentShop)
    {
        $request->validate(['documents.*' => 'required|file|max:20480']);
        foreach ($request->file('documents', []) as $file) {
            $path = $file->store('rent-shops/docs', 'public');
            $rentShop->documents()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => in_array(strtolower($file->extension()), ['jpg','jpeg','png','gif','webp']) ? 'image' : 'document',
            ]);
        }
        return redirect()->route('rent.shops.show', $rentShop)->with('success', 'Files uploaded.');
    }

    public function deleteDocument(EntryDocument $document)
    {
        Storage::disk('public')->delete($document->path);
        $shopId = $document->documentable_id;
        $document->delete();
        $shop = RentShop::find($shopId);
        return redirect()->route('rent.shops.show', $shop)->with('success', 'File deleted.');
    }
}
