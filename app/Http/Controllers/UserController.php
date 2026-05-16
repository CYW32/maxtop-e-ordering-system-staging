<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_all_users') && !auth()->user()->can('view_my_users')) {
            abort(403, 'Unauthorized access to login credentials.');
        }

        $roles = Role::pluck('name', 'name');
        $status = ['active', 'deactive'];

        $query = User::with(['roles', 'company']);

        $canSwitchScope = auth()->user()->can('view_all_users');
        $currentScope = $request->get('scope', $canSwitchScope ? 'all' : 'assigned');

        if ($canSwitchScope) {
            if (auth()->user()->hasRole('cs_leader')) {
                $query->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['admin', 'cs_leader']));
            }
            
            if ($currentScope === 'assigned') {
                $query->where('assigned_cs_id', auth()->id());
            }
        } else {
            $currentScope = 'assigned';
            $query->where('assigned_cs_id', auth()->id());
        }

        $users = $query->search($request->search)
            ->filterByDate()
            ->filterByRole()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'roles', 'status', 'currentScope', 'canSwitchScope'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create_users');

        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'admin')->get();
        $companys = \App\Models\Company::orderBy('company_name')->get();
        $csStaffMembers = User::role(['admin', 'cs_leader', 'cs_staff'])->get();

        $parent = $request->has('parent_id') ? User::find($request->parent_id) : null;

        return view('users.create', compact('roles', 'csStaffMembers', 'companys', 'parent'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create_users');

        $request->validate([
            'name' => 'required|string|max:255',
            'login_id' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|exists:roles,name',
            'company_id' => 'required_if:role,customer|nullable|exists:companys,id',
            'assigned_cs_id' => 'nullable|exists:users,id',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'login_id' => $request->login_id,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'company_id' => $request->company_id,
                'assigned_cs_id' => $request->assigned_cs_id,
                'status' => 'active',
            ]);

            $user->assignRole($request->role);

            return redirect()
                ->route('users.index')
                ->with('success', "User ({$user->login_id}) has been created successfully.");
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), "Column 'company_id' cannot be null")) {
                return back()->withInput()->with('error', 'Action Failed: A Business Entity (Company) is strictly required to create this account.');
            }
            return back()->withInput()->with('error', 'Action Failed: An unexpected database error occurred while saving the user.');
        }
    }

    public function edit(User $user)
    {
        Gate::authorize('edit_users');

        if ($user->hasRole('admin') && auth()->id() !== $user->id) {
            abort(403, 'System Integrity Lock: Root Admin is immutable.');
        }

        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'admin')->get();
        $csStaffMembers = User::role(['admin', 'cs_leader', 'cs_staff'])->get();
        $companys = \App\Models\Company::orderBy('company_name')->get();

        $isAssignmentLocked = $user->orders()->exists();

        return view('users.edit', compact('user', 'roles', 'csStaffMembers', 'companys', 'isAssignmentLocked'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,deactive',
            'company_id' => 'required_if:role,customer|nullable|exists:companys,id',
            'assigned_cs_id' => 'nullable|exists:users,id',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $request->company_id,
        ];

        if (!$user->hasRole('admin')) {
            $userData['status'] = $request->status;

            if (auth()->user()->hasPermissionTo('reassign_customers')) {
                $userData['assigned_cs_id'] = $request->assigned_cs_id;
            }

            $user->syncRoles([$request->role]);
        }

        if ($request->filled('password')) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($userData);

        if (auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            return redirect()
                ->route('users.index')
                ->with('success', "User ({$user->login_id}) credentials updated successfully.");
        }

        return redirect()->route('users.index')->with('success', 'Customer login updated.');
    }

    public function destroy(User $user)
    {
        if (!$user->canBeDeleted()) {
            return redirect()
                ->route('users.index')
                ->with('error', "Cannot delete {$user->name} because they have existing order transactions.");
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "User ({$name}) deleted successfully.");
    }
}