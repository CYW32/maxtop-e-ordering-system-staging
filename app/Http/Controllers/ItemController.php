<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Catalog;
use Illuminate\Support\Facades\DB; // ARCHITECTURE FIX: Added missing DB Facade import
use Illuminate\Http\RedirectResponse; // ARCHITECTURE FIX: Resolved TypeError by importing framework class
use App\Models\Uom;

// Note: For actual compression, you would typically use 'Intervention Image' library
// composer require intervention/image

class ItemController extends Controller
{
    /**
     * Fulfills Requirement: Master Item Registry oversight.
     * ARCHITECTURE FIX: Removed price logic; optimized for status visibility.
     */
    public function index(Request $request)
    {
        Gate::authorize('view_items');

        $query = Item::query();

        // Multi-attribute search (SKU or Name)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        // ARCHITECTURE FIX: Pricing is now in UOMs. Removed sorting by 'price' [Addendum 5.a].
        // Eager load UOMs and Catalogs for performance.
        $items = $query->with(['activeUoms', 'catalogs'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        Gate::authorize('create_items');

        // ARCHITECTURE FIX: Fetch all catalogs to enable item-side whitelisting
        $catalogs = \App\Models\Catalog::orderBy('name')->get();

        return view('admin.items.create', compact('catalogs'));
    }

    /**
     * ARCHITECTURE FIX: Atomic Product Initialization.
     * Fulfills Requirement: Nested UOM persistence during Item creation [11.a].
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create_items');

        // 1. HARDENED VALIDATION [Backbone 5.c]
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'sku'          => ['required', 'string', 'unique:items,sku'],
            'description'  => ['nullable', 'string'],
            'status'       => ['required', 'in:active,inactive'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

            // Whitelist Sync Arrays
            'categories'   => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'catalogs'     => ['nullable', 'array'],
            'catalogs.*'   => ['exists:catalogs,id'],

            // UOM Array Validation [Pure UOM Model]
            'uoms'            => ['required', 'array', 'min:1'], // Fulfills Base-Unit Guard [4.a.3]
            'uoms.*.uom_name' => ['required', 'string', 'max:50'],
            'uoms.*.rate_qty' => ['required', 'numeric', 'min:1'],
            'uoms.*.price'    => ['required', 'numeric', 'min:0'],
            'uoms.*.status'   => ['required', 'in:active,inactive'],
        ], [
            'uoms.required' => __('You must initialize at least one packaging unit (Base Unit Rate 1) for catalog visibility.')
        ]);

        // 2. ATOMIC TRANSACTION GUARD [Backbone 11.a]
        DB::transaction(function () use ($request, $validated) {

            // Create Core Identity
            $item = Item::create($request->only(['name', 'sku', 'description', 'status']));

            // Handle Media
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('items', 'public');
                $item->update(['image_path' => $path]);
            }

            // Sync Whitelist Assignments [Backbone 3.a.3]
            $item->categories()->sync($validated['categories'] ?? []);
            $item->catalogs()->sync($validated['catalogs'] ?? []);

            // 3. PERSIST PACKAGING TIERS (The Missing Link)
            // We use createMany to efficiently insert the nested array into the uoms table.
            $item->uoms()->createMany($validated['uoms']);
        });

        return redirect()->route('items.index')
            ->with('success', __('Product entity and packaging configurations successfully registry-initialized.'));
    }

    /**
 * Fulfills Requirement: Advanced Assignment with Whitelist Oversight.
 */
    public function edit(Item $item): \Illuminate\View\View
    {
        Gate::authorize('edit_items');

        // ARCHITECTURE FIX: Eager load relationships to prevent N+1 queries.
        // Loads Categories, Catalogs, and UOMs for the Pure UOM Pricing Model.
        $item->load(['categories', 'catalogs', 'activeUoms']);

        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $catalogs = Catalog::where('status', 'active')->orderBy('name')->get();

        return view('admin.items.edit', compact('item', 'categories', 'catalogs'));
    }

    /**
     * ARCHITECTURE FIX: Nested UOM CRUD with Status Synchronization.
     * Resolves persistence failure by standardizing on 'inactive' [3.c.1].
     */
    public function update(Request $request, Item $item): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit_items');

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['required', 'string', 'unique:items,sku,' . $item->id],
            'status'      => ['required', 'in:active,inactive'],
            'categories'  => ['nullable', 'array'],
            'catalogs'    => ['nullable', 'array'],

            // Nested UOM Validation
            'uoms'             => ['nullable', 'array'],
            'uoms.*.id'        => ['nullable', 'exists:uoms,id'],
            'uoms.*.uom_name'  => ['required_with:uoms', 'string', 'max:50'],
            'uoms.*.rate_qty'  => ['required_with:uoms', 'numeric', 'min:1'],
            'uoms.*.price'     => ['required_with:uoms', 'numeric', 'min:0'],
            'uoms.*.status'    => ['required_with:uoms', 'in:active,inactive'], // Accept both for transition
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($item, $request, $validated) {
            // 1. Sync Core Attributes
            $item->update($request->only(['name', 'sku', 'description', 'status']));

            // 2. Whitelist Syncing [4.a.3]
            $item->categories()->sync($validated['categories'] ?? []);
            $item->catalogs()->sync($validated['catalogs'] ?? []);

            // 3. Process UOM Lifecycle
            $incomingUomIds = collect($validated['uoms'] ?? [])->pluck('id')->filter()->toArray();

            // Deletion Guard: Preserve historical snapshots [6.b]
            $uomsToRemove = $item->uoms()->whereNotIn('id', $incomingUomIds)->get();
            foreach ($uomsToRemove as $oldUom) {
                if ($oldUom->orderItems()->exists()) {
                    $oldUom->update(['status' => 'inactive']);
                } else {
                    $oldUom->delete();
                }
            }

            // 4. Persistence with Strict String Binding
            foreach ($validated['uoms'] ?? [] as $uomData) {
                // MAPPER: Harmonize frontend 'deactive' to architectural 'inactive'
                $statusValue = ($uomData['status'] === 'active') ? 'active' : 'inactive';

                $item->uoms()->updateOrCreate(
                    ['id' => $uomData['id'] ?? null],
                    [
                        'uom_name' => strtoupper($uomData['uom_name']),
                        'rate_qty' => $uomData['rate_qty'],
                        'price'    => $uomData['price'],
                        'status'   => (string) $statusValue,
                    ]
                );
            }
        });

        return redirect()->route('items.index')
            ->with('success', __('Product entity and UOM configurations successfully registry-hardened.'));
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
