<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class AuditLogger
{
    public function log(
        Request $request,
        string $action,
        string $entityType,
        string|int|null $entityId = null,
        mixed $before = null,
        mixed $after = null,
        ?User $user = null,
    ): void {
        try {
            $actor = $user ?? $request->user();

            AuditLog::query()->create([
                'user_id' => $actor?->id,
                'actor_username' => $actor?->username,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId !== null ? (string) $entityId : null,
                'before_json' => $this->normalizePayload($before),
                'after_json' => $this->normalizePayload($after),
                'ip_address' => $request->ip(),
                'route_name' => $request->route()?->getName(),
                'method' => $request->method(),
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Audit logging must never block the primary user action.
        }
    }

    private function normalizePayload(mixed $payload): mixed
    {
        if ($payload instanceof User) {
            return $payload->only(['id', 'username', 'full_name', 'email', 'status']);
        }

        return $payload;
    }
}
