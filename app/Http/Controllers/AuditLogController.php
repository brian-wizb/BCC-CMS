<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search     = $request->input('search');
        $action     = $request->input('action');
        $dateFrom   = $request->input('date_from');
        $dateTo     = $request->input('date_to');

        $logs = AuditLog::query()
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('actor_username', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('entity_type', 'like', "%{$search}%")
                  ->orWhere('entity_id', 'like', "%{$search}%");
            }))
            ->when($action, fn ($q) => $q->where('action', 'like', "%{$action}%"))
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('created_at', '<=', $dateTo . ' 23:59:59'))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        // Distinct actions for filter dropdown
        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('audit-logs.index', compact('logs', 'actions', 'search', 'action', 'dateFrom', 'dateTo'));
    }
}
