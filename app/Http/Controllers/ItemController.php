<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Catalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Models\Uom;

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

        // 1. Multi-attribute search (SKU or Name)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        // 2. Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Category Filter
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category); // Specifying table name to avoid ambiguity
            });
        }

        // Fetch categories for the filter dropdown
        $categories = Category::orderBy('name')->get();

        // ARCHITECTURE FIX: Pricing is now in UOMs. Removed sorting by 'price' [Addendum 5.a].
        // Eager load UOMs and Catalogs for performance.
        $items = $query
            ->with(['activeUoms', 'catalogs'])
            ->latest()
            ->paginate(15)
            ->withQueryString(); // Ensures pagination links keep the current filter parameters

        return view('admin.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        Gate::authorize('create_items');

        // Fetch all active categories and catalogs for the search selection boxes
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $catalogs = Catalog::where('status', 'active')->orderBy('name')->get();

        // Pass both variables to the view
        return view('admin.items.create', compact('categories', 'catalogs'));
    }

    /**
     * ARCHITECTURE FIX: Atomic Product Initialization.
     * Fulfills Requirement: Nested UOM persistence during Item creation [11.a].
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create_items');

        // 1. HARDENED VALIDATION [Backbone 5.c]
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'sku' => ['required', 'string', 'unique:items,sku'],
                'description' => ['nullable', 'string'],
                'status' => ['required', 'in:active,inactive'],
                'images' => ['nullable', 'array'],
                'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

                // Whitelist Sync Arrays
                'categories' => ['nullable', 'array'],
                'categories.*' => ['exists:categories,id'],
                'catalogs' => ['nullable', 'array'],
                'catalogs.*' => ['exists:catalogs,id'],

                // UOM Array Validation [Pure UOM Model]
                'uoms' => ['required', 'array', 'min:1'], // Fulfills Base-Unit Guard [4.a.3]
                'uoms.*.uom_name' => ['required', 'string', 'max:50'],
                'uoms.*.rate_qty' => ['required', 'numeric', 'min:1'],
                'uoms.*.price' => ['required', 'numeric', 'min:0'],
                'uoms.*.status' => ['required', 'in:active,inactive'],
            ],
            [
                'uoms.required' => __('You must initialize at least one packaging unit (Base Unit Rate 1) for catalog visibility.'),
            ],
        );

        // 2. ATOMIC TRANSACTION GUARD [Backbone 11.a]
        // BUGFIX: Return the created $item from the transaction so we can use it in the success message below
        $item = DB::transaction(function () use ($request, $validated) {
            // Create Core Identity
            $newItem = Item::create($request->only(['name', 'sku', 'description', 'status']));

            // Handle Media
            if ($request->hasFile('images')) {
                $paths = [];
                foreach ($request->file('images') as $image) {
                    $paths[] = $image->store('items', 'public');
                }
                $newItem->update(['image_path' => $paths]);
            }

            // Sync Whitelist Assignments [Backbone 3.a.3]
            $newItem->categories()->sync($validated['categories'] ?? []);
            $newItem->catalogs()->sync($validated['catalogs'] ?? []);

            // 3. PERSIST PACKAGING TIERS (The Missing Link)
            $newItem->uoms()->createMany($validated['uoms']);

            return $newItem;
        });

        // Redirect back to Index after creation with the "added successfully" message
        return redirect()
            ->route('items.index')
            ->with('success', "Item ({$item->name}) added successfully !");
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
            'name' => ['required', 'string', 'max:255'],
            // SYSTEM SKU LOCK: 'sku' validation completely removed so it cannot be tampered with.
            'status' => ['required', 'in:active,inactive'],
            'categories' => ['nullable', 'array'],
            'catalogs' => ['nullable', 'array'],

            // ADD THE IMAGE VALIDATION HERE:
            'existing_images' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

            // Nested UOM Validation
            'uoms' => ['nullable', 'array'],
            'uoms.*.id' => ['nullable', 'exists:uoms,id'],
            'uoms.*.uom_name' => ['required_with:uoms', 'string', 'max:50'],
            'uoms.*.rate_qty' => ['required_with:uoms', 'numeric', 'min:1'],
            'uoms.*.price' => ['required_with:uoms', 'numeric', 'min:0'],
            'uoms.*.status' => ['required_with:uoms', 'in:active,inactive'], // Accept both for transition
        ]);

        DB::transaction(function () use ($item, $request, $validated) {
            // 1. Sync Core Attributes
            // SYSTEM SKU LOCK: 'sku' removed from the update payload. It is permanently locked in DB.
            $item->update($request->only(['name', 'description', 'status']));

            // 1. Get the list of existing images the user decided to KEEP (sent from the hidden inputs)
            $keptImages = $request->input('existing_images', []);

            // 2. Add any NEWly uploaded images to that list
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $keptImages[] = $image->store('items', 'public');
                }
            }

            // 3. Sync the final combined list to the database
            $item->update(['image_path' => $keptImages]);

            // 2. Whitelist Syncing [4.a.3]
            $item->categories()->sync($validated['categories'] ?? []);
            $item->catalogs()->sync($validated['catalogs'] ?? []);

            // 3. Process UOM Lifecycle
            $incomingUomIds = collect($validated['uoms'] ?? [])
                ->pluck('id')
                ->filter()
                ->toArray();

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
                $statusValue = $uomData['status'] === 'active' ? 'active' : 'inactive';

                $item->uoms()->updateOrCreate(
                    ['id' => $uomData['id'] ?? null],
                    [
                        'uom_name' => strtoupper($uomData['uom_name']),
                        'rate_qty' => $uomData['rate_qty'],
                        'price' => $uomData['price'],
                        'status' => (string) $statusValue,
                    ],
                );
            }
        });

        // Redirect back to the Item Master List (index) after updating
        return redirect()
            ->route('items.index')
            ->with('success', "Product ({$item->name}) updated successfully !");
    }

    public function destroy(Item $item)
    {
        // 1. Guard: Check if item is tied to existing transactions
        if (!$item->canBeDeleted()) {
            return redirect()
                ->route('items.index')
                ->with('error', "Product ({$item->name}) is linked to existing order transactions and cannot be deleted to maintain system integrity.");
        }

        // 2. Remove associated media if it exists
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        // 3. Save the name before deleting so we can use it in the success message
        $itemName = $item->name;

        // 4. Execute Hard Delete
        $item->delete();

        // 5. Redirect with dynamic success message
        return redirect()
            ->route('items.index')
            ->with('success', "Product ({$itemName}) deleted successfully !");
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item): \Illuminate\View\View
    {
        Gate::authorize('view_items');

        // Eager load relationships so we can display them on the view page
        $item->load(['categories', 'catalogs', 'uoms']);

        return view('admin.items.show', compact('item'));
    }
}
