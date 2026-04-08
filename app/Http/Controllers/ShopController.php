<?php
namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Shop;
use App\Models\ShopPayment;
use App\Models\ShopDocument;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function store(Request $request, Market $market)
    {
        $data = $request->validate([
            'shop_number'     => 'required|string|max:255',
            'owner_id'        => 'nullable|exists:users,id',
            'type'            => 'required|in:instalment,rent,sell,purchase',
            'date_of_payment' => 'nullable|date',
            'total_amount'    => 'nullable|numeric|min:0',
            'paid_amount'     => 'nullable|numeric|min:0',
            'rent_amount'     => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:active,inactive,sold,rented',
        ]);
        $market->shops()->create($data);
        return redirect()->route('markets.show', $market)->with('success', 'Shop added.');
    }

    public function show(Shop $shop)
    {
        $shop->load(['payments' => fn($q) => $q->latest(), 'documents', 'owner', 'market', 'customers']);
        $totalPaid = $shop->payments->sum('amount');
        $balance   = $shop->total_amount - $totalPaid;
        return view('shops.show', compact('shop', 'totalPaid', 'balance'));
    }

    public function update(Request $request, Shop $shop)
    {
        $data = $request->validate([
            'shop_number'     => 'required|string|max:255',
            'owner_id'        => 'nullable|exists:users,id',
            'type'            => 'nullable|in:instalment,rent,sell,purchase',
            'date_of_payment' => 'nullable|date',
            'total_amount'    => 'nullable|numeric|min:0',
            'paid_amount'     => 'nullable|numeric|min:0',
            'rent_amount'     => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:active,inactive,sold,rented',
        ]);
        $shop->update($data);
        return redirect()->route('markets.show', $shop->market)->with('success', 'Shop updated.');
    }

    public function destroy(Shop $shop)
    {
        $market = $shop->market;
        $shop->delete();
        return redirect()->route('markets.show', $market)->with('success', 'Shop deleted.');
    }

    public function addPayment(Request $request, Shop $shop)
    {
        $data = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'receipt_number' => 'nullable|string|max:100',
        ]);
        $data['user_id'] = auth()->id();
        if (empty($data['receipt_number'])) {
            $data['receipt_number'] = 'RCP-' . strtoupper(substr(uniqid(), -8));
        }
        $shop->payments()->create($data);
        $shop->increment('paid_amount', $data['amount']);
        return redirect()->route('shops.show', $shop)->with('success', 'Payment recorded.');
    }

    public function printReceipt(ShopPayment $payment)
    {
        $payment->load(['shop.market', 'shop.owner', 'recordedBy']);
        return view('shops.receipt', compact('payment'));
    }

    public function uploadDocument(Request $request, Shop $shop)
    {
        $request->validate(['document' => 'required|file|max:10240', 'name' => 'nullable|string|max:255']);
        $path = $request->file('document')->store('shop-documents', 'public');
        $shop->documents()->create([
            'name' => $request->input('name', $request->file('document')->getClientOriginalName()),
            'path' => $path,
            'type' => str_contains($request->file('document')->getMimeType(), 'image') ? 'image' : 'pdf',
        ]);
        return redirect()->route('shops.show', $shop)->with('success', 'Document uploaded.');
    }

    public function deleteDocument(ShopDocument $document)
    {
        $shop = $document->shop;
        $document->delete();
        return redirect()->route('shops.show', $shop)->with('success', 'Document deleted.');
    }
}
