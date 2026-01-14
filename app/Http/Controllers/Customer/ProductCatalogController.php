<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class ProductCatalogController extends Controller
{
    /**
     * Display the whitelisted products for the logged-in customer.
     */
    public function index()
    {
        $user = auth()->user();

        // Fulfills Whitelist Logic: Uses the User model method to fetch items [2, 3]
        $items = $user->getVisibleItems();

        // Fulfills Inheritance: getVisibleItems() automatically checks parent catalog if branch is unassigned [2, 3]

        return view('customer.products.index', compact('items'));
    }
}
