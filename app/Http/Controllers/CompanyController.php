<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view_business_entities');

        $hqs = Company::whereNull('parent_id')->orderBy('company_name')->get();

        // 1. Determine if we need Flat View (Search active, or explicitly filtering branches)
        $isFlatView = $request->filled('search') || $request->type === 'branch';

        // Eager load everything needed for both views
        $query = Company::with(['catalog', 'parent', 'children.catalog', 'children.parent']);

        if ($isFlatView) {
            // --- FLAT VIEW (Search Mode) ---
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('company_name', 'like', '%' . $request->search . '%')
                        ->orWhere('company_code', 'like', '%' . $request->search . '%')
                        ->orWhere('branch_code', 'like', '%' . $request->search . '%');
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('type')) {
                if ($request->type === 'hq') {
                    $query->whereNull('parent_id');
                } elseif ($request->type === 'branch') {
                    $query->whereNotNull('parent_id');
                }
            }
            if ($request->filled('hq_id')) {
                $query->where(function ($q) use ($request) {
                    $q->where('id', $request->hq_id)->orWhere('parent_id', $request->hq_id);
                });
            }
        } else {
            // --- ACCORDION VIEW (Default Mode) ---
            $query->whereNull('parent_id');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('hq_id')) {
                $query->where('id', $request->hq_id);
            }
        }

        $companys = $query->latest()->paginate(15)->withQueryString();

        return view('admin.companys.index', compact('companys', 'hqs', 'isFlatView'));
    }

    public function create(Request $request)
    {
        $catalogs = Catalog::where('status', 'active')->orderBy('name')->get();
        $hqs = Company::whereNull('parent_id')->orderBy('company_name')->get();

        $preselectedParentId = $request->query('parent_id');
        $preselectedParent = $preselectedParentId ? Company::find($preselectedParentId) : null;

        return view('admin.companys.create', compact('catalogs', 'hqs', 'preselectedParent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:companys,id',
            'company_code' => ['nullable', 'required_without:parent_id', 'unique:companys,company_code'],
            'branch_code' => ['nullable', 'required_with:parent_id', 'unique:companys,branch_code'],
            'catalog_id' => 'nullable|exists:catalogs,id',
            'company_reg_no' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
            'delivery_address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        // 1. Create the company and store it in a variable
        $company = Company::create($validated);

        // 2. Check if it is an HQ or a Branch based on parent_id
        $type = is_null($company->parent_id) ? 'HQ' : 'Branch';

        // 3. Redirect to index with the custom dynamic message
        return redirect()
            ->route('companys.index')
            ->with('success', "{$type} ({$company->company_name}) registered successfully.");
    }

    public function show(Company $company)
    {
        // Gate::authorize('view_business_entities');

        // Load the related catalog, parent (if branch), and children (if HQ) to display
        $company->load(['catalog', 'parent', 'children']);

        return view('admin.companys.show', compact('company'));
    }

    public function edit(Company $company)
    {
        $catalogs = Catalog::where('status', 'active')->get();

        return view('admin.companys.edit', compact('company', 'catalogs'));
    }

    public function update(Request $request, Company $company)
    {
        // 1. CAPTURE ORIGINAL STATUS FIRST: Remember it before we change anything
        $originalStatus = $company->status;

        // 2. Validate all fields
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'catalog_id' => 'nullable|exists:catalogs,id',
            'company_reg_no' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
            'delivery_address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        // 3. Update the company with the validated data
        $company->update($validated);

        // 4. CASCADE LOGIC: If an HQ was changed to inactive, turn off all its branches.
        // We check is_null(parent_id) to ensure it's an HQ, and compare the old status vs new status.
        if (is_null($company->parent_id) && $originalStatus !== 'inactive' && $company->status === 'inactive') {
            // This instantly updates all associated branches in the database
            $company->children()->update(['status' => 'inactive']);
        }

        return redirect()
            ->route('companys.index')
            ->with('success', "Business Entity ({$company->company_name}) updated successfully.");
    }

    public function destroy(Company $company)
    {
        // Gate::authorize('edit_business_entities');

        // 1. SAFEGUARD: Check if the company has associated orders
        if (!$company->canBeDeleted()) {
            return redirect()
                ->route('companys.index')
                ->with('error', "Cannot delete {$company->company_name} because it has existing order transactions tied to it.");
        }

        // 2. If safe, proceed with deletion
        $name = $company->company_name;
        $company->delete();

        return redirect()
            ->route('companys.index')
            ->with('success', "Business ({$name}) deleted successfully.");
    }
}
