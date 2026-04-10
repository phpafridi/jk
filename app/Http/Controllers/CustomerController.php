<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Shop;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $query = Customer::with(['shop.market']);
        if (request('search')) {
            $s = request('search');
            $query->where(fn($q) => $q
                ->where('name', 'like', "%$s%")
                ->orWhere('cnic', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%"));
        }
        $customers = $query->latest()->paginate(20);
        // Only instalment shops linkable to customers
        $shops = Shop::with('market')->where('type', 'instalment')->orderBy('shop_number')->get();
        return view('customers.index', compact('customers', 'shops'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'cnic'    => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email'   => 'nullable|email|max:255',
            'shop_id' => 'nullable|exists:shops,id',
            'notes'   => 'nullable|string',
        ]);
        $customer = Customer::create($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('customer-documents', 'public');
                $customer->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => str_contains($file->getMimeType(), 'image') ? 'image' : 'document',
                ]);
            }
        }
        return redirect()->route('customers.index')->with('success', 'Customer added.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['documents', 'shop.market', 'shop.payments']);
        return view('customers.show', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'cnic'    => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email'   => 'nullable|email|max:255',
            'shop_id' => 'nullable|exists:shops,id',
            'notes'   => 'nullable|string',
        ]);
        $customer->update($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('customer-documents', 'public');
                $customer->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => str_contains($file->getMimeType(), 'image') ? 'image' : 'document',
                ]);
            }
        }
        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }

    public function deleteDocument(CustomerDocument $document)
    {
        $customer = $document->customer;
        \Illuminate\Support\Facades\Storage::disk('public')->delete($document->path);
        $document->delete();
        return redirect()->route('customers.show', $customer)->with('success', 'Document deleted.');
    }

    public function uploadDocument(Request $request, Customer $customer)
    {
        $request->validate([
            'documents.*' => 'required|file|max:20480|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
            'doc_type'    => 'nullable|string|in:cnic,mou,agreement,photo,other',
        ]);
        $docType = $request->doc_type ?? 'other';
        foreach ($request->file('documents', []) as $file) {
            $path    = $file->store('customer-documents', 'public');
            $isImage = str_contains($file->getMimeType(), 'image');
            $customer->documents()->create([
                'name'     => $file->getClientOriginalName(),
                'path'     => $path,
                'type'     => $isImage ? 'image' : 'document',
                'doc_type' => $docType,
            ]);
        }
        return redirect()->route('customers.show', $customer)->with('success', 'Document(s) uploaded.');
    }
}
