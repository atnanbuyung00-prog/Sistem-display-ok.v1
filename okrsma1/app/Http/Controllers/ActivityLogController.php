<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        // Filter: Action (INPUT, UBAH, HAPUS, etc.)
        if ($request->filled('action')) {
            $query->where('action', strtoupper(trim($request->query('action'))));
        }

        // Filter: Module
        if ($request->filled('module')) {
            $query->where('module', $request->query('module'));
        }

        // Filter: User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        // Filter: Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->query('date'));
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->query('start_date'))->startOfDay(),
                Carbon::parse($request->query('end_date'))->endOfDay(),
            ]);
        }

        // Filter: Search Keyword
        if ($request->filled('q')) {
            $q = trim((string)$request->query('q'));
            $query->where(function ($sq) use ($q) {
                $sq->where('description', 'like', "%{$q}%")
                   ->orWhere('patient_name', 'like', "%{$q}%")
                   ->orWhere('user_name', 'like', "%{$q}%")
                   ->orWhere('user_username', 'like', "%{$q}%")
                   ->orWhere('ip_address', 'like', "%{$q}%");
            });
        }

        // Stats for top cards
        $today = now('Asia/Jakarta')->toDateString();
        $stats = [
            'total'       => ActivityLog::count(),
            'today_total' => ActivityLog::whereDate('created_at', $today)->count(),
            'total_input' => ActivityLog::where('action', 'INPUT')->count(),
            'total_ubah'  => ActivityLog::where('action', 'UBAH')->count(),
            'total_hapus' => ActivityLog::where('action', 'HAPUS')->count(),
        ];

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100, 500, 1000], true)) {
            $perPage = 10;
        }

        $logs = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        $modules = ActivityLog::select('module')->distinct()->pluck('module');

        return view('admin.activity_logs.index', compact('logs', 'stats', 'modules', 'perPage'));
    }

    public function show(ActivityLog $activity_log)
    {
        return response()->json([
            'id'             => $activity_log->id,
            'user_name'      => $activity_log->user_name,
            'user_username'  => $activity_log->user_username,
            'action'         => $activity_log->action,
            'module'         => $activity_log->module,
            'patient_name'   => $activity_log->patient_name,
            'operation_date' => $activity_log->operation_date?->format('d-m-Y'),
            'description'    => $activity_log->description,
            'old_values'     => $activity_log->old_values,
            'new_values'     => $activity_log->new_values,
            'ip_address'     => $activity_log->ip_address,
            'user_agent'     => $activity_log->user_agent,
            'created_at'     => $activity_log->created_at->format('d/m/Y H:i:s'),
        ]);
    }
}
