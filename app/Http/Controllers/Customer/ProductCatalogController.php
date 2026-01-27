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

        $catalog = \App\Models\Catalog::find($catalogId);
        if (! $catalog || $catalog->status === 'deactive') {
            return view('customer.products.index', ['items' => collect(), 'availableCategories' => collect()]);
        }

        $query = Item::where('status', 'active')
            ->whereHas('catalogs', function ($q) use ($catalogId) {
                $q->where('catalogs.id', $catalogId);
            });

        // ARCHITECTURE FIX: Enable item name and SKU search within the whitelist
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $request->category));
        }

        $items = $query->with('categories')->latest()->paginate(12)->withQueryString();

        $availableCategories = Category::where('status', 'active')
            ->whereHas('items', function ($q) use ($catalogId) {
                $q->where('items.status', 'active')
                    ->whereHas('catalogs', fn ($c) => $c->where('catalogs.id', $catalogId));
            })->get();

        return view('customer.products.index', compact('items', 'availableCategories'));
    }
}
