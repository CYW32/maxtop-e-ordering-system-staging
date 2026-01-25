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
     * Fulfills Section 3.a Whitelist Logic.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $catalogId = $user->getEffectiveCatalogId();

        // Fulfills Request: If the assigned catalog is deactivated, show nothing
        $catalog = \App\Models\Catalog::find($catalogId);
        if (! $catalog || $catalog->status === 'deactive') {
            return view('customer.products.index', ['items' => collect(), 'availableCategories' => collect()]);
        }

        // Fulfills Whitelist Logic + Request: Only show 'active' items [7]
        $query = Item::where('status', 'active')
            ->whereHas('catalogs', function ($q) use ($catalogId) {
                $q->where('catalogs.id', $catalogId);
            });

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category)
                    ->where('categories.status', 'active'); // Fulfills Category deactivation
            });
        }

        $items = $query->with('categories')->get();

        // Only show active categories containing active items for this catalog
        $availableCategories = Category::where('status', 'active')
            ->whereHas('items', function ($q) use ($catalogId) {
                $q->where('items.status', 'active')
                    ->whereHas('catalogs', fn ($c) => $c->where('catalogs.id', $catalogId));
            })->get();

        return view('customer.products.index', compact('items', 'availableCategories'));
    }
}
