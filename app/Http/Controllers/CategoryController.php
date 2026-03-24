<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view_items');

        $query = Category::query();

        // 1. Filter by Name (Search)
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // 2. Filter by Operational Status (Added this logic)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query->withCount('items')->latest()->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        Gate::authorize('create_items');

        $items = Item::orderBy('name')->get();

        return view('admin.categories.create', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create_items');

        // FIXED: Reverted to standard creation validation rules and updated 'inactive'
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'status' => ['required', 'in:active,inactive'],
            'items' => ['nullable', 'array'],
            'items.*' => ['exists:items,id'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'status' => $validated['status'],
        ]);

        $category->items()->sync($request->input('items', []));

        return redirect()
            ->route('categories.index')
            ->with('success', "Category ({$category->name}) been created successfully.");
    }

    public function show(Category $category): View
    {
        // Check if user has permission to view
        Gate::authorize('view_items');

        // Load the items assigned to this category so we can display them
        $category->load('items');

        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        Gate::authorize('edit_items');

        $canBeDeleted = !$category
            ->items()
            ->whereHas('orderItems.order', function ($q) {
                $q->whereIn('status', ['approved', 'completed']);
            })
            ->exists();

        $items = Item::orderBy('name')->get();
        $assignedItemIds = $category->items()->pluck('items.id')->toArray();

        return view('admin.categories.edit', compact('category', 'canBeDeleted', 'items', 'assignedItemIds'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        Gate::authorize('edit_items');

        // FIXED: Changed 'deactive' to 'inactive' here
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'status' => ['required', 'in:active,inactive'],
            'items' => ['nullable', 'array'],
            'items.*' => ['exists:items,id'],
        ]);

        // FIXED: Changed 'deactive' to 'inactive' here
        $isDeactivating = $category->status === 'active' && $validated['status'] === 'inactive';

        $category->update([
            'name' => $validated['name'],
            'status' => $validated['status'],
        ]);

        $category->items()->sync($request->input('items', []));

        $message = $isDeactivating ? "Category ({$category->name}) deactivated successfully." : "Category ({$category->name}) updated successfully.";

        return redirect()->route('categories.index')->with('success', $message);
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('edit_items');

        $hasHistory = $category
            ->items()
            ->whereHas('orderItems.order', function ($q) {
                $q->whereIn('status', ['approved', 'completed']);
            })
            ->exists();

        if ($hasHistory) {
            return redirect()->back()->with('error', __('Security Violation: Transaction records exist. Use "Inactive" status to hide instead.'));
        }

        $categoryName = $category->name;

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "Category ({$categoryName}) deleted successfully.");
    }
}
