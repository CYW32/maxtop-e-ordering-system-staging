<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        // Fulfills Addendum 3.c: Visualize HQ/Branch relationships [2]
        $companys = Company::with(['catalog', 'parent', 'branches'])
            ->whereNull('parent_id') // Start with HQs to build the hierarchy view
            ->when($request->search, function ($q) use ($request) {
                $q->where('company_name', 'like', "%{$request->search}%")
                    ->orWhere('company_code', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(15);

        return view('admin.companys.index', compact('companys'));
    }

    public function create(Request $request)
    {
        // Fetch active catalogs for assignment [Addendum 1.b]
        $catalogs = \App\Models\Catalog::where('status', 'active')->orderBy('name')->get();

        // Fetch existing HQs to populate the parent selection dropdown [Addendum 3.c]
        $hqs = Company::whereNull('parent_id')->orderBy('company_name')->get();

        // Check if we are creating a branch for a specific HQ from its edit page
        $preselectedParentId = $request->query('parent_id');
        $preselectedParent = $preselectedParentId ? Company::find($preselectedParentId) : null;

        return view('admin.companys.create', compact('catalogs', 'hqs', 'preselectedParent'));
    }

    public function store(Request $request)
    {
        // Fulfills Addendum 1.d Mutex: company_code for HQ, branch_code for Branch
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:companys,id',
            // Logic: company_code is required ONLY if it's an HQ (parent_id is null)
            'company_code' => ['nullable', 'required_without:parent_id', 'unique:companys,company_code'],
            // Logic: branch_code is required ONLY if it's a Branch (parent_id is present)
            'branch_code' => ['nullable', 'required_with:parent_id', 'unique:companys,branch_code'],
            'catalog_id' => 'nullable|exists:catalogs,id',
            'company_reg_no' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
            'delivery_address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ]);

        Company::create($validated);

        return redirect()->route('companys.index')->with('success', 'Business entity registered successfully.');
    }

    public function edit(Company $company)
    {
        $hqs = Company::whereNull('parent_id')->where('id', '!=', $company->id)->get();
        $catalogs = Catalog::where('status', 'active')->get();

        return view('admin.companys.edit', compact('company', 'hqs', 'catalogs'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:companys,id',
            'company_code' => ['nullable', 'required_without:parent_id', Rule::unique('companys')->ignore($company->id)],
            'branch_code' => ['nullable', 'required_with:parent_id', Rule::unique('companys')->ignore($company->id)],
            'catalog_id' => 'nullable|exists:catalogs,id',
            'company_reg_no' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
            'delivery_address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $company->update($validated);

        return redirect()->route('companys.index')->with('success', 'Business details updated.');
    }
}
