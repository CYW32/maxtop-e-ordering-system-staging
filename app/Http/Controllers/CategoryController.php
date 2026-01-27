<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with item counts.
     */
    public function index(Request $request) // Inject Request
    {
        Gate::authorize('view_items');

        $query = Category::withCount('items');

        // ARCHITECTURE FIX: Apply Searchable scope
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $categories = $query->latest()
            ->paginate(10)
            ->withQueryString(); // Preserve search on pagination [6]

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        Gate::authorize('create_items');

        $validated = $request->validate([
            'name' => 'required|unique:categories,name|max:255',
        ]);

        Category::create($validated);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the category and its assigned items.
     */
    public function edit(Category $category)
    {
        Gate::authorize('edit_items');

        // Fetch all items to allow staff to group them into this category
        $items = Item::orderBy('name')->get();
        $assignedItemIds = $category->items()->pluck('items.id')->toArray();

        return view('admin.categories.edit', compact('category', 'items', 'assignedItemIds'));
    }

    /**
     * Update the category name and synchronize item groupings.
     */
    public function update(Request $request, Category $category)
    {
        Gate::authorize('edit_items');

        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories,name,'.$category->id,
            'items' => 'nullable|array',
            'items.*' => 'exists:items,id',
            'status' => 'sometimes|in:active,deactive',
        ]);

        $category->update([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? $category->status,
        ]);

        // FIX: Only sync if item list is present (prevents wiping during status toggle)
        if ($request->has('items')) {
            $category->items()->sync($request->input('items', []));
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the category (Items are not deleted).
     */
    public function destroy(Category $category)
    {
        // Ensure only authorized staff can trigger deletion
        \Illuminate\Support\Facades\Gate::authorize('edit_items');

        // Fulfills Section 3.c.1 Guard
        if (! $category->canBeDeleted()) {
            return redirect()->back()->with('error', 'This category contains items linked to historical orders and cannot be deleted. Use "Deactivate" instead.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category removed. Items remain in the system.');
    }
}
