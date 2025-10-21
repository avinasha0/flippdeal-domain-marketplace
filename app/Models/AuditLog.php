<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected.
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Log an action.
     */
    public static function log(
        string $action,
        $model = null,
        array $oldValues = null,
        array $newValues = null,
        string $description = null,
        User $user = null
    ): self {
        return static::create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description
        ]);
    }

    /**
     * Log a model creation.
     */
    public static function logCreate($model, User $user = null): self
    {
        return static::log(
            'create',
            $model,
            null,
            $model->getAttributes(),
            "Created {$model->getTable()}",
            $user
        );
    }

    /**
     * Log a model update.
     */
    public static function logUpdate($model, array $oldValues, array $newValues, User $user = null): self
    {
        return static::log(
            'update',
            $model,
            $oldValues,
            $newValues,
            "Updated {$model->getTable()}",
            $user
        );
    }

    /**
     * Log a model deletion.
     */
    public static function logDelete($model, User $user = null): self
    {
        return static::log(
            'delete',
            $model,
            $model->getAttributes(),
            null,
            "Deleted {$model->getTable()}",
            $user
        );
    }

    /**
     * Log a login action.
     */
    public static function logLogin(User $user): self
    {
        return static::log(
            'login',
            $user,
            null,
            null,
            "User logged in",
            $user
        );
    }

    /**
     * Log a logout action.
     */
    public static function logLogout(User $user): self
    {
        return static::log(
            'logout',
            $user,
            null,
            null,
            "User logged out",
            $user
        );
    }

    /**
     * Scope to get logs for a specific user.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope to get logs for a specific model.
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('model_type', get_class($model))
                    ->where('model_id', $model->id);
    }

    /**
     * Scope to get logs by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get logs within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}