<?php

namespace App\Http\Controllers;

use App\Models\RentEntry;
use App\Models\SellPurchaseEntry;
use App\Models\ConstructionItem;
use App\Models\OwnerLedger;
use App\Models\Customer;
use App\Models\Owner;
use App\Models\CustomerDocument;
use App\Models\EntryDocument;
use App\Models\User;
use App\Models\Market;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// ==================== RENT ====================
class RentController extends Controller
{
    public function index(Request $request)
    {
        $query = RentEntry::with(['shop.market', 'customer']);
        if ($request->search) {
            $query->where('shop_number', 'like', "%{$request->search}%");
        }
        if ($request->market_id) {
            $query->whereHas('shop', fn($q) => $q->where('market_id', $request->market_id));
        }
        $entries  = $query->latest()->paginate(15);
        $markets  = \App\Models\Market::orderBy('name')->get();
        $shops    = Shop::with('market')->where('type','rent')->get();
        $customers = \App\Models\Customer::orderBy('name')->get(['id','name','phone','cnic']);
        return view('rent.index', compact('entries', 'shops', 'markets', 'customers'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage rent');
        $data = $request->validate([
            'shop_id'      => 'required|exists:shops,id',
            'shop_number'  => 'required|string',
            'rent'         => 'required|numeric|min:0',
            'date'         => 'required|date',
            'customer_id'  => 'nullable|exists:customers,id',
            'received_by'  => 'nullable|string',
            'amount_paid'  => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);

        RentEntry::create($data);
        return back()->with('success', 'Rent entry added!');
    }

    public function destroy(RentEntry $rentEntry)
    {
        $this->authorize('manage rent');
        $rentEntry->delete();
        return back()->with('success', 'Entry deleted!');
    }
}

// ==================== SELL / PURCHASE ====================
class SellPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = SellPurchaseEntry::with('market');
        if ($request->type)   $query->where('entry_type', $request->type);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('buyer_name', 'like', "%{$request->search}%")
                  ->orWhere('seller_name', 'like', "%{$request->search}%")
                  ->orWhere('shop_or_item_number', 'like', "%{$request->search}%");
            });
        }
        $entries = $query->latest()->paginate(15);
        $markets = Market::all();
        $customers = Customer::orderBy('name')->get(['id','name','phone','cnic']);
        $owners    = Owner::orderBy('name')->get(['id','name','phone','cnic']);
        return view('sell.index', compact('entries', 'markets', 'customers', 'owners'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage sell purchase');
        $data = $request->validate([
            'entry_type'       => 'required|in:car,shop,plot',
            'transaction_type' => 'required|in:sell,purchase',
            'market_id'        => 'nullable|exists:markets,id',
            'date'             => 'required|date',
            'shop_or_item_number' => 'nullable|string',
            'per_sqft_rate'    => 'nullable|numeric',
            'sqft'             => 'nullable|numeric',
            'total'            => 'required|numeric',
            'seller_name'      => 'nullable|string',
            'seller_cnic'      => 'nullable|string',
            'seller_phone'     => 'nullable|string',
            'buyer_name'       => 'nullable|string',
            'buyer_cnic'       => 'nullable|string',
            'buyer_phone'      => 'nullable|string',
            'car_make'         => 'nullable|string',
            'car_model'        => 'nullable|string',
            'car_year'         => 'nullable|string',
            'car_registration' => 'nullable|string',
            'notes'            => 'nullable|string',
            'seller_customer_id' => 'nullable|exists:customers,id',
            'buyer_customer_id'  => 'nullable|exists:customers,id',
            'seller_owner_id'    => 'nullable|exists:owners,id',
            'buyer_owner_id'     => 'nullable|exists:owners,id',
        ]);

        $entry = SellPurchaseEntry::create($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('sell-purchase/docs', 'public');
                $entry->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => in_array($file->extension(), ['jpg','jpeg','png','gif']) ? 'image' : 'document',
                ]);
            }
        }

        return back()->with('success', 'Entry saved!');
    }

    public function destroy(SellPurchaseEntry $entry)
    {
        $this->authorize('manage sell purchase');
        $entry->delete();
        return back()->with('success', 'Entry deleted!');
    }
}

