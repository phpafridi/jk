<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class OwnerManagementController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Owner::withCount(['shops','rentEntries','ledgers']);
        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name',  'like', "%$s%")
                  ->orWhere('phone','like', "%$s%")
                  ->orWhere('cnic', 'like', "%$s%");
            });
        }
        $owners = $query->latest()->paginate(20);
        return view('owners.manage', compact('owners'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage owners');
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'cnic'    => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email'   => 'nullable|email',
            'notes'   => 'nullable|string',
        ]);
        Owner::create($data);
        return back()->with('success', 'Owner added successfully!');
    }

    public function update(Request $request, Owner $owner)
    {
        $this->authorize('manage owners');
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'cnic'    => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email'   => 'nullable|email',
            'notes'   => 'nullable|string',
        ]);
        $owner->update($data);
        return back()->with('success', 'Owner updated!');
    }

    public function destroy(Owner $owner)
    {
        $this->authorize('manage owners');
        $owner->delete();
        return back()->with('success', 'Owner deleted!');
    }

    // JSON search endpoint used by select inputs
    public function search(Request $request)
    {
        $q = $request->q ?? '';
        $owners = Owner::where('name',  'like', "%$q%")
                       ->orWhere('phone','like', "%$q%")
                       ->orWhere('cnic', 'like', "%$q%")
                       ->orderBy('name')
                       ->limit(30)
                       ->get(['id','name','phone','cnic']);
        return response()->json($owners);
    }

    // JSON search endpoint for customers
    public static function searchCustomers(Request $request)
    {
        $q = $request->q ?? '';
        $customers = Customer::where('name',  'like', "%$q%")
                             ->orWhere('phone','like', "%$q%")
                             ->orWhere('cnic', 'like', "%$q%")
                             ->orderBy('name')
                             ->limit(30)
                             ->get(['id','name','phone','cnic']);
        return response()->json($customers);
    }
}
