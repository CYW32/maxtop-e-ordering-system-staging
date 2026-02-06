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
        // Requirement: Needs View AND Create
        if (! auth()->user()->can('view_items') || ! auth()->user()->can('create_items')) {
            abort(403, 'Unauthorized creation access.');
        }

        return view('admin.items.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create_items');

        $validated = $request->validate([
            'sku' => 'required|unique:items,sku',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $validated['image_path'] = $path;
        }

        // FIX: Assign the created instance to $item so it can be used for relationship syncing
        $item = Item::create($validated);

        // Fulfills Category Assignment requirement
        $item->categories()->sync($request->input('categories', []));

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        // Requirement: Needs View AND Create AND Edit
        if (! auth()->user()->can('view_items') ||
            ! auth()->user()->can('create_items') ||
            ! auth()->user()->can('edit_items')) {
            abort(403, 'Unauthorized edit access.');
        }

        return view('admin.items.edit', compact('item'));
    }

    /**
     * Fulfills Addendum 5.a & 5.c: Synchronize Items and dynamic UOMs.
     */
    public function update(Request $request, Item $item)
    {
        \Illuminate\Support\Facades\Gate::authorize('edit_items');

        $validated = $request->validate([
            'sku' => 'required|unique:items,sku,'.$item->id,
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'sometimes|in:active,deactive',
            // UOM Validation Logic [5.a]
            'uoms' => 'nullable|array',
            'uoms.*.id' => 'nullable|exists:uoms,id',
            'uoms.*.uom_name' => 'required|string|max:100',
            'uoms.*.rate_qty' => 'required|integer|min:1',
            'uoms.*.price' => 'required|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($item, $request, $validated) {
            // Handle image updates per Section 7.b
            if ($request->hasFile('image')) {
                if ($item->image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($item->image_path);
                }
                $validated['image_path'] = $request->file('image')->store('items', 'public');
            }

            // 1. Update Core Item Details
            $item->update($validated);

            // 2. Synchronize Categories
            if ($request->has('categories')) {
                $item->categories()->sync($request->input('categories', []));
            }

            // 3. Synchronize Unit of Measure (UOM) Collection [5.a]
            if ($request->has('uoms')) {
                $incomingUomIds = [];

                foreach ($request->uoms as $uomData) {
                    $uom = $item->uoms()->updateOrCreate(
                        ['id' => $uomData['id'] ?? null],
                        [
                            'uom_name' => $uomData['uom_name'],
                            'rate_qty' => $uomData['rate_qty'],
                            'price' => $uomData['price'],
                        ]
                    );

                    // Handle Visibility: Restore if 'is_active' is checked, otherwise Soft Delete [1]
                    isset($uomData['is_active']) ? $uom->restore() : $uom->delete();

                    $incomingUomIds[] = $uom->id;
                }

                // 4. Handle Deletion of Removed (Orphaned) UOMs [5.c]
                // We find UOMs that were on the item but not in the current form submission
                $orphans = $item->uoms()->withTrashed()->whereNotIn('id', $incomingUomIds)->get();
                foreach ($orphans as $orphan) {
                    if ($orphan->canBeDeleted()) {
                        $orphan->forceDelete(); // Hard Delete allowed if no history [1]
                    } else {
                        $orphan->delete(); // Fallback to Soft Delete (Hide) to preserve history
                    }
                }
            }
        });

        return redirect()->route('items.index')->with('success', 'Product and UOM configurations updated.');
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
