<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_type', 'user_id', 'action', 'entity_type', 'entity_id',
        'details', 'ip_address', 'user_agent', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public static function record(string $userType, ?int $userId, string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null): void
    {
        static::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
