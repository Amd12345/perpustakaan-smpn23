<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('action', 'like', '%'.$request->input('search').'%')
                        ->orWhere('description', 'like', '%'.$request->input('search').'%');
                });
            })
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->input('user_id')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('created_at', $request->input('date')))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('activity_logs.index', [
            'logs' => $logs,
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
