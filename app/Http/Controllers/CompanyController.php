<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Company;
use Illuminate\Http\Request; // FIXED: Added missing facade import
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_business_entities');

        $companys = Company::with(['catalog', 'parent', 'branches'])
            // 1. Use the Searchable trait's scope instead of manual where/orWhere
            ->when(!$request->filled('search'), function ($query) {
                $query->whereNull('parent_id');
            })

            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->input('search'));
            })

            ->latest()
            ->paginate(15)
            // 2. CRITICAL: Add this so pagination links remember the ?search=... term
            ->withQueryString();

        return view('admin.companys.index', compact('companys'));
    }

    public function create(Request $request)
    {
        $catalogs = Catalog::where('status', 'active')->orderBy('name')->get();
        $hqs = Company::whereNull('parent_id')->orderBy('company_name')->get();

        $preselectedParentId = $request->query('parent_id');
        $preselectedParent = $preselectedParentId ? Company::find($preselectedParentId) : null;

        return view('admin.companys.create', compact('catalogs', 'hqs', 'preselectedParent'));
    }

    /**
     * Fulfills Addendum 1.d: Register HQ or Branch with strict code requirements.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:companys,id',
            'company_code' => ['nullable', 'required_without:parent_id', 'unique:companys,company_code'],
            'branch_code' => ['nullable', 'required_with:parent_id', 'unique:companys,branch_code'],
            'catalog_id' => 'nullable|exists:catalogs,id',
            'company_reg_no' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
            'delivery_address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        Company::create($validated);

        return redirect()->route('companys.index')->with('success', 'Business entity registered successfully.');
    }

    public function edit(Company $company)
    {
        $catalogs = Catalog::where('status', 'active')->get();

        return view('admin.companys.edit', compact('company', 'catalogs'));
    }

    /**
     * ARCHITECTURE FIX: Security Lockdown.
     * Block the user from updating uneditable fields (Identity & Hierarchy).
     */
    public function update(Request $request, Company $company)
    {
        // 1. Validate ONLY the fields allowed to be changed [Addendum 3.c]
        $validated = $request->validate([
            'catalog_id' => 'nullable|exists:catalogs,id',
            'company_reg_no' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
            'delivery_address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        // 2. SECURITY GUARD: Explicitly exclude identity fields from the request
        // to prevent forged POST injections of 'company_name', 'company_code', or 'parent_id'.
        $updateData = $request->only([
            'catalog_id', 'company_reg_no', 'pic_name', 'pic_phone',
            'delivery_address', 'postal_code', 'city', 'state',
        ]);

        $company->update($updateData);

        return redirect()->route('companys.index')->with('success', 'Business logistics and contact updated.');
    }
}
