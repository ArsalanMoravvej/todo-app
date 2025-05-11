<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create(mixed $validated)
 */
class Todo extends Model
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStatus($query, $value)
    {
        return $query->where('status', $value);
    }

    public function scopeTitle($query, $value)
    {
        return $query->where('title', 'like', "%$value%");
    }

    public function scopePriority($query, $value)
    {
        return $query->where('priority', $value);
    }

    public function scopeDescription($query, $value)
    {
        return $query->where('description', 'like', "%$value%");
    }


}
