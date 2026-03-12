<?php

namespace App\Services\Core;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AuditTrailService
{
    public function record(string $action, array $context = []): ?AuditLog
    {
        if (! Schema::hasTable('audit_logs')) {
            return null;
        }

        $request = request();
        $user = $request?->user();
        $auditable = $context['auditable'] ?? null;
        $createdBy = $context['created_by']
            ?? ($auditable instanceof Model ? ($auditable->created_by ?? null) : null)
            ?? ($user && method_exists($user, 'creatorId') ? $user->creatorId() : ($user->created_by ?? $user->id ?? 0));

        return AuditLog::create([
            'user_id' => $context['user_id'] ?? ($user->id ?? 0),
            'action' => $action,
            'route' => $context['route'] ?? $request?->route()?->getName(),
            'method' => $context['method'] ?? $request?->method() ?? 'SYSTEM',
            'ip_address' => $context['ip_address'] ?? $request?->ip(),
            'user_agent' => $context['user_agent'] ?? $request?->userAgent(),
            'auditable_type' => $context['auditable_type'] ?? ($auditable ? get_class($auditable) : null),
            'auditable_id' => $context['auditable_id'] ?? ($auditable->id ?? null),
            'old_values' => $context['old_values'] ?? null,
            'new_values' => $context['new_values'] ?? null,
            'created_by' => (int) $createdBy,
            'created_at' => $context['created_at'] ?? now(),
        ]);
    }
}