// ==================== CONSTRUCTION ====================
class ConstructionController extends Controller
{
    public function index(Request $request)
    {
        $query = ConstructionItem::with('market');
        if ($request->market_id) $query->where('market_id', $request->market_id);
        if ($request->search)    $query->where('item_name', 'like', "%{$request->search}%");
        $items   = $query->latest()->paginate(20);
        $markets = Market::all();
        $total   = $query->sum('total');
        return view('construction.index', compact('items', 'markets', 'total'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage construction');
        $data = $request->validate([
            'market_id'    => 'nullable|exists:markets,id',
            'project_name' => 'required|string',
            'item_name'    => 'required|string',
            'quantity'     => 'required|numeric|min:0',
            'unit'         => 'required|string',
            'measurement'  => 'nullable|string',
            'unit_price'   => 'required|numeric|min:0',
            'total'        => 'required|numeric|min:0',
            'date'         => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        ConstructionItem::create($data);
        return back()->with('success', 'Construction item added!');
    }

    public function destroy(ConstructionItem $item)
    {
        $this->authorize('manage construction');
        $item->delete();
        return back()->with('success', 'Item removed!');
    }
}

// ==================== OWNER LEDGER ====================
class OwnerLedgerController extends Controller
{
    public function index(Request $request)
    {
        $owners = Owner::orderBy('name')->get();
        $selectedOwner = null;
        $ledgers = collect();
        $balance = 0;

        if ($request->owner_id) {
            $selectedOwner = Owner::findOrFail($request->owner_id);
            $ledgers = OwnerLedger::with(['market','shop'])
                ->where('owner_id', $request->owner_id)
                ->latest('date')
                ->paginate(20);
            $credits = OwnerLedger::where('owner_id', $request->owner_id)->where('transaction_type','credit')->sum('amount');
            $debits  = OwnerLedger::where('owner_id', $request->owner_id)->where('transaction_type','debit')->sum('amount');
            $balance = $credits - $debits;
        }

        $markets = Market::all();
        $shops   = Shop::with('market')->get();
        return view('owners.index', compact('owners', 'selectedOwner', 'ledgers', 'balance', 'markets', 'shops'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage owners');
        $data = $request->validate([
            'owner_id'         => 'required|exists:owners,id',
            'market_id'        => 'nullable|exists:markets,id',
            'shop_id'          => 'nullable|exists:shops,id',
            'transaction_type' => 'required|in:debit,credit',
            'amount'           => 'required|numeric|min:0.01',
            'date'             => 'required|date',
            'description'      => 'nullable|string',
            'reference'        => 'nullable|string',
        ]);

        OwnerLedger::create($data);
        return back()->with('success', 'Ledger entry added!');
    }

    public function destroy(OwnerLedger $ownerLedger)
    {
        $this->authorize('manage owners');
        $ownerLedger->delete();
        return back()->with('success', 'Entry deleted!');
    }
}

// ==================== CUSTOMERS ====================
class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['shop.market', 'linkedUser']);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('cnic', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }
        $customers = $query->latest()->paginate(15);
        $shops     = Shop::with('market')->where('type','instalment')->get();
        return view('customers.index', compact('customers', 'shops'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['shop.market', 'shop.payments', 'linkedUser', 'documents']);
        return view('customers.show', compact('customer'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage customers');
        $data = $request->validate([
            'name'           => 'required|string',
            'phone'          => 'nullable|string',
            'cnic'           => 'nullable|string',
            'address'        => 'nullable|string',
            'email'          => 'nullable|email',
            'linked_user_id' => 'nullable|exists:users,id',
            'shop_id'        => 'nullable|exists:shops,id',
            'notes'          => 'nullable|string',
        ]);

        $customer = Customer::create($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('customers/docs', 'public');
                $customer->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => in_array($file->extension(), ['jpg','jpeg','png','gif']) ? 'image' : 'document',
                ]);
            }
        }

        return redirect()->route('customers.show', $customer)->with('success', 'Customer added!');
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('manage customers');
        $data = $request->validate([
            'name'           => 'required|string',
            'phone'          => 'nullable|string',
            'cnic'           => 'nullable|string',
            'address'        => 'nullable|string',
            'email'          => 'nullable|email',
            'linked_user_id' => 'nullable|exists:users,id',
            'shop_id'        => 'nullable|exists:shops,id',
            'notes'          => 'nullable|string',
        ]);
        $customer->update($data);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('customers/docs', 'public');
                $customer->documents()->create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => in_array($file->extension(), ['jpg','jpeg','png','gif']) ? 'image' : 'document',
                ]);
            }
        }

        return back()->with('success', 'Customer updated!');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('manage customers');
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted!');
    }

    public function deleteDocument(CustomerDocument $document)
    {
        Storage::disk('public')->delete($document->path);
        $document->delete();
        return back()->with('success', 'Document removed!');
    }
}

// ==================== DASHBOARD ====================
class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'markets'       => \App\Models\Market::count(),
            'shops'         => \App\Models\Shop::count(),
            'customers'     => \App\Models\Customer::count(),
            'owners'        => \App\Models\Owner::count(),
            'total_income'  => \App\Models\ShopPayment::sum('amount'),
            'pending'       => \App\Models\Shop::selectRaw('SUM(total_amount - paid_amount) as pending')->value('pending') ?? 0,
            'rent_this_month' => \App\Models\RentEntry::whereMonth('date', now()->month)->sum('amount_paid'),
            'construction_total' => \App\Models\ConstructionItem::sum('total'),
        ];
        $recentPayments = \App\Models\ShopPayment::with(['shop.market'])->latest()->take(5)->get();
        return view('dashboard', compact('stats', 'recentPayments'));
    }
}

// ==================== USER MANAGEMENT ====================
class UserManagementController extends Controller
{
    public function index(Request $request)
    {

        $query = User::with('roles');
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }
        $users = $query->paginate(15);
        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('manage users');
        $request->validate(['role' => 'required|in:admin,viewer']);
        $user->syncRoles([$request->role]);
        return back()->with('success', 'Role updated!');
    }
}
