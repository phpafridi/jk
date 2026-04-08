<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Shop;
use App\Models\User;
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
        $shops     = Shop::with('market')->orderBy('shop_number')->get();
        $users     = User::orderBy('name')->get(); // for "Link to User Account" dropdown
        return view('customers.index', compact('customers', 'shops', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'cnic'           => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'email'          => 'nullable|email|max:255',
            'linked_user_id' => 'nullable|exists:users,id',
            'shop_id'        => 'nullable|exists:shops,id',
            'notes'          => 'nullable|string',
        ]);
        $customer = Customer::create($data);

        // Handle document uploads
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
        $customer->load(['documents', 'shop.market', 'shop.payments', 'linkedUser']);
        return view('customers.show', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'nullable|string|max:50',
            'cnic'           => 'nullable|string|max:20',
            'address'        => 'nullable|string',
            'email'          => 'nullable|email|max:255',
            'linked_user_id' => 'nullable|exists:users,id',
            'shop_id'        => 'nullable|exists:shops,id',
            'notes'          => 'nullable|string',
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
        $document->delete();
        return redirect()->route('customers.show', $customer)->with('success', 'Document deleted.');
    }
}
