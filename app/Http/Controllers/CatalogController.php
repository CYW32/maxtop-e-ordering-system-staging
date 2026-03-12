<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_catalogs');

        $query = Catalog::query();

        if ($request->filled('search')) {
            $query->search($request->search); // Uses Searchable trait [1]
        }

        $catalogs = $query->withCount('items')->latest()->paginate(10)->withQueryString();

        return view('admin.catalogs.index', compact('catalogs'));
    }

public function create()
    {
        // Requirement: Needs View + Create
        if (! auth()->user()->can('view_catalogs') || ! auth()->user()->can('create_catalogs')) {
            abort(403);
        }

        // Fetch all items to populate the Whitelist Interface during creation
        $items = Item::orderBy('name')->get();

        return view('admin.catalogs.create', compact('items'));
    }

    public function store(Request $request)
    {
        // Requirement: Needs View + Create + Edit
        if (! auth()->user()->can('view_catalogs') ||
            ! auth()->user()->can('create_catalogs') ||
            ! auth()->user()->can('edit_catalogs')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalogs,name',
            'items' => 'nullable|array',
            'items.*' => 'exists:items,id',
        ]);

        $catalog = Catalog::create([
            'name' => $validated['name']
        ]);

        // Sync items if the whitelist data is present in the request
        if ($request->has('items')) {
            $catalog->items()->sync($request->input('items', []));
        }

        return redirect()->route('catalogs.index')->with('success', 'Catalog created successfully.');
    }

    public function edit(Catalog $catalog)
    {
        // Requirement: Needs View + Create + Edit
        if (! auth()->user()->can('view_catalogs') ||
            ! auth()->user()->can('create_catalogs') ||
            ! auth()->user()->can('edit_catalogs')) {
            abort(403);
        }

        // Fetch all items to populate the Whitelist Interface [2]
        $items = Item::orderBy('name')->get();

        // Get IDs of items already in this catalog for checkbox states
        $assignedItemIds = $catalog->items()->pluck('items.id')->toArray();

        return view('admin.catalogs.edit', compact('catalog', 'items', 'assignedItemIds'));
    }

    public function update(Request $request, Catalog $catalog)
    {
        if (! auth()->user()->can('view_catalogs') || ! auth()->user()->can('create_catalogs') || ! auth()->user()->can('edit_catalogs')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalogs,name,'.$catalog->id,
            'items' => 'nullable|array',
            'items.*' => 'exists:items,id',
            'status' => 'sometimes|in:active,deactive',
        ]);

        $catalog->update([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? $catalog->status,
        ]);

        // FIX: Only sync items if the whitelist data is present in the request
        if ($request->has('items')) {
            $catalog->items()->sync($request->input('items', []));
        }

        return redirect()->route('catalogs.index')->with('success', 'Catalog updated successfully.');
    }

    public function destroy(Catalog $catalog)
    {
        Gate::authorize('edit_catalogs');

        if (! $catalog->canBeDeleted()) {
            return redirect()->back()->with('error', 'Cannot delete catalog while customers are assigned to it.');
        }

        $catalog->delete();

        return redirect()->route('catalogs.index')->with('success', 'Catalog removed.');
    }
}
