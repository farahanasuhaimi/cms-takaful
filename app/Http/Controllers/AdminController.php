<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::withCount([
            'clients' => fn($q) => $q->withoutGlobalScopes(),
            'leads'   => fn($q) => $q->withoutGlobalScopes(),
        ])->orderBy('id')->get();

        return view('admin.index', compact('users'));
    }

    public function activity()
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(50);

        return view('admin.activity', compact('logs'));
    }

    public function toggleActive(User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Cannot deactivate an admin account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$user->name} has been {$status}.");
    }
}
