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

        $isFlatView = $request->filled('search') || $request->type === 'branch';

        $query = Company::with(['catalog', 'parent', 'children.catalog', 'children.parent']);

        if ($isFlatView) {
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
            'pic_phone' => 'nullable|string|max:20|regex:/^[0-9+\-]+$/',
            'delivery_address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        $company = Company::create($validated);
        $type = is_null($company->parent_id) ? 'HQ' : 'Branch';

        return redirect()
            ->route('companys.index')
            ->with('success', "{$type} ({$company->company_name}) registered successfully.");
    }

    public function show(Company $company)
    {
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
        $originalStatus = $company->status;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'catalog_id' => 'nullable|exists:catalogs,id',
            'company_reg_no' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20|regex:/^[0-9+\-]+$/',
            'delivery_address' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $company->update($validated);

        if (is_null($company->parent_id) && $originalStatus !== 'inactive' && $company->status === 'inactive') {
            $company->children()->update(['status' => 'inactive']);
        }

        return redirect()
            ->route('companys.index')
            ->with('success', "Business Entity ({$company->company_name}) updated successfully.");
    }

    public function destroy(Company $company)
    {
        if (!$company->canBeDeleted()) {
            return redirect()
                ->route('companys.index')
                ->with('error', "Cannot delete {$company->company_name} because it has existing order transactions tied to it.");
        }

        $name = $company->company_name;
        $company->delete();

        return redirect()
            ->route('companys.index')
            ->with('success', "Business ({$name}) deleted successfully.");
    }
}