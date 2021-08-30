<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';
    protected $dates = ['performed_at'];
    protected $guarded = ['id'];

    /**
     * RELATIONSHIPS
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * SCOPES
     */

    public function scopeMyTasks($query, $userId){
        return $query->where('user_id',$userId);
    }

    public function scopeMyTasksManagerWithTechnicianTasks($query,$userid){
        return $query->where('user_id',$userid)->orWhereIn('user_id',\App\Models\User::role('Technician')->pluck('id')->toArray());
    }
}
