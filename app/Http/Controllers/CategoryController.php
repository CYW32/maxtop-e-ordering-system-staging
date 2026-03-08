<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Fulfills Requirement: Permission-gated Registry.
     */
    public function index(Request $request): View
    {
        Gate::authorize('view_items');

        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Optimized with Item Counts for oversight performance [Backbone 15.a]
        $categories = $query->withCount('items')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        Gate::authorize('create_items');
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create_items');

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', 'unique:categories,name'],
            'status' => ['required', 'in:active,deactive'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', __('Category successfully initialized.'));
    }

    /**
     * Fulfills Requirement: Edit with Deletion Guard Logic [Backbone 9.c.1].
     */
    public function edit(Category $category): View
    {
        Gate::authorize('edit_items');

        // ARCHITECTURE CHECK: Permissibility of Hard Deletion.
        // Restricted if items within this category are part of an Approved/Completed order.
        $canBeDeleted = !$category->items()->whereHas('orderItems.order', function ($q) {
            $q->whereIn('status', ['approved', 'completed']);
        })->exists();

        return view('admin.categories.edit', compact('category', 'canBeDeleted'));
    }

    /**
     * ARCHITECTURE FIX: Secure Update Protocol.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        Gate::authorize('edit_items');

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'status' => ['required', 'in:active,deactive'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', __('Category configuration updated.'));
    }

    /**
     * ARCHITECTURE GUARD: Data Integrity Policy [Backbone 9.c.1].
     */
    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('edit_items');

        // Secondary server-side security re-check
        $hasHistory = $category->items()->whereHas('orderItems.order', function ($q) {
            $q->whereIn('status', ['approved', 'completed']);
        })->exists();

        if ($hasHistory) {
            return redirect()->back()->with('error', __('Security Violation: Transaction records exist. Use "Inactive" status to hide instead.'));
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', __('Category purged from registry.'));
    }
}
