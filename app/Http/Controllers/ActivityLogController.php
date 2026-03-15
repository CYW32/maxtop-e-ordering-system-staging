<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // 1. Filter by Date Range (using logic from your DateFilterable trait [1])
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // 2. Filter by Action Type (description column [2])
        if ($request->filled('action_type')) {
            $query->where('description', $request->action_type);
        }

        // 3. Dynamic Search: Name or Login ID (filtered through the 'causer' relationship)
        if ($request->filled('search')) {
            $term = $request->search;
            $query->whereHasMorph('causer', [User::class], function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('login_id', 'like', "%{$term}%");
            });
        }

        // 4. Filter by Role of the Causer
        if ($request->filled('role')) {
            $query->whereHasMorph('causer', [User::class], function ($q) use ($request) {
                $q->role($request->role);
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        // Fetch roles for the dropdown [3]
        $roles = Role::pluck('name', 'name');

        // Define common action types for the dropdown
        $actionTypes = ['created', 'updated', 'deleted', 'auth'];

        return view('admin.activity.index', compact('logs', 'roles', 'actionTypes'));
    }
}
