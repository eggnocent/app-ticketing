<?php

namespace App\Modules\EventModule\Models;

use App\Models\Concerns\HasAudit;
use App\Modules\EventModule\Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasAudit, HasFactory, HasUuids;

    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'location',
        'start_time', 'end_time', 'status', 'banner_path',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    protected static function newFactory(): EventFactory
    {
        return EventFactory::new();
    }
}
