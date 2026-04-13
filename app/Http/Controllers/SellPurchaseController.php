<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Owner;
use App\Models\SellPurchaseEntry;
use App\Models\SellMarket;
use Illuminate\Http\Request;

class SellPurchaseController extends Controller
{
    public function index()
    {
        $query = SellPurchaseEntry::with('sellMarket');

        if (request('search')) {
            $s = request('search');
            $query->where(fn($q) => $q
                ->where('buyer_name',         'like', "%$s%")
                ->orWhere('seller_name',       'like', "%$s%")
                ->orWhere('shop_or_item_number','like', "%$s%"));
        }
        if (request('type')) {
            $query->where('entry_type', request('type'));
        }

        $entries   = $query->latest()->paginate(20);
        $markets   = SellMarket::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get(['id','name','phone','cnic']);
        $owners    = Owner::orderBy('name')->get(['id','name','phone','cnic']);

        return view('sell.index', compact('entries', 'markets', 'customers', 'owners'));
    }

    public function show(SellPurchaseEntry $entry)
    {
        $entry->load(['sellMarket', 'documents', 'sellerCustomer', 'buyerCustomer']);
        $remaining = max(0, (float)$entry->total - (float)($entry->amount_paid ?? 0));
        return view('sell.show', compact('entry', 'remaining'));
    }

    public function printReceipt(SellPurchaseEntry $entry)
    {
        $entry->load(['sellMarket', 'sellerCustomer', 'buyerCustomer', 'sellerOwner', 'buyerOwner']);
        return view('sell.receipt', compact('entry'));
    }

    public function store(Request $request)
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
            'amount_paid'         => 'nullable|numeric|min:0',
            'payment_method'      => 'nullable|in:cash,bank_transfer,cheque,online,other',
            'received_by'         => 'nullable|string|max:255',
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
            'seller_owner_id'     => 'nullable|exists:owners,id',
            'buyer_owner_id'      => 'nullable|exists:owners,id',
        ]);

        $data['amount_paid']    = $data['amount_paid'] ?? 0;
        $data['payment_method'] = $data['payment_method'] ?? 'cash';

        $entry = SellPurchaseEntry::create($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('sell-purchase/docs', 'public');
                $entry->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => in_array(strtolower($file->extension()), ['jpg','jpeg','png','gif','webp']) ? 'image' : 'document',
                ]);
            }
        }

        return redirect()->route('sell.index')->with('success', 'Entry added.');
    }

    public function uploadDocument(Request $request, SellPurchaseEntry $entry)
    {
        $request->validate(['documents.*' => 'required|file|max:20480']);
        foreach ($request->file('documents', []) as $file) {
            $path = $file->store('sell-purchase/docs', 'public');
            $entry->documents()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => in_array(strtolower($file->extension()), ['jpg','jpeg','png','gif','webp']) ? 'image' : 'document',
            ]);
        }
        return redirect()->route('sell.show', $entry)->with('success', 'File(s) uploaded.');
    }

    public function deleteDocument(\App\Models\EntryDocument $document)
    {
        $entryId = $document->documentable_id;
        \Illuminate\Support\Facades\Storage::disk('public')->delete($document->path);
        $document->delete();
        return redirect()->route('sell.show', $entryId)->with('success', 'Document deleted.');
    }

    public function destroy(SellPurchaseEntry $entry)
    {
        foreach ($entry->documents as $doc) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->path);
            $doc->delete();
        }
        $entry->delete();
        return redirect()->route('sell.index')->with('success', 'Entry deleted.');
    }
}
