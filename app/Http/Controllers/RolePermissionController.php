<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolePermissionController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(): View
    {
        $roles       = Role::query()->with('permissions')->orderBy('id')->get();
        $permissions = Permission::query()->orderBy('key')->get();

        // Group permissions by module prefix (e.g. "members", "payroll", …)
        $grouped = $permissions->groupBy(fn ($p) => explode('.', $p->key)[0]);

        return view('roles.index', compact('roles', 'permissions', 'grouped'));
    }

    /**
     * Toggle a single permission on/off for a role (AJAX).
     */
    public function toggle(Request $request, Role $role): JsonResponse
    {
        abort_if($role->key === 'system_admin', 422, 'System Admin permissions cannot be modified.');

        $data = $request->validate([
            'permission_id' => ['required', 'integer', 'exists:permissions,id'],
            'grant'         => ['required', 'boolean'],
        ]);

        $permissionId = (int) $data['permission_id'];

        if ($data['grant']) {
            $role->permissions()->syncWithoutDetaching([$permissionId]);
        } else {
            $role->permissions()->detach($permissionId);
        }

        $permission = Permission::find($permissionId);

        $this->auditLogger->log(
            request: $request,
            action: $data['grant'] ? 'role.permission_granted' : 'role.permission_revoked',
            entityType: 'role',
            entityId: $role->id,
            after: ['role' => $role->key, 'permission' => $permission->key],
        );

        return response()->json(['ok' => true]);
    }
}
