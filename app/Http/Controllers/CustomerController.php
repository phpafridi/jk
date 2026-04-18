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


    /**
     * Quick AJAX store — returns JSON. Used by inline modals so page doesn't reload.
     */
    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'cnic'        => 'nullable|string|max:20',
            'address'     => 'nullable|string',
        ]);

        // Check for duplicate CNIC across customers and owners (ignore blank CNICs)
        if (!empty($data['cnic'])) {
            $existingCustomer = Customer::where('cnic', $data['cnic'])->first();
            if ($existingCustomer) {
                return response()->json([
                    'error'   => 'duplicate_cnic',
                    'message' => 'A customer with CNIC "' . $data['cnic'] . '" already exists: ' . $existingCustomer->name . '. Please search and select them instead.',
                ], 422);
            }

            $existingOwner = \App\Models\Owner::where('cnic', $data['cnic'])->first();
            if ($existingOwner) {
                return response()->json([
                    'error'   => 'duplicate_cnic',
                    'message' => 'An owner with CNIC "' . $data['cnic'] . '" already exists: ' . $existingOwner->name . '. Duplicate CNICs are not allowed.',
                ], 422);
            }
        }

        $customer = Customer::create($data);
        return response()->json([
            'id'    => $customer->id,
            'name'  => $customer->name,
            'phone' => $customer->phone ?? '',
            'cnic'  => $customer->cnic  ?? '',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'cnic'        => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'email'       => 'nullable|email|max:255',
            'shop_id'     => 'nullable|exists:shops,id',
            'notes'       => 'nullable|string',
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
        // If called from an inline modal (AJAX-like or same-page), redirect back
        if ($request->has('_redirect_back') || $request->headers->get('referer')) {
            return redirect()->back()->with('success', 'Customer "' . $customer->name . '" created.');
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