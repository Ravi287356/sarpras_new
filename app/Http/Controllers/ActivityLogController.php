<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::query()
            ->with(['user.role'])
            ->orderByDesc('timesatamp')  // ✅ sesuai DB
            ->paginate(20);

        return view('pages.admin.activity_logs.index', [
            'title' => 'Activity Log',
            'logs'  => $logs,
        ]);
    }
}
