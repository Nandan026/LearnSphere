<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    public function log(
        ?User $user,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $old = null,
        ?array $new = null,
    ): void {
        AuditLog::query()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}
