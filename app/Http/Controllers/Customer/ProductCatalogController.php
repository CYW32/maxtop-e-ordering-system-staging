<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    /**
     * Display the whitelisted products for the logged-in customer.
     * Fulfills Section 3.a Whitelist Logic and Search/Category Filtering.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $catalogId = $user->getEffectiveCatalogId();
        $catalog = \App\Models\Catalog::find($catalogId);

        // Fail-safe if no valid catalog is assigned or it's deactivated
        if (! $catalog || $catalog->status === 'deactive') {
            return view('customer.products.index', [
                'items' => collect(), 
                'availableCategories' => collect(), 
                'draftItems' => collect()
            ]);
        }

        // Base Query: Only Active items inside the customer's assigned catalog
        $query = Item::where('status', 'active')
            ->whereHas('catalogs', fn ($q) => $q->where('catalogs.id', $catalogId));

        // 1. Keyword Search Filter (Name or SKU)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // 2. Category Dropdown Filter
        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $request->category));
        }

        // Fetch Paginated Items (withQueryString ensures pagination links remember the current search/category)
        $items = $query->with(['categories', 'activeUoms'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Fetch categories that actually contain active products in this specific catalog
        $availableCategories = Category::where('status', 'active')
            ->whereHas('items', function ($q) use ($catalogId) {
                $q->where('items.status', 'active')
                  ->whereHas('catalogs', fn ($c) => $c->where('catalogs.id', $catalogId));
            })
            ->get();

        // Fetch draft items to display "Already in Cart" indicator
        $draftItems = $user->currentDraft()?->items ?? collect();

        return view('customer.products.index', compact('items', 'availableCategories', 'draftItems'));
    }
}