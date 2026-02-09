<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

// Note: For actual compression, you would typically use 'Intervention Image' library
// composer require intervention/image

class ItemController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_items'); // Minimum requirement [9]

        $query = Item::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('sku', 'like', "%{$request->search}%");
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        return view('admin.items.index', compact('items'));
    }

    public function create()
{
    Gate::authorize('create_items');
    
    // ARCHITECTURE FIX: Fetch all catalogs to enable item-side whitelisting
    $catalogs = \App\Models\Catalog::orderBy('name')->get();
    
    return view('admin.items.create', compact('catalogs'));
}

public function store(Request $request)
{
    \Illuminate\Support\Facades\Gate::authorize('create_items');

    $validated = $request->validate([
        'sku' => 'required|unique:items,sku',
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string|max:2000', // Fulfills Requirement
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'status' => 'required|in:active,deactive',
        'catalogs' => 'nullable|array', // Fulfills Requirement
        'catalogs.*' => 'exists:catalogs,id',
        'uoms' => 'nullable|array',
        'uoms.*.uom_name' => 'required|string|max:100',
        'uoms.*.rate_qty' => 'required|integer|min:1',
        'uoms.*.price' => 'required|numeric|min:0',
        'uoms.*.status' => 'required|in:active,inactive',
    ]);

    \Illuminate\Support\Facades\DB::transaction(function () use ($request, $validated) {
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('items', 'public');
        }

        // Strict UOM Auto-Inactivation Rule [Addendum 5.a]
        $itemStatus = $request->status;
        if (!$request->has('uoms') || count($request->uoms) === 0) {
            $itemStatus = 'deactive';
        }

        $item = \App\Models\Item::create(array_merge($validated, ['status' => $itemStatus]));

        // Synchronize Catalog Assignments [Backbone 3.a.3]
        if ($request->has('catalogs')) {
            $item->catalogs()->sync($request->input('catalogs', []));
        }

        if ($request->has('categories')) {
            $item->categories()->sync($request->input('categories', []));
        }

        if ($request->has('uoms')) {
            foreach ($request->uoms as $uomData) {
                $item->uoms()->create($uomData);
            }
        }
    });

    return redirect()->route('items.index')->with('success', 'Product and catalog assignments established.');
}

public function edit(Item $item)
{
    Gate::authorize('edit_items');
    
    // Load catalogs and categories for management
    $catalogs = \App\Models\Catalog::orderBy('name')->get();
    $categories = \App\Models\Category::orderBy('name')->get();
    
    return view('admin.items.edit', compact('item', 'catalogs', 'categories'));
}

public function update(Request $request, Item $item)
{
    \Illuminate\Support\Facades\Gate::authorize('edit_items');

    $validated = $request->validate([
        'sku' => 'required|unique:items,sku,'.$item->id, // Resolved SKU collision [1]
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string|max:2000', // Fulfills Requirement
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'status' => 'required|in:active,deactive',
        'catalogs' => 'nullable|array', // Fulfills Requirement
        'catalogs.*' => 'exists:catalogs,id',
        'uoms' => 'nullable|array',
        'uoms.*.id' => 'nullable|exists:uoms,id',
        'uoms.*.uom_name' => 'required|string|max:100',
        'uoms.*.rate_qty' => 'required|integer|min:1',
        'uoms.*.price' => 'required|numeric|min:0',
        'uoms.*.status' => 'required|in:active,inactive',
    ]);

    \Illuminate\Support\Facades\DB::transaction(function () use ($item, $request, $validated) {
        if ($request->hasFile('image')) {
            if ($item->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($item->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('items', 'public');
        }

        $item->update($validated);

        // Sync Catalogs [Backbone Whitelist Logic]
        if ($request->has('catalogs')) {
            $item->catalogs()->sync($request->input('catalogs', []));
        }

        if ($request->has('categories')) {
            $item->categories()->sync($request->input('categories', []));
        }

        if ($request->has('uoms')) {
            $incomingUomIds = [];
            foreach ($request->uoms as $uomData) {
                $uom = $item->uoms()->withTrashed()->updateOrCreate(
                    ['id' => $uomData['id'] ?? null],
                    $uomData
                );
                $uom->restore(); 
                $incomingUomIds[] = $uom->id;
            }
            $orphans = $item->uoms()->withTrashed()->whereNotIn('id', $incomingUomIds)->get();
            foreach ($orphans as $orphan) {
                $orphan->canBeDeleted() ? $orphan->forceDelete() : $orphan->update(['status' => 'inactive']);
            }
        }
    });

    return redirect()->route('items.index')->with('success', 'Product details and catalog visibility updated.');
}

    public function destroy(Item $item)
    {
        // Requirement 3C: Deletion Restriction Logic [2]
        if (! $item->canBeDeleted()) {
            return redirect()->back()->with('error', 'Item has transactions and cannot be deleted.');
        }

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
