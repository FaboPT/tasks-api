<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';
    protected $dates = ['performed_at'];
    protected $guarded = ['id'];

    /**
     * RELATIONSHIPS.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * SCOPES.
     *
     * @param Builder $query
     * @param int $userId
     */
    public function scopeMyTasks(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeMyTasksManagerWithTechnicianTasks(Builder $query, int $userid): Builder
    {
        return $query->where('user_id', $userid)->orWhereIn('user_id', User::role('Technician')->pluck('id')->toArray());
    }
}
