<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_catalogs');

        $query = Catalog::query();

        // 1. Filter by Name (Search)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 2. Filter by Operational Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $catalogs = $query->withCount('items')->latest()->paginate(15)->withQueryString();

        return view('admin.catalogs.index', compact('catalogs'));
    }

    public function show(Catalog $catalog)
    {
        Gate::authorize('view_catalogs');

        $catalog->load('items');

        return view('admin.catalogs.show', compact('catalog'));
    }

    public function create()
    {
        if (!auth()->user()->can('view_catalogs') || !auth()->user()->can('create_catalogs')) {
            abort(403);
        }

        $items = Item::orderBy('name')->get();

        return view('admin.catalogs.create', compact('items'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('view_catalogs') || !auth()->user()->can('create_catalogs') || !auth()->user()->can('edit_catalogs')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalogs,name',
            'items' => 'nullable|array',
            'items.*' => 'exists:items,id',
        ]);

        $catalog = Catalog::create([
            'name' => $validated['name'],
        ]);

        if ($request->has('items')) {
            $catalog->items()->sync($request->input('items', []));
        }

        // DYNAMIC NOTIFICATION ADDED
        return redirect()
            ->route('catalogs.index')
            ->with('success', "Catalog ({$catalog->name}) created successfully.");
    }

    public function edit(Catalog $catalog)
    {
        if (!auth()->user()->can('view_catalogs') || !auth()->user()->can('create_catalogs') || !auth()->user()->can('edit_catalogs')) {
            abort(403);
        }

        // AMEND HERE: Load item categories and fetch all categories
        $items = Item::with('categories')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $assignedItemIds = $catalog->items()->pluck('items.id')->toArray();

        // AMEND HERE: Pass $categories to the view
        return view('admin.catalogs.edit', compact('catalog', 'items', 'categories', 'assignedItemIds'));
    }

    public function update(Request $request, Catalog $catalog)
    {
        if (!auth()->user()->can('view_catalogs') || !auth()->user()->can('create_catalogs') || !auth()->user()->can('edit_catalogs')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalogs,name,' . $catalog->id,
            'items' => 'nullable|array',
            'items.*' => 'exists:items,id',
            'status' => 'sometimes|in:active,inactive', // SQL ERROR FIXED HERE
        ]);

        $catalog->update([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? $catalog->status,
        ]);

        if ($request->has('items')) {
            $catalog->items()->sync($request->input('items', []));
        }

        // DYNAMIC NOTIFICATION ADDED
        return redirect()
            ->route('catalogs.index')
            ->with('success', "Catalog ({$catalog->name}) updated successfully.");
    }

    public function destroy(Catalog $catalog)
    {
        Gate::authorize('edit_catalogs');

        if (!$catalog->canBeDeleted()) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete Catalog ({$catalog->name}) while customers are assigned to it.");
        }

        // Store the name before deleting so we can use it in the success message
        $catalogName = $catalog->name;

        $catalog->delete();

        // DYNAMIC NOTIFICATION ADDED
        return redirect()
            ->route('catalogs.index')
            ->with('success', "Catalog ({$catalogName}) deleted successfully.");
    }
}
